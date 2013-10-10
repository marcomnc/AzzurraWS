<?php

class AnagraficheAPI {
    
    private $_helper;
    
    public function __construct() {
        $this->_helper = Mage::Helper("autelcatalog/product");
    }
    
    public function addProduttore($produttore)
    {
        $select = $this->_helper->getDbReader()->select()
                        ->from(array("avProd" => "AV_Produttore"))
                        ->where("IDAnagrafica = ?", $produttore->Codice);
      
        $img = false;
        $fileName = "";
        if ($produttore->Immagine != "" && $produttore->ImmagineBase64) {
            $fileName = Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . "av_produttore/" . strtolower($produttore->Codice) . "_" . strtolower($produttore->Immagine);
            if (file_exists($fileName)) {
                unlink($fileName);
            }
            $img = imagecreatefromstring(base64_decode($produttore->ImmagineBase64));
            try {
    
                if($img != false) {
                    switch (strtolower(substr($fileName,-3))) {
                        case "png": 
                            imagealphablending($img, false);
                            imagesavealpha($img, true);
                            imagepng($img, $fileName);
                            break;
                        case "jpg":
                            imagejpeg($img, $fileName);
                            break;
                        case "gif":
                            imagegif($img, $fileName);
                            break;
                    }
                }
            } catch (Exception $e) {
                myLog($e, Zend_Log::ERR, "AzzurraWS_error.log", true);   
            }
        }

        if ($this->_helper->getDbReader()->fetchOne($select)."" == "") {
            $this->_helper->getDbWriter()->insert("AV_Produttore",
                                            array("IDAnagrafica"    => $produttore->Codice,
                                                  "Codice"          => $produttore->Codice,
                                                  "NomeProduttore"  => $produttore->NomeProduttore,
                                                  "Descrizione"     => $produttore->Descrizione,
                                                  "Immagine"        => strtolower($produttore->Codice) . "_" . strtolower($produttore->Immagine)));
        } else {
            $this->_helper->getDbWriter()->update("AV_Produttore",
                                              array("Codice"          => $produttore->Codice,
                                                    "NomeProduttore"  => $produttore->NomeProduttore,
                                                    "Descrizione"     => $produttore->Descrizione,
                                                    "Immagine"        => strtolower($produttore->Codice) . "_" . strtolower($produttore->Immagine)),
                                              "IDAnagrafica = '" . $produttore->Codice ."'");
        }        

        //Aggiungo il codice agli attributi
        $this->_updAttribute(PRODUTTORE, $produttore->Codice, (trim($produttore->NomeProduttore) != "")?$produttore->NomeProduttore:$produttore->Descrizione, 1);
    }
    
    private function _updAttribute ($attributeName, $code, $value, $store ) {
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
                        ->getIdByCode( 'catalog_product', $attributeName);
        $select = $this->_helper->getDbReader()->select()
                       ->from(array("opt"=>Mage::getSingleton('core/resource')->getTableName("eav_attribute_option")))
                       ->join(array("val"=>Mage::getSingleton('core/resource')->getTableName("eav_attribute_option_value")),
                              "val.option_id = opt.option_id")
                       ->where("opt.Attribute_id = $attributeId")
                       ->where("val.store_id = 0")
                       ->where("val.value = '$code'")
                       ->reset(Zend_Db_Select::COLUMNS)
                       ->columns(array("option_id" => "opt.option_id"));

        $optionId = $this->_helper->getDbReader()->fetchOne($select)."";
        if ( $optionId == "") {
            $this->_helper->getDbWriter()->insert(Mage::getSingleton('core/resource')->getTableName("eav_attribute_option"),
                                                    array("Attribute_id"    => $attributeId,
                                                          "sort_order"      => 0));
            $optionId = $this->_helper->getDbReader()->lastInsertId();
            $this->_helper->getDbWriter()->insert(Mage::getSingleton('core/resource')->getTableName("eav_attribute_option_value"),
                                                      array("value"     => $code,
                                                            "option_id" => $optionId,
                                                            "store_id" => 0));
        } 
        $select = $this->_helper->getDbReader()->select()
                       ->from(array("val"=>Mage::getSingleton('core/resource')->getTableName("eav_attribute_option_value")))
                       ->where("val.option_id = $optionId")
                       ->where("val.store_id = $store")
                       ->reset(Zend_Db_Select::COLUMNS)
                       ->columns(array("value_id" => "val.value_id"));
        $valueId = $this->_helper->getDbReader()->FetchOne($select)."";
        if ($valueId == "") {
            $this->_helper->getDbWriter()->insert(Mage::getSingleton('core/resource')->getTableName("eav_attribute_option_value"),
                                                                  array("value"     => $value,
                                                                        "option_id" => $optionId,
                                                                        "store_id" => $store));                
        } else {
            $this->_helper->getDbWriter()->update(Mage::getSingleton('core/resource')->getTableName("eav_attribute_option_value"),
                                                  array("value"     => $value),
                                                  "value_id = $valueId and store_id = $store");
        }
        
    }

    /**
     * Recupera il codice produttore
     * 
     * @param type $id  Codice Producttore
     * @param type $where   Campo su cui fare il filtro
     * @param string $idx   Campo da ritornare
     * @return type 
     */
    public function getProduttore($id, $where="IDAnagrafica", $idx="Codice") {
        $defaultReturnValue = ($idx="IDAnagrafica")?0:null;
        $select = $this->_helper->getDbReader()->select()
                        ->from(array("avProd" => "AV_Produttore"))
                        ->where("$where = '$id'"  );
        foreach ($this->_helper->getDbReader()->fetchAll($select) as $prod) {
            return $prod[$idx];
        }        
        return $defaultReturnValue;
    }
    
