<?php

  class ImportAPI
  {
  	
  	
  	public function __construct() {
  		
  	}
  	
  	public function Start() {
  		
  		$uid = $_SERVER['REMOTE_ADDR'] . "-" . microtime(true);
  		
  		//Inizializzo il record
  		
  		$sql = "Insert into AV_Import (UID, StartDate) values (? ,?)";
  		$params = array($uid, DbAPI::MySqlDateTime());
  		
  		DbAPI::ExecuteNoRead($sql, $params);
  		
  		return $uid;
  	}
  	
  	public function Stop($uid) {
  		
  		$importSession = $this->getSession($uid);
		
  		if ($importSession) {
  		  $importSession['EndDate'] = DbAPI::MySqlDateTime();
  		  
  		  
  		  if (DbAPI::SaveTable("AV_Import", $importSession, array('Id'))) {
  		    
  		    //Parto con gli aggiornamenti
  		    
  		  }
  		  
  		}
  		
  		//amen
  		
  	}
  	
  	public function getSession($uid = "") {
  		
  		if ($uid == "") {
    		$uid = $_SERVER['REMOTE_ADDR'] . "-%";
    		
    		foreach (getallheaders() as $key => $value) {
    			if ($key == "Chiave del microtime") {
    				$uid = $value;
    				break;
    		  }
    		}
  		}
  		
  		$aprams = array("UID" =>        array( 'operator' => 'AND',
                                             'comp'     => 'Like',
                                             'value'	   => $uid),
                      "StartDate" =>  array( 'operator' => 'AND',
                                             'comp'     => '<',
                                             'value'	   => DbAPI::MySqlDateTime()),
                      "EndDate" =>  array( 'operator' => 'AND',
                                             'comp'     => 'is null'));
                                             
  		$session = DbAPI::GetTable("AV_Import", $aprams); 		
  		
  		return (isset($session[0])) ? $session[0]: $session;
  		
  	}
  	  
  }
  
 ?>