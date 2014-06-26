<?php

class ProductApi {
    
    private $_helper;
    private $_anagraficaApi;
    private $_categoryApi;
    private $_imgFormat;
    private $_tmpBaseDir;
    private $_mediaApi;
    private $_attributeApi;
     
    public function __construct() {
        $this->_helper = Mage::Helper("autelcatalog/product");
        $this->_anagraficaApi = new AnagraficheAPI();
        $this->_categoryApi = new CategoryApi();
        $this->_tmpBaseDir = Mage::getBaseDir('tmp') . DIRECTORY_SEPARATOR;
        $this->_imgFormat = new ImageType();
        $this->_attributeApi = new AttributeApi();
    }
    
    public function inserisciArticolo($productInfo) {
        
        //controllo se l'articolo esiste
        if (!property_exists($productInfo, "ID") || $productInfo->ID == 0) {
            return;
        } 
        
        $id = $this->_getIdbyIdGestionale($productInfo->ID);
        if (is_null($id) || $id =="") {
            //Nuovo
            $product = new Mage_Catalog_Model_Product();
            $product->setAttributeSetId("4");            
            $product->setTypeId("simple");
            $product->setAvIdproduct($productInfo->ID);                       
            $product->setweight(1);
        } else {
            $product = Mage::getModel('catalog/product')->Load($id);
        }
        $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $product->setTaxClassId(TAXABLE_GOODS);
        $product->setWebsiteIDs(array(1)); 
        //Segno gli atributi se disponibili
        if (property_exists($productInfo, "CodiceArticolo") && !is_null($productInfo->CodiceArticolo)) {
            $product->setSku($productInfo->CodiceArticolo);
        }
        if (property_exists($productInfo, "Descrizione") && !is_null($productInfo->Descrizione)) {
            $product->setName($productInfo->Descrizione);
        }            
        if (property_exists($productInfo, "Scheda") && !is_null($productInfo->Scheda)) {
            $product->setDescription($productInfo->Scheda);            
        } else {
            $product->setDescription("<p/>");
        }
        $product->setShortDescription("<p/>");
        if (property_exists($productInfo, "Immagine1") && !is_null($productInfo->Immagine1)) {
            $product->setAvImg1($productInfo->Immagine1);
        }                     
        if (property_exists($productInfo, "Immagine2") && !is_null($productInfo->Immagine2)) {
            $product->setAvImg2($productInfo->Immagine2);
        }       
        if (property_exists($productInfo, "Um1") && !is_null($productInfo->Um1)) {
            $product->setAvUm1($productInfo->Um1);                
        }
        if (property_exists($productInfo, "Um2") && !is_null($productInfo->Um2)) {
            $product->setAvUm2($productInfo->Um2);                
        }
        if (property_exists($productInfo, "IDProduttore") && $productInfo->IDProduttore != 0) {            
            $product->setManufacturer($this->_anagraficaApi->getProduttoreAttribute($productInfo->IDProduttore));                
        }
        if (property_exists($productInfo, "FattoreConversione") && $productInfo->FattoreConversione != 0) {
            $product->setAvFaconv($productInfo->FattoreConversione);
            $product->setweight($productInfo->FattoreConversione);
        }        
        if (property_exists($productInfo, "Annata") && !is_null($productInfo->Annata)) {
            $product->setAvAnno($productInfo->Annata);
        }
        if (property_exists($productInfo, "Promozione")) {
            $product->setAvPromozione((is_null($productInfo->Promozione))?0:$productInfo->Promozione);                
        }
        if (property_exists($productInfo, "PrezzoUnitario") && $productInfo->PrezzoUnitario != 0) {
            $product->setPrice($productInfo->PrezzoUnitario);
        }
        if ((property_exists($productInfo, "IDCategoria") && $productInfo->IDCategoria != 0) ||
            (property_exists($productInfo, "IDMerceologia") && $productInfo->IDMerceologia != 0) ||
            (property_exists($productInfo, "IDSottoMerceologia") && $productInfo->IDSottoMerceologia != 0)) {
            $category = array("categoria" => null,
                              "merceologia" => null,
                              "sottomerceologia" => null);
            if (property_exists($productInfo, "IDCategoria") && $productInfo->IDCategoria != 0) {
                $category["categoria"] = $productInfo->IDCategoria;
            }
            if (property_exists($productInfo, "IDMerceologia") && $productInfo->IDMerceologia != 0) {
                $category["merceologia"] = $productInfo->IDMerceologia;
            }
            if (property_exists($productInfo, "IDSottoMerceologia") && $productInfo->IDSottoMerceologia != 0) {
                $category["sottomerceologia"] = $productInfo->IDSottoMerceologia;
            }
            try {                
                $product->setCategoryIds($this->_categoryApi->getCategoryForProducts($category));
            } catch (Exception $e) {
                myLog($e, Zend_Log::ERR, "AzzurraWS_ERROR.log", true);  
                // Non faccio scattare l'errore, lo loggo e continuo 
                //throw new Exception($e->getMessage() . "\nArticolo ". $product->getSku());
            }            
        }
        
        $stock = array();
        if (property_exists($productInfo, "QtaDisponibile") && $productInfo->QtaDisponibile > 0) {
            $stock['qty'] = $productInfo->QtaDisponibile;
            if ($productInfo->QtaDisponibile > 0) 
                $stock['is_in_stock'] = 1;
            else
                $stock['is_in_stock'] = 0;
        }        
        if (property_exists($productInfo, "QtaMaxOrdinabile") && $productInfo->QtaMaxOrdinabile != 0) {
            //@todo
            //$maxQty = $productInfo->QtaMaxOrdinabile;
            
        }        
        if (sizeof($stock) > 0) {
            $product->setStockData($stock);
        }
try {        
        //Assegno Nuovi attributi aggiuntivi
        if (property_exists($productInfo, "InEvidenza") && ($productInfo->InEvidenza + 0) != 0) {
            $product->setNewsFormDate(Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            $product->setNewsToDate("");
        } else {
            $product->setNewsFormDate("");
            $product->setNewsToDate("");
        }
        
        if (property_exists($productInfo, "GradoAlcolico")) {
            $product->setAvGradoAlcolico($productInfo->GradoAlcolico);
        }
        
        if (property_exists($productInfo, "IDRiconoscimenti")) {
            $this->_attributeApi->setProductAttribute($product, "av_riconoscimenti", $productInfo->IDRiconoscimenti );
        }
        if (property_exists($productInfo, "IDDenominazioni")) {
            $this->_attributeApi->setProductAttribute($product, "av_denominazioni", $productInfo->IDDenominazioni );
        }
        if (property_exists($productInfo, "IDVitigni")) {
            $this->_attributeApi->setProductAttribute($product, "av_vitigni", $productInfo->IDVitigni );
        }
        
        if (property_exists($productInfo, "PrezzoPromozione")) {
            // Imposto il prezzo speciale a partire da oggi            
            $_price = $productInfo->PrezzoPromozione + 0;
            if ($_price > 0) {
                $product->setSpecialPrice($productInfo->PrezzoPromozione + 0);
		// é sempre il flag promozione che comanda
                //$product->setAvPromozione(1); 
		if ($product->getAvPromozione() == 1) {
	                $product->setSpecialFromDate(Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        	        $product->getSpecialToDate("");
		}

            } else {
                $product->setSpecialPrice(null);
		// é sempre il flag promozione che comanda
                //$product->setAvPromozione(0); 
                $product->setSpecialFromDate("");
                $product->setSpecialToDate("");
            }
        }
} catch (Exception $e) {
    myLog($e, Zend_Log::ERR, "AzzurraWS_ERROR.log", true);  
    // Non faccio scattare l'errore, lo loggo e continuo 
    //throw new Exception($e->getMessage() . "\nArticolo ". $product->getSku());
}        
        try {
            $product->save();
        } catch (Excetion $e) {
            myLog("Errore in fase di creazione articolo " . $e->getMessage(), Zend_Log::ERR, "AzzurraWS_ERROR.log", true);
        }
        return;
    }
    
    public function selezionaArticolo ($productId) {
        
        $articoloInfo = null;        
        $id = $this->_getIdbyIdGestionale($productId);
        if (!is_null($id) && $id != "") {      
            $product = Mage::getModel("catalog/product")->Load($id);           
            if ($product->getId() > 0) {
                $articoloInfo = new ArticoloInfo();
                $articoloInfo->ID = (int)$productId;
                $articoloInfo->CodiceArticolo = $product->getSku();
                $articoloInfo->Descrizione = htmlspecialchars($product->getName());
                $articoloInfo->Scheda = htmlspecialchars($product->getDescription());                
                if (!$product->getImage()) {
                    $articoloInfo->Immagine1 = $product->getAvImg1();
                }
                if ($product->getSmallImage()) {
                    $articoloInfo->Immagine2 = $product->getAvImg1();
                }
                $articoloInfo->Um1 = $product->getAvUm1()."";
                $articoloInfo->Um2 = $product->getAvUm1()."";                
                $articoloInfo->IDProduttore = $this->_anagraficaApi->getProduttoreId($product->getManufacturer());
                
                $categoryList = $this->_categoryApi->getCategoryListByProduct($product);
                if (isset ($categoryList[1])) {
                    $articoloInfo->IDCategoria = $categoryList[1];
                }
                if (isset ($categoryList[2])) {
                    $articoloInfo->IDMerceologia = $categoryList[2];
                }
                if (isset ($categoryList[3])) {
                    $articoloInfo->IDSottoMerceologia = $categoryList[3];
                }                
                $articoloInfo->PrezzoUnitario = (int)$product->getPrice();
                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                $articoloInfo->QtaDisponibile = (int)$stock->getQty();
                $articoloInfo->QtaMaxOrdinabile =  (int)$product->getMaxSalesQty();
                if ($articoloInfo->QtaMaxOrdinabile == 0) {
                    $articoloInfo->QtaMaxOrdinabile = (int)MAge::getStoreConfig('cataloginventory/item_options/max_sale_qty');
                }
                $articoloInfo->FattoreConversione =  (int)(is_null($product->getAvFaconv()) || $product->getAvFaconv()== "")?0:$product->getAvFaconv();
                $articoloInfo->Promozione = $product->getAvPromozione();
                $articoloInfo->PrezzoPromozione = (int)$product->getFinalPrice();
                $articoloInfo->InEvidenza = ($product->getNewsToDate()."" != "")?0:1;
                $articoloInfo->IDRiconoscimenti = str_replace(",", MULTI_SELECT_ATTRIBUTE_SEPARATOR, is_null($product->getAvRiconoscimenti())?"":$product->getAvRiconoscimenti());
                $articoloInfo->GradoAlcolico = $product->getAvGradoAlcolico();
                $articoloInfo->IDDenominazioni = str_replace(",", MULTI_SELECT_ATTRIBUTE_SEPARATOR, is_null($product->getAvDenominazioni())?"":$product->getAvDenominazioni());
                $articoloInfo->IDVitigni = str_replace(",", MULTI_SELECT_ATTRIBUTE_SEPARATOR, is_null($product->getAvVitigni())?"":$product->getAvVitigni());
                $articoloInfo->Annata = $product->getAvAnno();
            }
        }

        return $articoloInfo;        
    }

    public function getList () {
        $retList = Array();
        $prod = Mage::getModel("catalog/product")->getCollection()
                    ->addAttributeToFilter("status", 1);
        foreach ($prod as $p) {
            $product = Mage::getModel("catalog/product")->Load($p->getId());
            if (!is_null($product->getAvIdproduct())) {
                $retList[] = $product->getAvIdproduct();
            }
        }
        return $retList;
    }
    
    public function aggiornaQta($idArticolo, $qty) {
        $product = Mage::getModel("catalog/product")->Load($this->_getIdbyIdGestionale($idArticolo));
        if ($product->getId() > 0) {
            $stock = array();
            $stock['qty'] = $qty;
            if ($qty > 0) 
                $stock['is_in_stock'] = 1;
            else
                $stock['is_in_stock'] = 0;
            $product->setStockData($stock);
            $product->save();
        }
    }
    
    public function eliminaArticolo ($productId) {      
        $product = Mage::getModel("catalog/product")->Load($this->_getIdbyIdGestionale($productId));
        if ($product->getId() > 0) {
            $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
            $product->save();
        }
    }
    
    public function caricaImmagine ($imgName, $imgBase64) {        
        $fileName = $this->_getFileName($imgName);
myLog($this->_tmpBaseDir .$fileName[0]);        
        file_put_contents($this->_tmpBaseDir  .$fileName[0], base64_decode($imgBase64));        
        $this->_mediaApi = Mage::getModel("catalog/product_attribute_media_api");
        $id = $this->_getIdbyImage($fileName);
        if ($id."" != "" ) {
            $this->_setImage($id, 1, $fileName, $imgBase64);
        } 
    }
    
    private function _setImage($id, $imgType, $imgFile, $imgBase64) {
        $product = Mage::getModel("catalog/product")->Load($id);
        $mediaGallery = $product->getMediaGalleryImages()->getItems();    

        foreach ($mediaGallery as $item) {
            if ($item->getPosition() == $imgType) {
                $currentFileName = $item->getFile();

                try {
                    $this->_mediaApi->remove($product->getId(), $currentFileName);
                } catch (Exception $e) {
                    myLog("Errore remove", Zend_Log::ERR, "AzzurraWS_ERROR.log", true);
                    myLog($e->getMessage(), Zend_Log::ERR, "AzzurraWS_ERROR.log", true);
                }
            }
        }

        try {

            $newFile = $this->_mediaApi
                            ->create($product->getId(), 
                                            array("file" => 
                                                    array("content" => $imgBase64, 
                                                          "mime" => $this->_imgFormat->getData($imgFile[1]),
                                                          "name" => $imgFile[0])));
            $this->_mediaApi->update($product->getId(), $newFile, array("label" => "", 
                                                                    "position" => $imgType, 
                                                                    "exclude" => false));
        
            $product = Mage::getModel("catalog/product")->Load($id);
            if ($imgType==1) {
                $product->setData("image",$newFile);
                $product->setData("small_image", $newFile);
                $product->setData("thumbnail", $newFile);
            }
            $product->Save();
            
        } catch (Exception $e) {
            myLog("Errore create", Zend_Log::ERR, "AzzurraWS_ERROR.log", true);
            myLog($e, Zend_Log::ERR, "AzzurraWS_ERROR.log", true);
        }
                        
    }
    
    private function _getFileName($imgName) {
        $fileName = array();
        $format = strtolower(substr($imgName, -3));

        if ($this->_imgFormat->getData($format)."" != "") {
            $tmpArr = explode("\\", $imgName);
            if (sizeof($tmpArr)) {                
                $fileName[0] = str_replace(" ", "", $tmpArr[sizeof($tmpArr)-1]);
                $fileName[1] = $format;
            }
            //Cancello i file temporanei
            $handle = opendir($this->_tmpBaseDir);
            while ($entry = readdir($handle)) {
                if ($entry != '.' &&  $entry != '..') {
                    $tmpFormat = substr($entry,-3);
                    if ($this->_imgFormat->getData($tmpFormat)."" != "") {

                       // unlink ($this->_tmpBaseDir . $entry);
                    }
                }
            }                   
        }
        return $fileName;
    }
    
    public function _getIdbyImage($imgName) {

        $id = "";    
        if (is_array($imgName) && isset($imgName[0])) {
	    $imageName = str_replace('.'.$imgName[1],'', $imgName[0]);
myLog("\Leggo l'articolo . " . $imageName); 
            $id = Mage::getModel("catalog/product")->getIdBySku($imageName);
            
        }
        return $id;
    }
    
    private function _getIdbyIdGestionale($productId) {
        //Recupero l'articolo in base al productId 
        $select = $this->_helper->getDbReader()->select()
                       ->from(array('ea' => Mage::getSingleton('core/resource')->getTableName('eav_attribute')))
                       ->join(array('pv' => Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar')),
                             'ea.attribute_id = pv.attribute_id')
                       ->where("ea.attribute_code = 'av_idproduct'")
                       ->where("pv.entity_type_id = 4")
                       ->where("pv.store_id = 0")
                       ->where("pv.value = '$productId'")
                       ->reset(Zend_Db_Select::COLUMNS)
                       ->columns(array('productId'   => 'pv.entity_id'));        
        return $this->_helper->getDbReader()->fetchOne($select);
    }  
  
}
?>
