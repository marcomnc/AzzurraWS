<?php
class DbAPI 
{
	static protected $_writer = null;
	static protected $_reader = null;
	
	public function __construct() {
		
	}
	
	public static function dbInit() {
		self::$_writer = Mage::getSingleton('core/resource')->getConnection('core_write');
		self::$_reader = Mage::getSingleton('core/resource')->getConnection('core_read');
	}
	
	public static function ExecuteNoread($sql, $params) {
		
			try {
				self::$_writer->query($sql, $params);
			} catch (Exception $ex) {
				print_r($ex);
				die();
			}
		
	}
	
	public static function MySqlDateTime($time = null, $convert = true) {
		
		if (is_null($time))
			$time = time();
			
		$curUtc = date_default_timezone_get();
		
		if ($convert) 
			date_default_timezone_set("Europe/Rome");
			
		$mysqltime = date ("Y-m-d H:i:s", $time);
		
		date_default_timezone_set($curUtc);
		return $mysqltime;
	}
	
	public static function GetTable($tName, $params = array(), $fields = array(), $order = array(), $bind = null) {
	
		$columns = "";
		
		if (sizeof($fields) > 0) {
			foreach ($fields as $field) {
				$columns .= (($columns != "") ? "," : "") . $field;
			}
		}
		
		if ($columns == "") {
			$columns = "*";
		}
		
		$where = "";
                if (is_array($params)) {
                    $bind = array();
                    if (sizeof($params) > 0) {			
                            foreach ($params as $field  => $comparision) {
                                    $where .= $comparision['operator'] . " $field " . $comparision["comp"];
                                    if (isset($comparision["value"])) {
                                      $where .= " ? ";
                                      $bind[] = $comparision["value"];
                                    }
                            }

                    }
                } else {
                    $where = $params;
                }
		
		$orderBy = "";
		if (!is_null($order) && is_array($order) && sizeof($order) > 0) {			
		  foreach ($order as $field => $ord) {
		    $orderBy .= ($orderBy == "") ? ", ": "";
		    $orderBy .= "$field $ord";
		  }
		}
		
		
		$select = "SELECT $columns \nFROM $tName \nWHERE 1=1 \n$where $orderBy";
		try {
			$records = self::$_reader->fetchAll($select, $bind);
		} catch (Exception $ex) {
			print_r($ex);
			return false;
		} 
		
		return $records;
	}
	
	public static function SaveTable($tName, $fields, $keyFields = null) {
	  
	  $set = "";
	  $where = "";
	  $bindSet = array();
	  $bindWhere = array();
          $insertField = "";
          $insert = "";
          $bindInsert = array();
	  
	  foreach ($fields as $key => $value) {
	    
	    $isKey = false;
            if (!is_null($keyFields) && is_array($keyFields)) {
                foreach ($keyFields as $keyId) {
                    if (strtolower($key) == strtolower($keyId)) {
                        $isKey = true;
                        break;
                    }
                }
            }
	    	    
	    if (!$isKey) {
	      $set .= ($set != "") ? ",\n" : "";
	      $set .= $key . " = ?";
	      $bindSet[] = $value;
	    } else {
	      $where .= "\n\tAND $key = ?";
	      $bindWhere[] = $value;              
	    }
            
            $insertField .= ($insertField != "") ? ", $key" : $key;
            $insert .= ($insert != "") ? ", ?" : "?";
	    $bindInsert[] = $value;
	  }
          
	  $curRec = array();
          if (sizeof($bindWhere) > 0) {
              $curRec = self::GetTable($tName, $where, null, null, $bindWhere );
          }
          
          if (sizeof($bindWhere) > 0 && sizeof($curRec) > 0) {

                $update = "UPDATE $tName SET $set WHERE 1 = 1 $where";

                $bind = $bindSet;
                foreach ($bindWhere as $bw) {
                  $bind[] = $bw;
                }
                
            } else {
                $update = "INSERT INTO $tName ($insertField) VALUES ($insert)";
                $bind = $bindInsert;
            }
            
            try {
                self::$_writer->query($update, $bind);
            } catch (Exception $ex) {
                print_r($ex);
                return false;
            }  
            return true;
        }
}
?>