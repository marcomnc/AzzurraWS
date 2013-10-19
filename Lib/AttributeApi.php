<?php

class AttributeApi {

    private $_helper; 
    
    public function __construct() {
        $this->_helper = Mage::Helper("autelcatalog/product");
    }

    /**
     * Imposto un attributo su un prodotto. 
     * Se l'attrobuto è un select/multiselect il valore coincide al codice nello store ADMIN.
     * Se il valore non esiste viene creato per lo store Admin
     * @param Mage_Catalog_Model_Product $prod
     * @param type $attributeCode
     * @param type $attributeValue
     */
    public function setProductAttribute (Mage_Catalog_Model_Product $prod, $attributeCode, $attributeValue) {
        
        $actRow = $this->_getAttribute($attributeCode);

        if ($actRow !== false && $actRow->getIsUserDefined() && 
           ($actRow->getFrontendInput() == "select" || $actRow->getFrontendInput() == "multiselect" )) {
            //Cerco il valore tra le opzione 
            $_attrColl = Mage::getResourceModel('eav/entity_attribute_option_collection')
                           ->setStoreFilter(0)
                           ->setAttributeFilter($actRow->getId())
                           ->addFieldToFilter(' tdv.value ', array("in" => explode (";",$attributeValue)) );
            $_attrValueArray = array();
            foreach ($_attrColl as $_value) {
                $_attrValueArray[] = $_value->getId(); 
            }
            $_attrValue = implode(",", $_attrValueArray);                                        
        } else {
            $_attrValue = $attributeValue;
        }  
        
        $prod->setData($attributeCode, $_attrValue);
        
    }
    
    public function AggiornaTabellaGenerica($tabellaGenerica) {
        
        if (property_exists($tabellaGenerica, "IDTabella") && property_exists($tabellaGenerica, "Codice")) {
            $attributeCode = strtolower($tabellaGenerica->IDTabella);
            $attribute = $this->_getAttribute($attributeCode);
            if ($attribute !== false && ($attribute->getFrontendInput() == "select" || $attribute->getFrontendInput() == "multiselect" )) {
                $actId = null;
                foreach ($this->_getArrayOfOption($attributeCode) as $_attr) {
                    if ($_attr["label"] == $tabellaGenerica->Codice) {
                        $actId = $_attr["value"];
                        break;
                    }
                }
                $this->_updateAttribute($tabellaGenerica, $attribute, $actId);
            }            
        }
        
    }
    
    
    private function _updateAttribute($tabellaGenerica, $attribute, $optId = null) {
        try {
            if (is_null($optId)) {                
                $optId = "option_1";
            }
            $value = array($optId => array( Mage_Core_Model_App::ADMIN_STORE_ID => $tabellaGenerica->Codice,
                                            BASE_STORE => $tabellaGenerica->Descrizione));
                            
            $result['value'] = $value;
            $_attrModel = Mage::getModel('catalog/resource_eav_attribute')->Load($attribute->getId());                
            $_attrModel->setData('option',$result);
            $_attrModel->save();
            /**
             * Clear translation cache because attribute labels are stored in translation
             */
            Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
        } catch  (Exception $e) {
            myLog($e, Zend_Log::ERR);  
            throw new Exception($e->getMessage() . "\nTabella  ". $tabellaGenerica->IDTabella() . "/" . $tabellaGenerica->Codice . "/" . $tabellaGenerica->Descrzione);
        }
    }
    
    
    private function _getAttribute($attributeCode) {
        $retValue = false;
        if ($attributeCode != "") {
            $retValue = Mage::getResourceModel('eav/entity_attribute_collection') //Recuper l'id dell'attributo
                            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                            ->addFieldToFilter('attribute_code', $attributeCode) 
                            ->load()
                            ->getFirstItem();
            if (is_null($retValue) || $retValue->getId() == 0) {
                $retValue == false;
            }
        }
        return $retValue;
    }
    
    private function _getArrayOfOption($attributeCode) {
        $retArray = Array();
        $attribute = $this->_getAttribute($attributeCode);
        if ($attribute !== false && $attribute->GetId() > 0 && 
            ($attribute->getFrontendInput() == "select" || $attribute->getFrontendInput() == "multiselect" )) {
            $retArray = Mage::getResourceModel('eav/entity_attribute_option_collection')
                                ->setAttributeFilter($attribute->getId())
                                ->setStoreFilter(Mage_Core_Model_App::ADMIN_STORE_ID, false)
                                ->load()
                                ->toOptionArray();
        }
        return $retArray;        
    }
    
}
?>
