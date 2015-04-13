<?php
/*************************************************************************************************
* RF-Cloning SOAP Client
* This class is intended to assist in the creation of SOAP requests to http://www.rf-cloning.org.
* The WSDL file can be accessed at http://www.rf-cloning.com/classes/rf_cloning.wsdl
*
* Copyright (C) 2009-2014 Steve Bond
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License version 3 as published by
* the Free Software Foundation <http://www.gnu.org/licenses/>
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*************************************************************************************************/
class RF_Project
	{
	private $plasmid_seq, $insert_seq, $insert_sites, $client, $error;
	private $project_name = "RF-cloning project";
	private $PLAS_TARGET_TM = 60, $INS_TARGET_TM = 55, $PLAS_MIN_SIZE = 20, $INS_MIN_SIZE = 15, $PLAS_MAX_SIZE = 35, $INS_MAX_SIZE = 25;
	
	public function __construct($plasmid_seq, $insert_seq, $ins1, $ins2)
		{
		$this->plasmid_seq = $plasmid_seq;
		$this->insert_seq = $insert_seq;
		$this->insert_sites = $ins1."-".$ins2;
		
		try 
			{ 
			$this->client = @new SoapClient("http://www.rf-cloning.org/classes/rf_cloning.wsdl",array("exceptions" => 1)); 
			} 
		catch (SoapFault $E) 
			{ 
			$this->error = $E->faultstring;
			}
		}		
	
	public function runProject()
		{
		//The server returns a json_encoded string, which the json_decode() below converts to an object, which get_object_vars() converts to an array. I like working with arrays, but by all means, use one of the other formats if you prefer.  
		//You can get a printout of the array keys with print_r(array_keys(array $input));
		return get_object_vars(json_decode($this->client->getPrimers($this->plasmid_seq,$this->insert_seq,$this->insert_sites,$this->PLAS_TARGET_TM,$this->INS_TARGET_TM,$this->PLAS_MIN_SIZE,$this->INS_MIN_SIZE,$this->PLAS_MAX_SIZE,$this->INS_MAX_SIZE)));	
		}
	
	public function formatOutput()
		{
		$project_run = $this->runProject();	
		$fwd_primer = explode("|",$project_run['fwd_primer_database']);
		$fwd_primer = isset($fwd_primer[2]) ? "<span style='background-color:#09F;font-family:Courier New;'>".$fwd_primer[0]."</span><span style='background-color:#6F3;font-family:Courier New;'>".$fwd_primer[1]."</span><span style='background-color:#09F;font-family:Courier New;'>".$fwd_primer[2]."</span>" :"<span style='background-color:#09F;font-family:Courier New;'>".$fwd_primer[0]."</span><span style='background-color:#6F3;font-family:Courier New;'>".$fwd_primer[1]."</span>";
		
		$rev_primer = explode("|",$project_run['rev_primer_database']);
		$rev_primer = isset($rev_primer[2]) ? "<span style='background-color:#09F;font-family:Courier New;'>".$rev_primer[0]."</span><span style='background-color:#6F3;font-family:Courier New;'>".$rev_primer[1]."</span><span style='background-color:#09F;font-family:Courier New;'>".$rev_primer[2]."</span>" :"<span style='background-color:#09F;font-family:Courier New;'>".$rev_primer[0]."</span><span style='background-color:#6F3;font-family:Courier New;'>".$rev_primer[1]."</span>";
		
		$output = "	<h3>".$this->project_name."</h3>
					<em>Forward Primer</em><br />
					".$fwd_primer."<br /><br />
					<em>Reverse Primer</em><br />
					".$rev_primer."
					<table cellpadding=4>
					  <tr>
							<td><u>1&deg; PCR  Size</u></td>
							<td><u>New Plasmid Size</u></td>
							<td><u>Insert Sites</u></td>
							<td><u>Insert Size</u></td>
					  </tr>
						<tr>
							<td>".$project_run['target_pcr_size']."bps</td>
							<td>".strlen($project_run['new_construct'])."bps</td>
							<td>".$this->insert_sites."</td>
							<td>".strlen($this->insert_seq)."bps</td>
						</tr>
					</table>				
					<br />
					<table>
						<tr>
							<td colspan='2'><b>2&deg; PCR conditions</b></td>
						</tr>
						<tr>
							<td><u>Extension Time</u></td>
							<td><u>ng of insert</u></td>
							<td><u>ng of plasmid</u></td>
						</tr>
						<tr>
							<td>".$project_run['extension_time_mins']." mins</td>
							<td>".round($project_run['ng_of_insert'],1)."</td>
							<td>".round($project_run['ng_of_plasmid'],1)."</td>
						</tr>
					</table>";
		
		return $output;
		}
	
	public function getParameters()  
		{
		$params = array('plasmid_seq' => $this->plasmid_seq, 'insert_seq' => $this->insert_seq, 'insert_sites' => $this->insert_sites, 'plas_target_tm' => $this->PLAS_TARGET_TM, 'ins_target_tm' => $this->INS_TARGET_TM, 'plas_min_size' => $this->PLAS_MIN_SIZE, 'ins_min_size' => $this->INS_MIN_SIZE, 'plas_max_size' => $this->PLAS_MAX_SIZE, 'ins_max_size' => $this->INS_MAX_SIZE, 'project_name' => $this->project_name);
		return $params;	
		}
	
	public function getError()
		{
		return $this->error;	
		}
		
	public function setPlasSeq($sequence)
		{
		$this->plasmid_seq = $sequence;	
		}
	
	public function setInsSeq($sequence)
		{
		$this->insert_seq = $sequence;	
		}
	
	public function setInsSites($ins1, $ins2)
		{
		$this->insert_sites = $ins1."-".$ins2;	
		}
	
	public function setMaxPlasSize($size)
		{
		$this->PLAS_MAX_SIZE = $size;	
		}
	
	public function setMaxInsSize($size)
		{
		$this->INS_MAX_SIZE = $size;	
		}
	
	public function setMinPlasSize($size)
		{
		$this->PLAS_MIN_SIZE = $size;	
		}
	
	public function setMinInsSize($size)
		{
		$this->INS_MIN_SIZE = $size;	
		}
		
	public function setPlasTm($tm)
		{
		$this->PLAS_TARGET_TM = $tm;	
		}
		
	public function setInsTm($tm)
		{
		$this->INS_TARGET_TM = $tm;	
		}
	
	public function setName($name)
		{
		$this->project_name = $name;	
		}
	}

