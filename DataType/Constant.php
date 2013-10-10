<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define ("PRODUTTORE", "manufacturer");
define ("CODICE_CLIENTE", "");
define ("CATEGORIA_BASE", "2");
define ("TIPO_CATEGORIA", "categoria");
define ("ROOT_CATEGORY", "2");
define ("ALL_CATEGORY", "551");
define ("TEMPORARY_CATEGORY", "Temporary");
define ("TIPO_MERCEOLOGIA", "merceologia");
define ("TIPO_SOTTOMERCEOLOGIA", "sottomerceologia");
define ("TAXABLE_GOODS","2");
define ("MULTI_SELECT_ATTRIBUTE_SEPARATOR", ";");


class ImageType extends Varien_Object {
    
    private $_imgFormat = array("jpg" => "image/jpeg", "bmp" => "imgage/bmp", "gif" => "image/gif", "png" => "image/png");

    public function __construct() {
        parent::__construct();
        
        foreach ($this->_imgFormat as $k=>$v) {
            $this->setData($k, $v);
        }
        
    }
    
}

?>
