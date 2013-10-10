<?php

class CategoryApi {
    private $_helper;
    private $_temporary = null;
    private $_typeTree = array();
    
    public function __construct() {
        $this->_helper = Mage::Helper("autelcatalog/product");
        $this->_typeTree = array(TIPO_CATEGORIA => "root",
                                 TIPO_MERCEOLOGIA => TIPO_CATEGORIA,
                                 TIPO_SOTTOMERCEOLOGIA => TIPO_MERCEOLOGIA);
        $tempCat = MAge::getModel("catalog/category")->getCollection()
                        ->AddAttributeToFilter("name", TEMPORARY_CATEGORY)
                        ->getFirstItem();
        if ($tempCat->getId() > 0) {
            $this->_temporary = Mage::getModel("catalog/category")->Load($tempCat->getId());
        } else {
            $this->_temporary = new Mage_Catalog_Model_Category();
            $this->_temporary->setStoreId(0);
            $this->_temporary->setAttributeSetId($this->_temporary->getDefaultAttributeSetId());                 
            $this->_temporary->setParent(1);
            $this->_temporary->setLevel(1);
            $this->_temporary->setPath(1);
            $this->_temporary->setIncludeInMenu(0);
            $this->_temporary->setName(TEMPORARY_CATEGORY);
            $this->_temporary->setDescription(TEMPORARY_CATEGORY);
            $this->_temporary->setIsActive(1);
            try {
                $this->_temporary->Save();
                $tempCat = MAge::getModel("catalog/category")->getCollection()
                            ->AddAttributeToFilter("name", TEMPORARY_CATEGORY)
                            ->getFirstItem();
                $this->_temporary = Mage::getModel("catalog/category")->Load($tempCat->getId());
            } catch (Exception $e) {
                throw new Exception('Errore in fase di creazione della categoria temporanea '. $e->getMEssage());
            }
        }
                

    }
    
    /**
     * Inserisce una categoria. Se la categoria già esiste l'aggiorna, altrimenti
     * crea una nuova categoria sotto la root. Sarà poi l'inserimento degli articoli 
     * ad aggironarle
     * 
     * @param CategoriaInfo $idCategoria
     * @param null $type 
     */
    public function inserisciCategoria($categoria, $type = TIPO_CATEGORIA) {        
        if (property_exists($categoria, "ID") && $categoria->ID."" != "") {
            $cat = Mage::getModel("catalog/category")->getCollection()
                    ->AddAttributeToFilter("av_codice", $categoria->ID)
                    ->AddAttributeToFilter("av_tipo", $type)
                    ->getFirstItem();            
            if ($cat->getId() > 0) {
                $myCat = Mage::getModel("catalog/category")->Load($cat->getId());                
            } else {     
                $myCat = new Mage_Catalog_Model_Category();
                $myCat->setStoreId(0);
                $myCat->setAttributeSetId($myCat->getDefaultAttributeSetId());
                $myCat->setPath(implode('/',$this->_temporary->getPathIds()));                          
                $myCat->setParent($this->_temporary->getId());
                $myCat->setIncludeInMenu(0);
            };
            $myCat->setName($categoria->Nome);
            $myCat->setDescription($categoria->Descrizione);
            $myCat->setAvCodice($categoria->ID);
            $myCat->setAvTipo($type);
            $myCat->setIsActive(1); 
            $myCat->Save();
        }
     }

    public function selezionaCodiciCategoria () {
        return $this->_selezionaCodici(TIPO_CATEGORIA);
    }
    
    public function selezionaCodiciMerceologia () {
        return $this->_selezionaCodici(TIPO_MERCEOLOGIA);        
    }
    
    public function selezionaCategoria($idCategoria, $type = TIPO_CATEGORIA) {
        $categoriaInfo = null;
        $catCollection = Mage::getModel("catalog/category")->getCollection()
                                ->AddAttributeToFilter("av_codice", $idCategoria)
                                ->AddAttributeToFilter("av_tipo", $type)
                                ->AddAttributeToFilter("is_active", 1);
        foreach ($catCollection as $cat) {
            $myCat = Mage::getModel("catalog/category")->Load($cat->getId());
            $categoriaInfo = new CategoriaInfo();
            $categoriaInfo->ID = $myCat->getAvCodice();
            $categoriaInfo->Nome = $myCat->getDescription();
            $categoriaInfo->Descrizione = $myCat->getDescription();
            if ($myCat->getAvParent().""!="") {
                $categoriaInfo->ParentId = $myCat->getAvParent();   
            } 
            break;
        }
        return $categoriaInfo;
    }
    
