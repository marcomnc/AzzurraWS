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

$categories = Mage::getModel('catalog/category')->getCollection();

/**
 * Category list
echo "<pre>";

foreach ($categories as $cat) {

    $category = Mage::getModel('catalog/category')->Load($cat->getId());

    echo '' . $category->getId() . ';';
    echo '' .$category->getName() . ';';
    echo '' .$category->getPosition() . ';';
    echo '' .$category->getLevel() . ';';
    echo '' .$category->getAvTipo() . ';';
    echo '' .$category->getAvCodice() . ';';
    echo '' .$category->getAvParent() . ';';
    echo "\n";

}
*/

/**
 * Prodotti senza immagini
foreach (Mage::getModel('catalog/product')->getCollection() as $prod) {
    $product = Mage::getModel('catalog/product')->Load($prod->getId());
    
    if ($product->getImage() == "" || $product->getImage() == "no_image")
        echo $product->getSku() . " - " . $product->getImage() . "<br>";
    
}
*/

$order_details = Mage::getModel('sales/order')->loadByIncrementId('100000061');
print_r($order_details->getdata());

foreach (Mage::getModel('customer/customer')->getCollection() as $cli) {
    
    $cliente = Mage::getModel('customer/customer')->Load($cli->getId());
    
    print_r($cliente->getData());
    $billing = Mage::getModel("customer/Address")->Load($cliente->getDefaultBilling());
    if (!is_null($billing)) {
	print_r($billing->getData());
    }
    
    
}

?>
    