    public function removeProduttore($codice) {
        $id = $this->getProduttore($codice, "Codice","IDAnagrafica");
        if (!is_null($id)) {
            $this->_helper->getDbWriter()->delete("AV_Produttore", "IDAnagrafica = $id");
        }
    }
    
    public function selezionaCodiciProduttore() {
        $retVal = array();
        $select = $this->_helper->getDbReader()->select()
                        ->from(array("avProd" => "AV_Produttore"));
        foreach ($this->_helper->getDbReader()->fetchAll($select) as $prod) {
            $retVal[] = $prod["Codice"];
        }
        return $retVal;        
    }
    
    public function selezionaProduttore($codice) {
        $prodInfo = null;
        $select = $this->_helper->getDbReader()->select()
                        ->from(array("avProd" => "AV_Produttore"))
                        ->where("Codice = '$codice'"  );       
        foreach ($this->_helper->getDbReader()->FetchAll($select) as $prod) {            
            $prodInfo = new AnagraficaInfoProduttoreInfo();
            $prodInfo->ID = $prod["IDAnagrafica"];
            $prodInfo->Nome = str_replace("&", "&amp;", substr($prod["Descrizione"],0,25));
            $prodInfo->IDAnagrafica = $prod["IDAnagrafica"];
            $prodInfo->Codice = $prod["Codice"];
            $prodInfo->Descrizione = str_replace("&", "&amp;", $prod["Descrizione"]);
            $prodInfo->NomeProduttore = str_replace("&", "&amp;", $prod["NomeProduttore"]);         
            break;
        }
        return $prodInfo;
    }
    
    public function selezionaCliente($idCliente) {
        $clienteInfo = null;
        $cliente = Mage::getModel("customer/customer")->Load($idCliente);
        if ($cliente->getId() > 0) {
            $billing = Mage::getModel("customer/Address")->Load($cliente->getDefaultBilling());
            $shipping = Mage::getModel("customer/Address")->Load($cliente->getDefaultShipping());
            $clienteInfo = new AnagraficaInfoClienteInfo();
            $clienteInfo->ID = $cliente->getId();
            $clienteInfo->Riferimento = $shipping->getLastname() . " " . $shipping->getFirstname();
            $clienteInfo->RagioneSociale = (($billing->getCompany().'') == '') ? $clienteInfo->Riferimento : $billing->getCompany();
            $clienteInfo->Nome = $cliente->getFirstname();
            $clienteInfo->Cognome = $cliente->getLastname();
            $street = $billing->getStreet();
            $clienteInfo->Indirizzo = "";
            if (array_key_exists(0,$street)) {
                $clienteInfo->Indirizzo = $street[0];
            }
            if (array_key_exists(1,$street)) {
                $clienteInfo->Indirizzo = (($clienteInfo->Indirizzo!="")?" ":"").$street[1];
            }
            $clienteInfo->CAP = $billing->getPostcode();
            $clienteInfo->Comune = $billing->getCity();
            $clienteInfo->Frazione = "";
            $clienteInfo->Provincia = $billing->getRegion();
            $clienteInfo->Nazione = $billing->getCountryId();
            $clienteInfo->PartitaIVA = "";
            $clienteInfo->CodiceFiscale = "";
            if ($billing->getCompany()."" == "") {
                $clienteInfo->CodiceFiscale = $cliente->getTaxvat();
            } else {
                $clienteInfo->PartitaIVA = $cliente->getTaxvat();
            }
            $clienteInfo->Mail = $cliente->getEmail();
            $clienteInfo->Telefono = $billing->getTelephone();
            $clienteInfo->Fax = $billing->getFax();
            $clienteInfo->Codice = ($cliente->getAvCodice()."" == "")?CODICE_CLIENTE:$cliente->getAvCodice();
            $clienteInfo->IDAnagrafica = $cliente->getId();
        }
        return $clienteInfo;
    }
    
    public function getProduttoreAttribute($idAnagrafica) {
        $ret = null;
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
                        ->getIdByCode( 'catalog_product', PRODUTTORE);
        $select = $this->_helper->getDbReader()->select()
                       ->from (array("eo" => Mage::getSingleton('core/resource')->getTableName("eav_attribute_option") ))
                       ->join (array("ev" => Mage::getSingleton('core/resource')->getTableName("eav_attribute_option_value") ),
                               "eo.option_id=ev.option_id")
                       ->where("eo.attribute_id = ?", $attributeId )
                       ->where("ev.store_id = 0")
                       ->where("ev.value = ?", $idAnagrafica )
                       ->reset(Zend_Db_Select::COLUMNS)
                       ->columns(array("option_id" => "ev.option_id"));

        return $this->_helper->getDbReader()->fetchOne($select)."";
    }
    
    public function getProduttoreId($idProduttore) {
        $ret = null;
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
                        ->getIdByCode( 'catalog_product', PRODUTTORE);
        $select = $this->_helper->getDbReader()->select()
                       ->from (array("eo" => Mage::getSingleton('core/resource')->getTableName("eav_attribute_option") ))
                       ->join (array("ev" => Mage::getSingleton('core/resource')->getTableName("eav_attribute_option_value") ),
                               "eo.option_id=ev.option_id")
                       ->where("eo.attribute_id = ?", $attributeId )
                       ->where("ev.store_id = 0")
                       ->where("eo.option_id = ?", $idProduttore )
                       ->reset(Zend_Db_Select::COLUMNS)
                       ->columns(array("value" => "ev.value"));

        return $this->_helper->getDbReader()->fetchOne($select)."";
    }
    
        
}

?>
