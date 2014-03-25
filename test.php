<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
  
define("MAGE_BASE_DIR", "..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR);

define("BASE_STORE", 1);
require_once(MAGE_BASE_DIR.'app/Mage.php'); //Path to Magento
umask(0);
Mage::app();
echo "<pre>";
error_reporting(E_ALL);
ini_set("display_errors",1 );

$imp = 12345.345;
$importo = Zend_Locale_Format::toNumber($imp, array('number_format' => '#0.00'));
//$importo = preg_replace("/./", ",", $importo);
echo $importo;
die();

?>
    