// turn off the WSDL cache 
ini_set("soap.wsdl_cache_enabled", "0");

//The following is a simple example of the class in action. PLEASE NOTE! Most installations of php do not come with the soap extension turned on by default. You need to activate the extension on your server before this program will work.
//first off, instantiate a new object => RF_Project(string $plasmid_seq, string $insert_seq, int $first_insert_site, int $second_insert_site) 
$plasmid_seq = "CACCTAAATTGTAAGCGTTAATATTTTGTTAAAATTCGCGTTAAATTTTTGTTAAATCAGCTCATTTTTTAACCAATAGGCCGAAATCGGCAAAATCCCTTATAAATCAAAAGAATAGACCGAGATAGGGTTGAGTGTTGTTCCAGTTTGGAACAAGAGTCCACTATTAAAGAACGTGGACTCCAACGTCAAAGGGCGAAAAACCGTCTATCAGGGCGATGGCCCACTACGTGAACCATCACCCTAATCAAGTTTTTTGGGGTCGAGGTGCCGTAAAGCACTAAATCGGAACCCTAAAGGGAGCCCCCGATTTAGAGCTTGACGGGGAAAGCCGGCGAACGTGGCGAGAAAGGAAGGGAAGAAAGCGAAAGGAGCGGGCGCTAGGGCGCTGGCAAGTGTAGCGGTCACGCTGCGCGTAACCACCACACCCGCCGCGCTTAATGCGCCGCTACAGGGCGCGTCCCATTCGCCATTCAGGCTGCGCAACTGTTGGGAAGGGCGATCGGTGCGGGCCTCTTCGCTATTACGCCAGCTGGCGAAAGGGGGATGTGCTGCAAGGCGATTAAGTTGGGTAACGCCAGGGTTTTCCCAGTCACGACGTTGTAAAACGACGGCCAGTGAATTGTAATACGACTCACTATAGGGCGAATTGGGTACCGGGCCCCCCCTCGAGGTCGACGGTATCGATAAGCTTGATATCGAATTCCTGCAGCCCGGGGGATCCACTAGTTCTAGAGCGGCCGCCACCGCGGTGGAGCTCCAGCTTTTGTTCCCTTTAGTGAGGGTTAATTTCGAGCTTGGCGTAATCATGGTCATAGCTGTTTCCTGTGTGAAATTGTTATCCGCTCACAATTCCACACAACATACGAGCCGGAAGCATAAAGTGTAAAGCCTGGGGTGCCTAATGAGTGAGCTAACTCACATTAATTGCGTTGCGCTCACTGCCCGCTTTCCAGTCGGGAAACCTGTCGTGCCAGCTGCATTAATGAATCGGCCAACGCGCGGGGAGAGGCGGTTTGCGTATTGGGCGCTCTTCCGCTTCCTCGCTCACTGACTCGCTGCGCTCGGTCGTTCGGCTGCGGCGAGCGGTATCAGCTCACTCAAAGGCGGTAATACGGTTATCCACAGAATCAGGGGATAACGCAGGAAAGAACATGTGAGCAAAAGGCCAGCAAAAGGCCAGGAACCGTAAAAAGGCCGCGTTGCTGGCGTTTTTCCATAGGCTCCGCCCCCCTGACGAGCATCACAAAAATCGACGCTCAAGTCAGAGGTGGCGAAACCCGACAGGACTATAAAGATACCAGGCGTTTCCCCCTGGAAGCTCCCTCGTGCGCTCTCCTGTTCCGACCCTGCCGCTTACCGGATACCTGTCCGCCTTTCTCCCTTCGGGAAGCGTGGCGCTTTCTCATAGCTCACGCTGTAGGTATCTCAGTTCGGTGTAGGTCGTTCGCTCCAAGCTGGGCTGTGTGCACGAACCCCCCGTTCAGCCCGACCGCTGCGCCTTATCCGGTAACTATCGTCTTGAGTCCAACCCGGTAAGACACGACTTATCGCCACTGGCAGCAGCCACTGGTAACAGGATTAGCAGAGCGAGGTATGTAGGCGGTGCTACAGAGTTCTTGAAGTGGTGGCCTAACTACGGCTACACTAGAAGGACAGTATTTGGTATCTGCGCTCTGCTGAAGCCAGTTACCTTCGGAAAAAGAGTTGGTAGCTCTTGATCCGGCAAACAAACCACCGCTGGTAGCGGTGGTTTTTTTGTTTGCAAGCAGCAGATTACGCGCAGAAAAAAAGGATCTCAAGAAGATCCTTTGATCTTTTCTACGGGGTCTGACGCTCAGTGGAACGAAAACTCACGTTAAGGGATTTTGGTCATGAGATTATCAAAAAGGATCTTCACCTAGATCCTTTTAAATTAAAAATGAAGTTTTAAATCAATCTAAAGTATATATGAGTAAACTTGGTCTGACAGTTACCAATGCTTAATCAGTGAGGCACCTATCTCAGCGATCTGTCTATTTCGTTCATCCATAGTTGCCTGACTCCCCGTCGTGTAGATAACTACGATACGGGAGGGCTTACCATCTGGCCCCAGTGCTGCAATGATACCGCGAGACCCACGCTCACCGGCTCCAGATTTATCAGCAATAAACCAGCCAGCCGGAAGGGCCGAGCGCAGAAGTGGTCCTGCAACTTTATCCGCCTCCATCCAGTCTATTAATTGTTGCCGGGAAGCTAGAGTAAGTAGTTCGCCAGTTAATAGTTTGCGCAACGTTGTTGCCATTGCTACAGGCATCGTGGTGTCACGCTCGTCGTTTGGTATGGCTTCATTCAGCTCCGGTTCCCAACGATCAAGGCGAGTTACATGATCCCCCATGTTGTGCAAAAAAGCGGTTAGCTCCTTCGGTCCTCCGATCGTTGTCAGAAGTAAGTTGGCCGCAGTGTTATCACTCATGGTTATGGCAGCACTGCATAATTCTCTTACTGTCATGCCATCCGTAAGATGCTTTTCTGTGACTGGTGAGTACTCAACCAAGTCATTCTGAGAATAGTGTATGCGGCGACCGAGTTGCTCTTGCCCGGCGTCAATACGGGATAATACCGCGCCACATAGCAGAACTTTAAAAGTGCTCATCATTGGAAAACGTTCTTCGGGGCGAAAACTCTCAAGGATCTTACCGCTGTTGAGATCCAGTTCGATGTAACCCACTCGTGCACCCAACTGATCTTCAGCATCTTTTACTTTCACCAGCGTTTCTGGGTGAGCAAAAACAGGAAGGCAAAATGCCGCAAAAAAGGGAATAAGGGCGACACGGAAATGTTGAATACTCATACTCTTCCTTTTTCAATATTATTGAAGCATTTATCAGGGTTATTGTCTCATGAGCGGATACATATTTGAATGTATTTAGAAAAATAAACAAATAGGGGTTCCGCGCACATTTCCCCGAAAAGTGC";
$insert_seq = "CTACGGCGTG";

$primers = new RF_Project($plasmid_seq,$insert_seq,60,70);

//Printout of the private variables currently stored in the object
print_r($primers->getParameters());
echo "<br /><br />";

//Then fire the xml request off to the SOAP server by calling the runProject method. 
$output = $primers->runProject();

//Here's a printout of the array you get back form the server...
print_r($output);

//There are a number of setter and getter methods in the class that I'm sure you can figure out on your own, but the formatOutput() method is worth taking a look at if you want a quick printout of your project.  
$output = $primers->formatOutput();
echo "<br /><br />".$output;
?>