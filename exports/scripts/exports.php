<?php
class TableLoader 
{
	public $firstfile=true;
	public $mysqli;
	public $init_array = array();  
	public $func;
	public $old_files="";
	Public $Alert="";
	Public $cleannull=0;
	
 	function __construct() {
		$this->init_array = parse_ini_file("tableloader.ini",true);
		$this->displaymsg ("INI loaded");
	}
	
	function connect() {
		$this->mysqli = new mysqli($this->init_array["db"]["hostname"],$this->init_array["db"]["username"], $this->init_array["db"]["password"], $this->init_array["db"]["database"], $this->init_array["db"]["port"]);
		if ($this->mysqli->connect_errno) {
			$this->LogError("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error);
		}
		$this->displaymsg ("Connected to MySQL");
	}	
	function disconnect(){
		mysqli_close($this->mysqli);
			$this->displaymsg ("Disconnected");
	}
	
	function LogError($msg){
		file_put_contents($this->init_array["setting"]["path"].$this->init_array["logging"]["logfile"], date('m/d/Y h:i:s a', time()).": ".$this->func." - ".$msg."\r\n", FILE_APPEND ) ;
	}	
	
	function LogStatus($file, $status){
		if ($this->firstfile){
			file_put_contents($this->init_array["setting"]["path"].$this->init_array["logging"]["logstatus"], $file."\t".$status."\r\n") ;
			$this->firstfile=false;
		}else{
			file_put_contents($this->init_array["setting"]["path"].$this->init_array["logging"]["logstatus"], $file."\t".$status."\r\n", FILE_APPEND ) ;
		}
	}
	
	function loadfile($fileinfo,$table, $columns, $duplicate, $delimiter){
		set_time_limit($this->init_array["setting"]["timeout"]);
		$sqlstr = "INSERT INTO ".$table." ".$columns." VALUES ";
		$j=0;
		$cnt=0;
		
		if (($handle = fopen($this->init_array["setting"]["path"].$fileinfo, "r")) !== FALSE) {
			while (($data = fgetcsv($handle,0,$delimiter)) !== FALSE) {
				$cnt++;
				$sqlstr .="(";
				//$this->displaymsg ("Row: ".$j++);
				foreach ($data as $col)
				{	
						$sqlstr .= "'".$this->cleanchar($col)."',";
				}
				$sqlstr =substr($sqlstr,0,-1);
				$sqlstr .="),";
				if($j++> $this->init_array["setting"]["insertlimit"]){
					$sqlstr =substr($sqlstr,0,-1);
					$sqlstr .=$duplicate;
					//$this->LogError($sqlstr);
					$this->execquery($sqlstr);

					$sqlstr = "INSERT INTO ".$table." ".$columns." VALUES ";
					$j=0;
				}
			}
			fclose($handle);
		}
		$sqlstr =substr($sqlstr,0,-1);
		$sqlstr .=$duplicate;

		//$this->LogError($sqlstr);
//		if (strpos($sqlstr,'VALUES;')==false && strpos($sqlstr,'VALUESON')==false) {
		if ($j>0){
			$this->execquery($sqlstr);
		}
		return $cnt;
	}
	
	function cleanchar($chars)
	{ 
		$chars=str_replace("'","\'",$chars);
		
		return $chars;
	}
	
	function processfiles(){
		$this->connect();
		
		for ($i=0; $i<sizeof($this->init_array["files"]["filename"]);$i++)
		{
			//set file name being processed for logging.
			$this->func=$this->init_array["files"]["filename"][$i];
			$this->cleannull=$this->init_array["nullcheck"]["$this->func"];
			
			if(file_exists($this->init_array["setting"]["path"].$this->init_array["files"]["filename"][$i])){
				$status='Passed';
				//$this->execquery($this->preparesql($this->init_array["files"]["filename"][$i],$this->init_array["files"]["tablename"][$i],$this->init_array["files"]["columns"][$i],$this->init_array["files"]["duplicate"][$i]));
				$rec_cnt = $this->loadfile($this->init_array["files"]["filename"][$i],$this->init_array["files"]["tablename"][$i],$this->init_array["files"]["columns"][$i],$this->init_array["files"]["duplicate"][$i],$this->init_array["files"]["delimiter"][$i]);
				$file_dt = date ("Y-m-d H:i:s", filemtime($this->init_array["setting"]["path"].$this->init_array["files"]["filename"][$i]));
				
				// alert logic
				//$this->displaymsg ("File time: "$this->init_array["files"]["filename"][$i]."  =  ".filemtime($this->init_array["setting"]["path"].$this->init_array["files"]["filename"][$i]));
				//$daysold = intval((time() - filemtime($this->init_array["setting"]["path"].$this->init_array["files"]["filename"][$i]))/86400);

/* 				if($daysold<$this->init_array["setting"]["alertdays"]){
					$status="Passed";
				}else{
					$status="File is $daysold days old";
					//$this->Alert.= $this->init_array["files"]["filename"][$i].": old, ";
				} */
			}else{
				$status="File Does Not Exist";
				//$this->Alert.= $this->init_array["files"]["filename"][$i].": missing, ";
			}
			$this->LogStatus($this->init_array["files"]["filename"][$i],$status);
			
			$strsql = "REPLACE INTO fs_files (NAME, file_dt, rec_cnt, owner_id, update_by, create_by, create_time, description) 
						VALUES ('$this->func', '$file_dt',$rec_cnt,1,1,1,NOW(),'$status');";
						
			$this->execquery($strsql);
			$this->displaymsg ("Processed ".$this->init_array["files"]["filename"][$i]);			
		}
		$this->disconnect();
	}
	
	function execquery($sql){
		if($this->cleannull=='1'){
		//if ($this->func <>'styles.txt'){
			$sql=str_replace("'0'","' '",$sql);
			$sql=str_replace("'',","' ',",$sql);
			$sql=str_replace("'NULL',","' ',",$sql);
		}else{
			$sql=str_replace("'NULL',","NULL,",$sql);
		}
			
		if (!$this->mysqli->query($sql)){
			$this->LogError($this->mysqli->error.":   ".$sql);
		}
	 }
	
	function preprocessing(){
		$this->connect();
		$this->cleannull=0;
		
		for ($i=0; $i<sizeof($this->init_array["pre"]["query"]);$i++)
		{
			$this->func="Pre query: ".$this->init_array["post"]["query"][$i];
			$this->execquery($this->init_array["pre"]["query"][$i]);
			$this->displaymsg ($this->init_array["pre"]["query"][$i]);
		}
		$this->disconnect();
	}
	
	function postprocessing(){
		$this->connect();
		$this->cleannull=0;
		for ($i=0; $i<sizeof($this->init_array["post"]["query"]);$i++)
		{
			$this->func="Post query: ".$this->init_array["post"]["query"][$i];
			$this->execquery($this->init_array["post"]["query"][$i]);
			$this->displaymsg ("Post query: ".$this->init_array["post"]["query"][$i]);
		}
		$this->disconnect();
	}
	
	function LogAlert(){
		$this->connect();		
		
		if (strlen($this->Alert)>0){
			$strsql = "INSERT INTO notification (TYPE,SUBJECT,create_time, update_time,message) VALUES ('Alert','Synch Data',NOW(),NOW(),'".$this->Alert."');";
				$this->displaymsg ("Alert query: ".$this->Alert);
				//$this->LogError($this->init_array["alert"]["query"][$i].substr($this->old_files,0,-2)."');");
				//SELECT DATEDIFF(NOW(), MAX(period_dt)) FROM fs_sales
				$this->execquery($strsql);
		}else{
			$this->displaymsg ("Alert query: No Alerts");
			$this->execquery("delete from notification where subject='Synch Data';");
		}
		$this->disconnect();	
		}
	
	function displaymsg($msg)
	{
		if ($this->init_array["setting"]["display"])
		{
			ob_end_flush();
			echo date('m/d/Y h:i:s a', time()).": ".$msg."</br>";
			flush();
			ob_start();
		}
	}
 }

$load = new TableLoader;
$load->preprocessing();
$load->processfiles();
$load->postprocessing();
$load->LogAlert();
?>