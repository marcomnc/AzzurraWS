<?php

  class ImportAPI
  {
  	const EAV_PROD_IDGESTIONALE     = 139;
        const EAV_PROD_STATUS           = 89;
  	
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
		
  		if ($importSession ) {
  		  $importSession['EndDate'] = DbAPI::MySqlDateTime();
  		  
  		  
  		  if (DbAPI::SaveTable("AV_Import", $importSession, array('Id'))) {
  		    
  		    //Cancello la differenza tra gli articoli anullati e quelli inseriti
                    $importSession['ElabQtaStart'] = DbAPI::MySqlDateTime();
  		    DbAPI::SaveTable("AV_Import", $importSession, array('Id'));
                    
                    $this->UpdateQtaProduct($importSession['UID']);
                    
                    $this->deleteProduct($importSession['UID']);                                       
                    
                    $this->rebuildIndex("catalog_product_attribute");
                    $this->rebuildIndex("cataloginventory_stock");
                    
                    $importSession['ElabQtaEnd'] = DbAPI::MySqlDateTime();
  		    DbAPI::SaveTable("AV_Import", $importSession, array('Id'));
                    
                    //Avvio in modalità asincrona l'aggiornamento dei prodotti.... fico
                    
                    myLog("Avvio l'esecuzione batch", Zend_Log::DEBUG);
                    
                    $c = curl_init();
                    curl_setopt($c, CURLOPT_URL, WSDL::getBaseUri().'/ws.php?___execute_batch=' . $importSession["Id"]);
                    curl_setopt($c, CURLOPT_HEADER, false);         // Don't retrieve headers
                    curl_setopt($c, CURLOPT_NOBODY, true);          // Don't retrieve the body
                    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);  // Return from curl_exec rather than echoing
                    curl_setopt($c, CURLOPT_FRESH_CONNECT, true);   // Always ensure the connection is fresh

                    // Timeout super fast once connected, so it goes into async.
                    curl_setopt( $c, CURLOPT_TIMEOUT, 1 );

                    curl_exec( $c );
                                        
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
                                             
  		$session = DbAPI::GetTable("AV_Import", $aprams, array(), array("StartDate" => "DESC")); 		
  		
  		return (isset($session[0])) ? $session[0]: $session;
  		
  	}
  	  
        public function deleteProduct($UID) {
            
            $updateStatus = "update " . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_int') ."\n";
            $updateStatus .= "set value = " . Mage_Catalog_Model_Product_Status::STATUS_DISABLED ."\n";
            $updateStatus .= "where \n";
            $updateStatus .= "attribute_id = " . self::EAV_PROD_STATUS . " and entity_id in (\n";
            $updateStatus .= "SELECT entity_id  FROM `catalog_product_entity_varchar` WHERE `attribute_id` = 139 AND `store_id` = 0 AND `value` in (\n";
            $updateStatus .= "SELECT a.idGestionale FROM AV_Import_Delete a \n";
            $updateStatus .= "left join AV_Import_Insert b \n";
            $updateStatus .= "on a.uid = b.uid \n";
            $updateStatus .= "and a.IdGestionale = b.IdGestionale \n";
            $updateStatus .= "where a.Uid = '$UID' and b.id is null)) ";
            
            $updateStock  = "update " . Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item') . "\n";
            $updateStock .= "set qty = 0 \n";
            $updateStock .= "where product_id in ( \n";
            $updateStock .= "SELECT entity_id  FROM `catalog_product_entity_varchar` WHERE `attribute_id` = " . self::EAV_PROD_IDGESTIONALE . "  AND `store_id` = 0 AND `value` in ( \n";
            $updateStock .= "SELECT a.idGestionale FROM AV_Import_Delete a \n";
            $updateStock .= "left join AV_Import_Insert b \n";
            $updateStock .= "on a.uid = b.uid \n";
            $updateStock .= "and a.IdGestionale = b.IdGestionale \n";
            $updateStock .= "where a.Uid = '$UID' and b.id is null)) ";
            
            $updateStockStatus  = "update " . Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_status') . "\n";
            $updateStockStatus .= "set qty = 0, stock_status = 0 \n";
            $updateStockStatus .= "where product_id in ( \n";
            $updateStockStatus .= "SELECT entity_id  FROM `catalog_product_entity_varchar` WHERE `attribute_id` = " . self::EAV_PROD_IDGESTIONALE . " AND `store_id` = 0 AND `value` in ( \n";
            $updateStockStatus .= "SELECT a.idGestionale FROM AV_Import_Delete a \n";
            $updateStockStatus .= "left join AV_Import_Insert b \n";
            $updateStockStatus .= "on a.uid = b.uid \n";
            $updateStockStatus .= "and a.IdGestionale = b.IdGestionale \n";
            $updateStockStatus .= "where a.Uid = '$UID' and b.id is null)) ";            
            
myLog("Eseguo Query per cancellazione", Zend_Log::DEBUG);
myLog("$updateStatus", Zend_Log::DEBUG);
            DbAPI::ExecuteNoread($updateStatus, array());
myLog("$updateStock", Zend_Log::DEBUG);
            DbAPI::ExecuteNoread($updateStock, array());
myLog("$updateStockStatus", Zend_Log::DEBUG);
            DbAPI::ExecuteNoread($updateStockStatus, array());
            
            
            $updateDelete = "Update AV_Import_Delete set ExecuteDate = '" . DbAPI::MySqlDateTime() . "'\n";
            $updateDelete .= "where UID = '$UID'";
myLog("$updateDelete", Zend_Log::DEBUG);            
            DbAPI::ExecuteNoread($updateDelete, array());
                        
        }
        
        public function UpdateQtaProduct($UID) {
            
            $updateStock  = "update " . Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item') ." item \n";
            $updateStock  .= "join catalog_product_entity_varchar prod on item.product_id = prod.entity_id \n";
            $updateStock  .= "join AV_Import_Insert on prod.attribute_id = " . self::EAV_PROD_IDGESTIONALE . " and prod.store_id = 0 and prod.value = AV_Import_Insert.idGestionale \n";
            $updateStock  .= "set item.qty = AV_Import_Insert.QtaNew, \n";
	    $updateStock  .= "is_in_stock = IF(AV_Import_Insert.QtaNew > 0, 1, 0) \n";
            $updateStock  .= "where UID = '$UID'";
            
            $updateStockStatus  = "update " . Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_status') ." item \n";
            $updateStockStatus  .= "join catalog_product_entity_varchar prod on item.product_id = prod.entity_id \n";
            $updateStockStatus  .= "join AV_Import_Insert on prod.attribute_id = " . self::EAV_PROD_IDGESTIONALE . " and prod.store_id = 0 and prod.value = AV_Import_Insert.idGestionale \n";
            $updateStockStatus  .= "set item.qty = AV_Import_Insert.QtaNew, stock_status = IF(AV_Import_Insert.QtaNew >0 , 1, 0)\n";
            $updateStockStatus  .= "where UID = '$UID'";

myLog("Eseguo Query per Aggiornamento qty", Zend_Log::DEBUG);
myLog("$updateStock", Zend_Log::DEBUG);
            DbAPI::ExecuteNoread($updateStock, array());
myLog("$updateStockStatus", Zend_Log::DEBUG);
            DbAPI::ExecuteNoread($updateStockStatus, array());
        }
        
        
        public function rebuildIndex($indexName) {
            
            $pProcess = Mage::getModel('index/process')->Load($indexName, 'indexer_code');
myLog("Ricostruisco l'indice per $indexName", Zend_Log::DEBUG);            
            $pProcess->reindexAll();
        }
        
        public function batch($sessionId) {
            
            $session = DbAPI::GetTable("AV_Import", array('Id' =>  array( 'operator' => 'AND','comp' => '=','value' => $sessionId))); 		

            if (isset($session[0]['EndDate']) && $session[0]['EndDate']) {
                $mySession = $session[0];
myLog("INIZIO AD IMPORTARE " . $mySession['UID'], Zend_Log::DEBUG, "Azzurra.Async.log");                                        
                $mySession['ElabArtStart'] = DbAPI::MySqlDateTime();
                DbAPI::SaveTable("AV_Import", $mySession, array('Id'));
                
                $products = DbAPI::GetTable("AV_Import_Insert", array("IdImport" => array('operator' => 'AND', 'comp' => '=', 'value' => $mySession['Id'])));
                
                if (sizeof($products) > 0) {
                    $productAPI = new ProductApi();
                    foreach ($products as $product) {
                        
                        $myProduct = unserialize($product['SerializedObject']);
                        $myProduct->QtaDisponibile = -1; // per non far aggiornare la qtà
                        
myLog("Inizio ad importare " . $myProduct->CodiceArticolo, Zend_Log::DEBUG, "Azzurra.Async.log");                        

                        $productAPI->inserisciArticolo($myProduct);

myLog("Finito di importare " . $myProduct->CodiceArticolo, Zend_Log::DEBUG, "Azzurra.Async.log");                                                
                        $product['ExecuteDate'] = DbAPI::MySqlDateTime();
                        DbAPI::SaveTable("AV_Import_Insert", $product, array('Id'));
                    }
                    
myLog("FINITO DI IMPORTARE SESSIONE " . $mySession['UID'], Zend_Log::DEBUG, "Azzurra.Async.log");                       
                }
                
                $mySession['ElabArtStart'] = DbAPI::MySqlDateTime();
                DbAPI::SaveTable("AV_Import", $mySession, array('Id'));
                
            } else {
                myLog("Tentativo di elaborare una sesseione $sessionId inesistente o non chiusa", Zend_Log::DEBUG, "Azzurra.Async.log");
            }
            
        }
        
  }
  
 ?>