    public function eliminaCategoria($idCategoria, $type = TIPO_CATEGORIA) {        
        
        if ($idCategoria."" != "") {
            $catCollection = Mage::getModel("catalog/category")->getCollection()
                                    ->AddAttributeToFilter("av_codice", $idCategoria)
                                    ->AddAttributeToFilter("av_tipo", $type);
            foreach ($catCollection as $cat) {
            	$myCat = Mage::getModel("catalog/category")->Load($cat->getId());
                $myCat->setIsActive(0);                
                $myCat->Save();
                break;
            }
        }
    }
    
    public function getCategoryListByProduct($product) {
        $catList = array();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $category = Mage::getModel("catalog/category")->getCollection()
                            ->AddAttributeToFilter("entity_id", array("IN", $product->getCategoryIds()))
                            ->AddAttributeToSort("level");
            foreach ($category as $cat) {
                $myCat = Mage::getModel("catalog/category")->Load($cat->getId());
                if ($myCat->getId() != ROOT_CATEGORY && $myCat->getAvCodice()) {
                    $catList[$myCat->getLevel()-1] = $myCat->getAvCodice();
                }
            }
        }
        return $catList;
    }
    
    /**
     * Recupero le categoria da assegnare ad un articolo.
     * inoltre sposto le categorie in modo da creare la struttra ad albero corretta
     * se l'attuale struttra non corrisponde all'albero delle categorie dell'articolo 
     * genero un errore
     * 
     * @param type $category 
     */
    public function getCategoryForProducts($category) {
        $categoryList = array();
        $categoryList[] = ROOT_CATEGORY; // a priori
        $categoryList[] = ALL_CATEGORY; // a priori

        if (!is_null($category["categoria"])) {
            $myCat = $this->_processCategory($category["categoria"], ROOT_CATEGORY, TIPO_CATEGORIA);
            if ($myCat !== false) {
                $categoryList[] = $myCat;
            }
            if (!is_null($category["merceologia"]) && $category["categoria"] != $category["merceologia"]) {
                $myCat = $this->_processCategory($category["merceologia"], $category["categoria"], TIPO_MERCEOLOGIA);
                if ($myCat !== false) {
                    $categoryList[] = $myCat;
                } 
                if (!is_null($category["sottomerceologia"]) && $category["sottomerceologia"] != $category["merceologia"]) {
                    $myCat = $this->_processCategory($category["sottomerceologia"], $category["merceologia"], TIPO_SOTTOMERCEOLOGIA);
                    if ($myCat !== false) {
                        $categoryList[] = $myCat;
                    }
                }
            }
        }
        return $categoryList;
    }
    
    private function _processCategory($category, $parent, $type) {
        $retValue = false;
        $myCat = Mage::getModel("catalog/category")->getCollection()
                        ->AddAttributeToFilter("av_codice", $category)
                        ->AddAttributeToFilter("av_tipo", ($type==TIPO_SOTTOMERCEOLOGIA)?TIPO_MERCEOLOGIA:$type)
                        ->getFirstItem();        
        
        if ($myCat->getId() > ROOT_CATEGORY) { //Se passo la root è un casino
            
            if ($type == TIPO_CATEGORIA) {
                $myParent = Mage::getModel("catalog/category")->Load($parent);
            } else {
                $myParent = Mage::getModel("catalog/category")->getCollection()
                                ->AddAttributeToFilter("av_codice", $parent)
                                ->AddAttributeToFilter("av_tipo", $this->_typeTree[$type])
                                ->getFirstItem();
            }

            $retValue = $myCat->getId();
            if ($this->_isTemporaryCategory($myCat->getId()) && $myParent->getId() > 1) {
                //Sposto la categoria
                $myCat->move($myParent->getId());
            } else {
                if ($myCat->getParentId() != $myParent->getId()) {
                	myLog("Albero delle categorie non corrette [$category][$parent][$type]", Zend_Log::ERR, "AzzurraWS_ERROR.log", true);
                }
            }
        }
        return $retValue;
    }
    
    private function _isTemporaryCategory($id) {
        $parent = Mage::getModel("catalog/category")->Load($id);
        return ($parent->getParentId() == $this->_temporary->getId())?true:false;
    }
        
    private function _selezionaCodici ($tipo) {
        $retVal = array();
        $listaCat = Mage::getModel("catalog/category")->getCollection()
                        ->AddAttributeToFilter("av_tipo", $tipo)
                        ->AddAttributeToFilter("is_active",1);
        foreach ($listaCat as $cat) {
            $category=Mage::getModel("catalog/category")->Load($cat->getId());
            $retVal[] = $category->getAvCodice();
        }      
        return $retVal;
    }
    
}

?>
