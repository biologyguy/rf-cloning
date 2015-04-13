<?php
/*************************************************************************************************#
# www.rf-cloning.org
#
# Copyright (C) 2009-2014 Steve R. Bond <biologyguy@gmail.com>
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License version 3 as published by
# the Free Software Foundation <http://www.gnu.org/licenses/>
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#*************************************************************************************************/	
class Plasmid
	{
	private $plasmid_id, $plasmid_name, $user_id, $plasmid_seq, $savvy_markers, $savvy_enzymes, $popularity, $privacy;
	private $insert_name, $backbone_name, $backbone_database, $backbone_id, $orig_plasmid_seq, $insert_seq, $insert_sites, $fwd_primer, $rev_primer, $notes, $savvy_meta, $complete, $proj_hash;
	private $checksum;
	//this is the database being accessed, either 'plasmids' or 'projects'
	private $database;
	private $error;
	
	//Used to make a new barebones plasmid object   
	public function __construct()
		{
		
		}	
	
	//Used to get a plasmid from the database
	public function get_database_plasmid($plasmid_id, $database)	
		{
		$this->plasmid_id = $plasmid_id;
		$this->database = $database;
		//connect to the database
		try
			{
			$plasmid_info_query = mysql_query("SELECT * FROM ".$this->database." WHERE plasmid_id = ".$this->plasmid_id.";"); 
			if (mysql_error())
				{
				throw new Exception(mysql_error());
				}
			
			$plasmid_info_array = mysql_fetch_assoc($plasmid_info_query);
			}
		
		catch (Exception $e)
			{
			$this->error .= "Error @ get_database_plasmid(): ".$e->getMessage()."<br />";	
			return FALSE;
			}	
		
		if($this->database == "projects" && $plasmid_info_array['backbone_database'] != "blank")
			{
			try
				{
				$plasmid_info = mysql_fetch_row(mysql_query("SELECT plasmid_name FROM ".$plasmid_info_array['backbone_database']." WHERE plasmid_id = ".$plasmid_info_array['backbone_id'].";")); 
				if (mysql_error())
					{
					throw new Exception(mysql_error());
					}
				
				$plasmid_info_array['backbone_name'] = $plasmid_info[0];
				}
			
			catch (Exception $e)
				{
				$this->error .= "Error @ get_database_plasmid(): ".$e->getMessage()."<br />";	
				return FALSE;
				}
			}
		
		else
			{
			$plasmid_info_array['backbone_name'] = "blank";	
			}
		
		$this->checksum = md5($plasmid_info_array["sequence"]);
		return $plasmid_info_array; 
		}
	
	public function set_database_plasmid($plasmid_id, $database)
		{
		$plasmid_info_array = $this->get_database_plasmid($plasmid_id,$database);
		
		$params_array = array( "plasmid_name" => $plasmid_info_array["plasmid_name"], "user_id" => $plasmid_info_array["user_id"], "plasmid_seq" => $plasmid_info_array["sequence"], "savvy_markers" => $plasmid_info_array["savvy_markers"], "savvy_enzymes" => $plasmid_info_array["savvy_enzymes"]);		
		
		if ($database == "projects")
			{
			$params_array["database"] = "projects";
			$params_array["backbone_name"] = $plasmid_info_array["backbone_name"];
			$params_array["insert_name"] = $plasmid_info_array["insert_name"];
			$params_array["backbone_database"] = $plasmid_info_array["backbone_database"];
			$params_array["backbone_id"] = $plasmid_info_array["backbone_id"];
			$params_array["orig_plasmid_seq"] = $plasmid_info_array["plasmid_sequence"];
			$params_array["insert_seq"] = $plasmid_info_array["insert_sequence"];
			$params_array["insert_sites"] = $plasmid_info_array["insert_sites"];
			$params_array["fwd_primer"] = $plasmid_info_array["fwd_primer"];
			$params_array["rev_primer"] = $plasmid_info_array["rev_primer"];
			$params_array["notes"] = $plasmid_info_array["notes"];
			$params_array["savvy_meta"] = $plasmid_info_array["savvy_meta"];
			$params_array["complete"] = $plasmid_info_array["complete"];
			$params_array["privacy"] = 0;
			$params_array["proj_hash"] = $plasmid_info_array["proj_hash"];
			}
		
		else
			{
			$params_array["database"] = "plasmids";
			$params_array["popularity"] = $plasmid_info_array["popularity"];
			$params_array["privacy"] = $plasmid_info_array["privacy"];	
			}
					
		return $this->set_parameters($params_array);	
		}
	
	public function get_parameters($param)
		{
		switch ($param)
				{
				case "plasmid_id":
					return $this->plasmid_id;
					break;
				
				case "plasmid_seq":
					return $this->plasmid_seq;
					break;
				
				case "orig_plasmid_seq":
					return $this->orig_plasmid_seq;
					break;
				
				case "database":
					return $this->database;
					break;
				
				case "backbone_database":
					return $this->backbone_database;
					break;
				
				case "backbone_id":
					return $this->backbone_id;
					break;
				
				case "backbone_name":
					return $this->backbone_name;
					break;
					
				case "plasmid_name":
					return $this->plasmid_name;
					break;
				
				case "insert_name":
					return $this->insert_name;
					break;
				
				case "plasmid_size":
					return strlen($this->plasmid_seq);
					break;
					
				case "savvy_markers":
					return $this->savvy_markers;
					break;
				
				case "savvy_enzymes":
					return $this->savvy_enzymes;
					break;
				
				case "notes":
					return $this->notes;
					break;
				
				case "complete":
					return $this->complete;
					break;
				
				case "savvy_meta":
					return $this->savvy_meta;
					break;
				
				case "fwd_primer":
					return $this->fwd_primer;
					break;
				
				case "rev_primer":
					return $this->rev_primer;
					break;
				
				case "insert_seq":
					return $this->insert_seq;
					break;
				
				case "insert_sites":
					return $this->insert_sites;
					break;
				
				case "popularity":
					return $this->popularity;
					break;
				
				case "privacy":
					return $this->privacy;
					break;
				
				case "user_id":
					return $this->user_id;
					break;
				
				case "error":
					return $this->error;
					break;
				
				case "proj_hash":
					return $this->proj_hash;
					break;
					
				default:
					return FALSE;
					$this->error .= "Error @ get_parameters(): ".$param." is not recognized.<br />";
					break;	
				}
		}
	
	public function set_parameters($params_array)
		{
		$no_errors = TRUE;
		$counter = 1;
		while (($next = current($params_array)) || ($counter <= count((array)$params_array)))
			{
			$counter++;
			
			switch (key($params_array))
				{
				case "plasmid_id":
					$this->plasmid_id = $next;
					break;
					
				case "plasmid_seq":
					$this->plasmid_seq = $this->clean_sequence($next);
					break;
				
				case "insert_seq":
					$this->insert_seq = $this->clean_sequence($next);
					break;
				
				case "orig_plasmid_seq":
					$this->orig_plasmid_seq = $this->clean_sequence($next);
					break;
				
				case "insert_name":
					$this->insert_name = $next;
					break;
											
				case "user_id":
					$this->user_id = $next;
					break;
					
				case "database":
					$this->database = $next;
					break;
				
				case "backbone_database":
					$this->backbone_database = $next;
					break;
				
				case "backbone_id":
					$this->backbone_id = $next;
					break;
				
				case "backbone_name":
					$this->backbone_name = $next;
					break;
				
				case "plasmid_name":
					$this->plasmid_name = $next;
					break;
				
				case "savvy_markers":
					$this->savvy_markers = $next;
					break;
				
				case "savvy_enzymes":
					$this->savvy_enzymes = $next;
					break;
				
				case "notes":
					$this->notes = $next;
					break;
				
				case "complete":
					$this->complete = $next;
					break;
				
				case "savvy_meta":
					$this->savvy_meta = $next;
					break;
				
				case "fwd_primer":
					$this->fwd_primer = $next;
					break;
				
				case "rev_primer":
					$this->rev_primer = $next;
					break;
				
				case "insert_sites":
					$this->insert_sites = $next;
					break;
				
				case "popularity":
					$this->popularity = $next;
					break;
				
				case "privacy":
					$this->privacy = $next;
					break;
				
				case "proj_hash":
					$this->proj_hash = $next;
					break;
					
				default:
					$this->error .= "Error @ set_parameters(): ".key($params_array)." is not a valid variable name.<br />";
					$no_errors = FALSE;
					break;	
				}
			next($params_array);
			}
		return $no_errors;
		}
		
	
	public function clear_error()
		{
		$this->error = "";	
		}
		
	private function clean_sequence($sequence)
		{
		//Tidy up a sequence, and make it safe
		//clear FASTA
		$sequence = preg_replace("/>.+/i","",$sequence);
			
		//get rid of anything that isn't a letter
		$sequence = preg_replace("/[^A-Za-z]/i","",$sequence);
		
		//convert any non-standard nucleotides into 'X'
		$sequence = preg_replace("/[^ATGCatgc]/i","X",$sequence);
		
		//convert to upper case
		$sequence = strtoupper($sequence);
		
		return $sequence;
		}
	
	public function checksum()
		{
		$check = ($this->checksum == md5($this->plasmid_seq)) ? TRUE : FALSE; 
		return $check;	
		}
	   	
	public function save()
		{
		if (empty($this->plasmid_seq))
			{
			$this->error .= "Error @ save(): Value for plasmid_seq has not been set for this plasmid object<br />";
			return FALSE;	
			}
		
		if (empty($this->plasmid_name))
			{
			$this->error .= "Error @ save(): Value for plasmid_name has not been set for this plasmid object<br />";
			return FALSE;	
			}
		
		if (empty($this->user_id))
			{
			$this->error .= "Error @ save(): Value for user_id has not been set for this plasmid object<br />";
			return FALSE;	
			}
		
		if (empty($this->database))
			{
			$this->error .= "Error @ save(): Value for database has not been set for this plasmid object<br />";
			return FALSE;	
			}
		
		if ($this->database == "plasmids")
			{
			if (!empty($this->plasmid_id) && $this->plasmid_id != "new")
				{
				$save_outcome = $this->update_backbone();	
				}
			
			else
				{
				$save_outcome = $this->new_backbone();	
				}
			}
		
		elseif($this->database == "projects")
			{
			if (!empty($this->plasmid_id) && $this->plasmid_id != "new")
				{
				$save_outcome = $this->update_project();	
				}
			
			else
				{
				$save_outcome = $this->new_project();	
				}	
			}
			
		else
			{
			$this->error .= "Error @ save(): Specify database as 'plasmids' or 'projects' only<br />";
			return FALSE;	
			}
		
		return $save_outcome;
		}
				
	private function update_backbone()
		{
		$params_array = array("plasmid_name" => $this->plasmid_name, "sequence" => $this->plasmid_seq, "plasmid_size" => strlen($this->plasmid_seq), "savvy_markers" => $this->savvy_markers, "savvy_enzymes" => $this->savvy_enzymes, "popularity" => $this->popularity, "privacy" => $this->privacy);
		
		$mysql_query = "UPDATE plasmids SET ";  
		$counter = 1;
		while(($next = current($params_array)) || ($counter <= count($params_array)))
			{
			if(is_numeric($next))
				{
				$mysql_query .= key($params_array)."=".$next.", ";		
				}
			elseif(isset($next))
				{
				$mysql_query .= key($params_array)."='".$next."', ";	
				}
			next($params_array);
			$counter++;
			}
		$mysql_query = substr($mysql_query,0,-2);
		$mysql_query .= " WHERE plasmid_id = ".$this->plasmid_id.";";
		
		$save_outcome = $this->mysqlQuery($mysql_query,"update_backbone()");
		return $save_outcome;
		}
		
	private function new_backbone()
		{
		$params_array = array("user_id" => $this->user_id, "plasmid_name" => $this->plasmid_name, "sequence" => $this->plasmid_seq, "plasmid_size" => strlen($this->plasmid_seq), "savvy_markers" => $this->savvy_markers, "savvy_enzymes" => $this->savvy_enzymes, "popularity" => $this->popularity, "privacy" => $this->privacy, "checksum" => md5($this->plasmid_seq));
		
		$mysql_query = "INSERT INTO plasmids SET ";  
		$counter = 1;
		while(($next = current($params_array)) || ($counter <= count($params_array)))
			{
			if(is_numeric($next))
				{
				$mysql_query .= key($params_array)."=".$next.", ";		
				}
			elseif(isset($next))
				{
				$mysql_query .=key($params_array)."='".$next."', ";	
				}
			next($params_array);
			$counter++;
			}
		$mysql_query = substr($mysql_query,0,-2);
		$mysql_query .= ";";
		
		$save_outcome = $this->mysqlQuery($mysql_query,"new_backbone()");
		return $save_outcome;
		}
	
	private function new_project()
		{
		$params_array = array("user_id" => $this->user_id, "plasmid_name" => $this->plasmid_name, "insert_name" => $this->insert_name, "backbone_id" => $this->backbone_id, "backbone_database" => $this->backbone_database, "sequence" => $this->plasmid_seq, "plasmid_sequence" => $this->orig_plasmid_seq, "insert_sequence" => $this->insert_seq, "insert_sites" => $this->insert_sites, "fwd_primer" => $this->fwd_primer, "rev_primer" => $this->rev_primer, "new_size" => strlen($this->plasmid_seq), "notes" => $this->notes, "savvy_markers" => $this->savvy_markers, "savvy_enzymes" => $this->savvy_enzymes, "savvy_meta" => $this->savvy_meta, "complete" => $this->complete, "checksum" => md5($this->plasmid_seq), "proj_hash" => $this->proj_hash);
		
		$mysql_query = "INSERT INTO projects SET ";  
		$counter = 1;
		while(($next = current($params_array)) || ($counter <= count($params_array)))
			{
			if(is_numeric($next))
				{
				$mysql_query .= key($params_array)."=".$next.", ";		
				}
			elseif(isset($next))
				{
				$mysql_query .=key($params_array)."='".$next."', ";	
				}
			next($params_array);
			$counter++;
			}
		$mysql_query = substr($mysql_query,0,-2);
		$mysql_query .= ";";
		
		$save_outcome = $this->mysqlQuery($mysql_query, "new_project()");
		
		if($save_outcome && $this->backbone_id > 0 && $this->backbone_database == "plasmids")
			{
			$mysql_query = "UPDATE plasmids	SET popularity = popularity+1 WHERE plasmid_id = ".$this->backbone_id.";";
			$this->mysqlQuery($mysql_query, "new_project() -> popularity");
			}
		
		return $save_outcome;
		}
		
	private function update_project()
		{
		$params_array = array("user_id" => $this->user_id, "plasmid_name" => $this->plasmid_name, "insert_name" => $this->insert_name, "backbone_id" => $this->backbone_id, "backbone_database" => $this->backbone_database, "sequence" => $this->plasmid_seq, "plasmid_sequence" => $this->orig_plasmid_seq, "insert_sequence" => $this->insert_seq, "insert_sites" => $this->insert_sites, "fwd_primer" => $this->fwd_primer, "rev_primer" => $this->rev_primer, "new_size" => strlen($this->plasmid_seq), "notes" => $this->notes, "savvy_markers" => $this->savvy_markers, "savvy_enzymes" => $this->savvy_enzymes, "savvy_meta" => $this->savvy_meta, "complete" => $this->complete, "checksum" => md5($this->plasmid_seq), "proj_hash" => $this->proj_hash);
		
		$mysql_query = "UPDATE projects SET ";  
		$counter = 1;
		while(($next = current($params_array)) || ($counter <= count($params_array)))
			{
			if(is_numeric($next))
				{
				$mysql_query .= key($params_array)."=".$next.", ";		
				}
			elseif(isset($next))
				{
				$mysql_query .= key($params_array)."='".$next."', ";	
				}
			next($params_array);
			$counter++;
			}
		$mysql_query = substr($mysql_query,0,-2);
		$mysql_query .= " WHERE plasmid_id = ".$this->plasmid_id.";";
		
		$save_outcome = $this->mysqlQuery($mysql_query, "update_projects()");
		
		return $save_outcome;
		}
	
	private function mysqlQuery($query_string, $origination)
		{
		try
			{
			mysql_query($query_string); 
			if (mysql_error())
				{
				throw new Exception(mysql_error());
				}
			}
		
		catch (Exception $e)
			{
			$this->error .= "Error @ ".$origination.": ".$e->getMessage()."<br />".$query_string."<br />";	
			return FALSE;
			}
		if (mysql_insert_id() > 0)
			{
			return mysql_insert_id();	
			}
			
		else
			{
			return TRUE;	
			}
		}

	
	public function build_construct()
		{	
			
		//get all the sequences sorted out so we can re-build the final plasmid	
		$insert_sites = explode("-",$this->insert_sites);
		$insert_site_1 = $insert_sites[0];
		$insert_site_2 = $insert_sites[1];
		
		$plasmid_array = array();
		$plasmid_array[0] = substr($this->orig_plasmid_seq,0,$insert_site_1);
		$plasmid_array[1] = $this->insert_seq;
		$plasmid_array[2] = substr($this->orig_plasmid_seq,$insert_site_2);
		
		//grab the full sequence of the new construct before the values in $plasmid_array are modified, break it into chunks so it displays nice, and add some colour.
		$left_seq_remainder = (strlen($plasmid_array[0])%10);
		$left_space = ($left_seq_remainder == 0) ? " " : "";
			
		$target_seq_remainder = ((strlen($this->insert_seq)-(10-$left_seq_remainder))%10);
		$target_space = ($target_seq_remainder == 0) ? " " : "";
		
		if ($target_seq_remainder < 0)
			{
			$target_sequence = $this->insert_seq;	
			$right_sequence = substr($plasmid_array[2],0,(10-strlen($this->insert_seq)-$left_seq_remainder))." ".chunk_split(substr($plasmid_array[2],(10-strlen($this->insert_seq)-$left_seq_remainder)),10," ");
			}
		else
			{
			$target_sequence = substr($this->insert_seq,0,(10-$left_seq_remainder))." ".rtrim(chunk_split(substr($this->insert_seq,(10-$left_seq_remainder)),10," ")).$target_space;	
			$right_sequence = substr($plasmid_array[2],0,(10-$target_seq_remainder))." ".chunk_split(substr($plasmid_array[2],(10-$target_seq_remainder)),10," ");
			}
		
		
		$left_sequence = rtrim(chunk_split($plasmid_array[0],10," ")).$left_space;
		
		$new_construct = "<span style='background-color:#09F;font-size:5pt;font-family:Courier New;'>".$left_sequence."</span><span style='background-color:#6F3;font-size:5pt;font-family:Courier New;'>".$target_sequence."</span><span style='background-color:#09F;font-size:5pt;font-family:Courier New;'>".$right_sequence."</span>";
		
		return $new_construct;	
		}
	
	}

		
	
	
?>