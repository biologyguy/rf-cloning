#!/usr/bin/env python3
# -*- coding: utf-8 -*-
# Created on: Apr 14 2015 

"""
DESCRIPTION OF PROGRAM
"""


import argparse
import json
import sys
sys.path.insert(0, "./suds")
from suds.client import Client
from SeqBuddy import SeqBuddy


class RFcloning():
    def __init__(self, plasmid_seq=str, insert_seq=str, insert_sites=list, plas_target_tm=int, ins_target_tm=int,
                 plas_min_size=int, ins_min_size=int, plas_max_size=int, ins_max_size=int, plas_name='pPlasmid',
                 ins_name='Insert'):
        wsdl_url = "http://www.rf-cloning.org/classes/rf_cloning.wsdl"
        self.client = Client(url=wsdl_url)
        self.plasmid_seq = plasmid_seq
        self.insert_seq = insert_seq
        self.insert_sites = insert_sites
        self.plas_target_tm = plas_target_tm
        self.ins_target_tm = ins_target_tm
        self.plas_min_size = plas_min_size
        self.ins_min_size = ins_min_size
        self.plas_max_size = plas_max_size
        self.ins_max_size = ins_max_size
        self.plas_name = plas_name
        self.ins_name = ins_name

        self.result = dict

    def run_soap(self):
        result = self.client.service.getPrimers(plasmid_seq=self.plasmid_seq, insert_seq=self.insert_seq,
                                                insert_sites=self.insert_sites,
                                                plas_target_tm=self.plas_target_tm, ins_target_tm=self.ins_target_tm,
                                                plas_min_size=self.plas_min_size, ins_min_size=self.ins_min_size,
                                                plas_max_size=self.plas_max_size, ins_max_size=self.ins_max_size)

        self.result = json.loads(result)

        # Returned keys: new_construct, fwd_primer_database, rev_primer_database, fwd_plas_tm, fwd_ins_tm,
        # rev_plas_tm, rev_ins_tm, ng_of_plasmid, pmol_of_plasmid, ng_of_insert, pmol_of_insert, extension_time_mins,
        # extension_time_secs, target_pcr_size, error

        return

    def write_report(self):
        output = "######### %s-%s #########\n\n" % (self.plas_name, self.ins_name)
        output += "### Forward Primer ###\n"
        output += "Plasmid annealing\t%s°C\n" % round(self.result["fwd_plas_tm"], 1)
        output += "Target annealing\t%s°C\n" % round(self.result["fwd_ins_tm"], 1)
        output += "%s\n\n" % "".join(self.result["fwd_primer_database"].split("|"))

        output += "### Reverse Primer ###\n"
        output += "Plasmid annealing\t%s°C\n" % round(self.result["rev_plas_tm"], 1)
        output += "Target annealing\t%s°C\n" % round(self.result["rev_ins_tm"], 1)
        output += "%s\n\n" % "".join(self.result["rev_primer_database"].split("|"))

        output += "### 2° PCR ###\n"
        output += "Extension time\t%s min\n" % self.result["extension_time_mins"]
        output += "Insert:\t%s ng\t%s pmol\n" % (round(self.result["ng_of_insert"], 1), self.result["pmol_of_insert"])
        output += "Plasmid:\t%s ng\t%s pmol\n\n" % (round(self.result["ng_of_plasmid"], 1), self.result["pmol_of_plasmid"])

        output += "### New construct ###\n"
        output += "Insert sites\t%s\n" % self.insert_sites
        output += "1° PCR size\t%s\n" % self.result["target_pcr_size"]
        output += "New plasmid size\t%s\n" % len(self.result["new_construct"])
        output += "New plasmid sequence\n%s\n\n" % self.result["new_construct"]

        return output

if __name__ == '__main__':

    parser = argparse.ArgumentParser(prog="soap_client", description="Batch run jobs through the rf-cloning server.",
                                     formatter_class=argparse.ArgumentDefaultsHelpFormatter)

    parser.add_argument("plasmid_seq", action="store",
                        help="Single sequence in fasta, gb, or plain text format. If multiple plasmids are in the file,"
                             " only the top most will be used.")
    parser.add_argument("insert_seqs", action="store",
                        help="Multiple sequences in fasta or gb format. A project will be generated for each.")
    parser.add_argument("insert_sites", action="store", type=str,
                        help="The exact insert location start-end. E.g., '20-73', or '149-149'")
    # parser.add_argument("-c", "--choice", help="", type=str, choices=["", ""], default=False)
    # parser.add_argument("-m", "--multi_arg", nargs="+", help="", default=[])

    in_args = parser.parse_args()

    plasmid = SeqBuddy(in_args.plasmid_seq)
    inserts = SeqBuddy(in_args.insert_seqs)
    insert_sites = in_args.insert_sites

    rf_cloning_client = RFcloning(plasmid_seq=str(plasmid.records[0].seq), insert_sites=insert_sites)

    rf_cloning_client.plas_target_tm = 60
    rf_cloning_client.ins_target_tm = 55
    rf_cloning_client.plas_min_size = 20
    rf_cloning_client.ins_min_size = 15
    rf_cloning_client.plas_max_size = 35
    rf_cloning_client.ins_max_size = 25
    rf_cloning_client.plas_name = plasmid.records[0].id

    for rec in inserts.records:
        rf_cloning_client.ins_name = rec.id
        rf_cloning_client.insert_seq = str(rec.seq)
        rf_cloning_client.run_soap()
        print(rf_cloning_client.write_report())