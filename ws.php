<?php
 
ini_set("soap.wsdl_cache_enabled", 0); 
define("MAGE_BASE_DIR", "..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR);
define("BASE_STORE", 1);

require_once(MAGE_BASE_DIR.'app/Mage.php'); //Path to Magento
umask(0);
Mage::app();
error_reporting(E_ALL);
ini_set("log_errors", 1); 
ini_set("error_log", __DIR__ . DIRECTORY_SEPARATOR ."error.log");

$_myrequest = file_get_contents('php://input');
if ($_myrequest."" != "") {
	myLog(getallheaders(), Zend_Log::DEBUG, "AzzurraWS.log", true);
	myLog($_myrequest, Zend_Log::DEBUG, "AzzurraWS.log", true);
	//myLog($_SESSION, Zend_Log::DEBUG, "AzzurraWS.log", true);	
	//myLog($_SERVER, Zend_Log::DEBUG, "AzzurraWS.log", true);
}

require_once("Zend/Soap/Server.php");
require_once("Zend/Soap/Wsdl.php");
require_once("Zend/Soap/Wsdl/Strategy/ArrayOfTypeSequence.php");
require_once("Zend/Soap/Wsdl/Strategy/ArrayOfTypeComplex.php");
require_once("Zend/Soap/AutoDiscover.php");

require_once("DataType/ArticoloInfo.php");
require_once("DataType/ArrayOfInt.php");
require_once("DataType/ProduttoreInfo.php");
require_once("DataType/AnagraficaInfo.php");
require_once("DataType/OrdineTestataInfo.php");
require_once("DataType/OrdineRigaInfo.php");
require_once("DataType/CategoriaInfo.php");
require_once("DataType/Constant.php");

require_once("Lib/Db/DbAPI.php");
require_once("Lib/V2/ImportAPI.php");

require_once("Lib/ProductAPIAsync.php");

require_once("Lib/ProductApi.php");
require_once("Lib/AttributeApi.php");
require_once("Lib/CategoryApi.php");
require_once("Lib/SalesApi.php");
require_once("Lib/AnagraficheAPI.php");
require_once("Lib/XmlOutPut.php");
require_once("Lib/WSDL.php");

/**
 * Gestione dei WS per Azzurra Vini
 * Per l'utilizzo di questi WS è ncessario creare i seguenti attributi:
 * Prodotti
 * av_idproduct -> Id del prodotto presente nel gestionale
 * 
 */
class AzzurraWebServiceSoap {

    /**
     * mncTest
     * 
     * @return string
     */
    public function mncTest() {
        $str = "hello World";             
        return $str;
    }

    /**
     * Seleziona un articolo
     * @param int $productId
     * @return ArticoloInfo 
     */
    public function SelezionaArticolo($productId) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);       
        $api = new ProductApi();
        $productInfo = $api->selezionaArticolo($productId->ArticoloId);        
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject($productInfo);
        die();
    }

    /**
     * Inserisco l'articolo
     * 
     * @param ArticoloInfo $articolo 
     */
    public function InserisciArticolo($articolo) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);
        $api = new ProductApi();        
        $api->inserisciArticolo($articolo->articolo);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();

    }
    
    /**
     * Aggiorono un articolo l'articolo
     * 
     * @param ArticoloInfo $articolo 
     */
    public function AggiornaArticolo($articolo) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $api = new ProductAPIAsync();        
        $api->inserisciArticolo($articolo->articolo);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }
    
    /**
     * Aggirona la quantità di un articolo
     * 
     * @param int $idArticolo
     * @param int $nuovaQta 
     */
    public function AggiornaQtaDisponibileArticolo($idArticolo) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $api = new ProductApi();
        $api->aggiornaQta($idArticolo->idArticolo, $idArticolo->nuovaQta);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }
    
    /**
     * Imposto l'articolo come annullato
     * 
     * @param type $idArticolo 
     */
    public function EliminaArticolo($ArticoloId) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);
        $api = new ProductAPIAsync();
        $api->eliminaArticolo($ArticoloId->ArticoloId);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }
    
    /**
     * Elenco Ordini
     * 
     * @return ArrayOfInt 
     */
    public function SelezionaOrdiniNuovi() {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $hlp = new SalesApi();
        $listaOrdini = $hlp->getList();
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->ArrayOfInt($listaOrdini);
        die();

    }
    /**
     * Ritorno la testata dell'ordine
     * 
     * @param Int $idOrdine 
     * @return OrdineTestataInfo
     */
    public function SelezionaOrdineTestata($idOrdine) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $hlp = new SalesApi();
        $testataInfo = $hlp->getTestata($idOrdine->idOrdine, false);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject($testataInfo);
        die();        
    }
    
     /**
     * Ritorno la testata dell'ordine
     * 
     * @param Int $idOrdine 
     * @return OrdineRigaInfo
     */
    public function SelezionaOrdineRighe($idOrdine) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $hlp = new SalesApi();
        $rows = $hlp->getRighe($idOrdine->idOrdine);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->ComplexObject($rows);
        die();        
    }    
    
    /**
     * Imposta gli ordini come già scaricati
     * 
     * @param type $elencoOrdini 
     * @return null
     */
    public function ImpostaOrdiniLetti($elencoOrdini) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $hlp = new SalesApi();
        $orderList = array();
        if (!is_array($elencoOrdini->elencoOrdini->int)) {
                $orderList[] = $elencoOrdini->elencoOrdini->int;
        } else {
                $orderList = $elencoOrdini->elencoOrdini->int;
        }
        foreach ($orderList as $order) {
            $hlp->setLetti($order);        
        }     
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();        
    }
    
    /**
     * Per ora non aggiorno nulla
     * 
     * @param OrdineTestataInfo $ordineTestata 
     * @return null
     */
    public function AggiornaOrdine($ordineTestata) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
myLog($ordineTestata, Zend_Log::DEBUG, "AzzurraWS.log", true);
        
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();        
    }
    
    /**
     * Lista degli articoli caricati     
     * @return int[]
     */
    public function SelezionaCodiciArticolo() {     
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $retList = array();
        $prod = Mage::getModel("catalog/product")->getCollection()
                    ->addAttributeToFilter("status", 1);
        foreach ($prod as $p) {
            $product = Mage::getModel("catalog/product")->Load($p->getId());
            if (!is_null($product->getAvIdproduct())) {
                $retList[] = $product->getAvIdproduct();
            }
        }
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->ArrayOfInt($retList);
        die();
    }
    
    public function SelezionaCodiciProduttore () {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $api = new AnagraficheAPI();
        $retVal = $api->selezionaCodiciProduttore();
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->ArrayOfString($retVal);
        die();
        
    }
    
    /**
     * Elimino il porduttore
     * 
     * @param string $Codice 
     */
    public function EliminaProduttore($Codice) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $api = new AnagraficheAPI();
        $api->removeProduttore($Codice->Codice);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();        
    }
    
    /**
     * Aggiorna l'anagrafica produttore
     * 
     * @param ProduttoreInfo
     * @return null
     */
    public function AggiornaProduttore ($produttore) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $this->InserisciProduttore($produttore);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }
    
    /**
     * Creo un nuovo produttore
     * 
     * @param ProduttoreInfo $produttore
     */
    public function InserisciProduttore($produttore) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);
        $api = new AnagraficheAPI();
        if (!property_exists($produttore->produttore, "Name")) {
            $produttore->produttore->Name = "";
        }
        if (!property_exists($produttore->produttore, "Descrizione")) {
            $produttore->produttore->Descrizione = "";
        }   
        if (!property_exists($produttore->produttore, "Immagine")) {
            $produttore->produttore->Immagine = "";
        }
        if (!property_exists($produttore->produttore, "ImmagineBase64")) {
            $produttore->produttore->ImmagineBase64 = "";
        }            
        $api->addProduttore($produttore->produttore);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }
    
    /**
     * Ritorna un produttore
     * 
     * @param type $codice
     * @return ProduttoreInfo 
     */
    public function SelezionaProduttore($Codice) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $api = new AnagraficheAPI();
        $produttoreInfo = $api->selezionaProduttore($Codice->Codice);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject($produttoreInfo);
        die();
        
    }
    
    /**
     * Lista dei clienti caricati nel sistema
     * 
     * @return ArrayOfInt
     */
    public function SelezionaCodiciCliente () {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $retList = array();
        $clienti = Mage::getModel("customer/customer")->getCollection();
        foreach ($clienti as $cli) {
            $retList[] = $cli->getId();            
        }
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->ArrayOfInt($retList);
        die();
    }
    /**
     * Ritorna l'anagrfica del cliente
     * 
     * @param string $ClienteId 
     */
    public function SelezionaCliente($ClienteId) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $api = new AnagraficheAPI();
        $cliente = $api->selezionaCliente($ClienteId->ClienteId);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject($cliente);
        die();
    }
    
    /**
     * Iponsta l'anagrafica cliente come non attiva
     * 
     * @param int $ClienteId
     * @return null
     */
    public function EliminaCliente($ClienteId) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $cliente = Mage::getModel("customer/customer")->Load($ClienteId->ClienteId);
        if (!is_null($cliente)) {
            //@todo non funziona
            $cliente->setIsActive("0");   
            $cliente->Save();
        }
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }
    
    /**
     * Aggiornamento anagrafica cliente. Per ora aggiorno solo il codice del gestionale
     * 
     * @param AnagraficaInfoClienteInfo $ClienteInfo 
     * @return null
     */
    public function AggiornaCliente($ClienteInfo) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        myLog("@todo Aggiornamento cliente", Zend_Log::DEBUG, "AzzurraWS.log", true);
        myLog($ClienteInfo, Zend_Log::DEBUG, "AzzurraWS.log", true);
        if (property_exists($ClienteInfo->cliente, "IDAnagrafica") && isset($ClienteInfo->cliente->IDAnagrafica)) {            
            $cliente = Mage::getModel("customer/customer")->Load($ClienteInfo->cliente->IDAnagrafica);
            if (!is_null($cliente)) {
                $cliente->setAvCodice($ClienteInfo->cliente->Codice);
                $cliente->Save();
            }
        }
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }
    
    /**
     * Ritorna i codici categoria
     * 
     * @return ArrayOfInt 
     */
    public function SelezionaCodiciCategoria() {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);
        $hlp = new CategoryApi();
        $listaOrdini = $hlp->selezionaCodiciCategoria();
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->ArrayOfInt($listaOrdini);
        die();        
    }
    
    /**
     * Ritorna la categoria selezionata
     * 
     * @param int $CategoriaId
     * @return CategoriaInfo
     */
    public function SelezionaCategoria($CategoriaId) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);
        $hlp = new CategoryApi();
        $categoriaInfo = $hlp->selezionaCategoria($CategoriaId->CategoriaId);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject($categoriaInfo);
        die();        
    }
    
    /**
     * Cancello la categoria. La categoria non viene eliminata, ma messa 
     * non attiva
     * 
     * @param int $CategoriaId 
     */
    public function EliminaCategoria($CategoriaId) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);
        $hlp = new CategoryApi();
        $hlp->eliminaCategoria($CategoriaId->CategoriaId, TIPO_CATEGORIA);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }
    
    /**
     * Inserisce una categoria. 
     * 
     * @param CategoriaInfo $categoria
     * @return null
     */
    public function InserisciCategoria($categoria) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);
        $hlp = new CategoryApi();
        $hlp->inserisciCategoria($categoria->categoria);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }
    
    /**
     * Ritorna i codici Merceologia
     * 
     * @return ArrayOfInt 
     */
    public function SelezionaCodiciMerceologia() {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $hlp = new CategoryApi();
        $listaMerceologia = $hlp->selezionaCodiciMerceologia();
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->ArrayOfInt($listaMerceologia);
        die();        
    }
    
    /**
     * Ritorna la categoria selezionata
     * 
     * @param int $CategoriaId
     * @return CategoriaInfo
     */
    public function SelezionaMerceologia($MerceologiaId) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $hlp = new CategoryApi();
        $categoriaInfo = $hlp->selezionaCategoria($MerceologiaId->MerceologiaId, TIPO_MERCEOLOGIA);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject($categoriaInfo);
        die();        
    }
    
    /**
     * Cancello la categoria. La categoria non viene eliminata, ma messa 
     * non attiva
     * 
     * @param int $MerceologiaId 
     */
    public function EliminaMerceologia($MerceologiaId) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $hlp = new CategoryApi();
        $hlp->eliminaCategoria($MerceologiaId->MerceologiaId, TIPO_MERCEOLOGIA);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }
    
    /**
     * Inserisce una categoria. 
     * 
     * @param CategoriaInfo $merceologia
     * @return null
     */
    public function InserisciMerceologia ($merceologia) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);        
        $hlp = new CategoryApi();  
        $hlp->inserisciCategoria($merceologia->merceologia, TIPO_MERCEOLOGIA);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    }    
    
    /**
     *
     * @param string $NomeFileImmagine
     * @param string $DatiImmagineInBase64 
     */
    public function CaricaImmagine($NomeFileImmagine, $DatiImmagineInBase64) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);
//        $hlp = new ProductApi();   
//        
//        $hlp->caricaImmagine($NomeFileImmagine->NomeFileImmagine, $NomeFileImmagine->DatiImmagineInBase64);
        
        $hlp = new ProductAPIAsync();   
        $hlp->caricaImmagine($NomeFileImmagine->NomeFileImmagine, $NomeFileImmagine->DatiImmagineInBase64);
        
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
      
        
    }
    
    public function AggiornaTabellaGenerica($tabellaGenerica) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);
        $hlp = new AttributeApi();
        $hlp->AggiornaTabellaGenerica($tabellaGenerica);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();

    }
    
    public function StartImport() {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);

        $hlp = new ImportAPI();
        $uid = $hlp->Start();
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleType($uid);
        die();

    }
    
    public function StopImport($Uid) {
myLog("enter in " . __FUNCTION__, Zend_Log::DEBUG, "AzzurraWS.log", true);


        $hlp = new ImportAPI();
        $hlp->Stop($Uid->ImportId);
        $arr = new XmlOutPut;
        $arr->setResponse(__FUNCTION__."Response");
        $arr->setResult(__FUNCTION__."Result");
        $arr->setHeder();
        echo $arr->SimpleObject();
        die();
    	
    }
}

function myLog($string, $type, $file = "", $force = true) {
    
    if (Zend_Log::ERR == $type) {
        Mage::Log($string, $type, "AzzurraWS-ERROR-". date("ymd") .".log", true);
    } else {
        Mage::Log($string, $type, "AzzurraWS-". date("ymd") .".log", true);
    }
    
}

//ini_set('zlib.output_compression','On');

	
if ( isset($_GET['wsdl']) && $_GET['wsdl'] == 'ori') {
    $autodiscover = new Zend_Soap_AutoDiscover('Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence');
    $autodiscover->setBindingStyle(array('style'=>'document'));
    $autodiscover->setOperationBodyStyle(array('use' => 'literal'));
    $autodiscover->setClass("AzzurraWebServiceSoap");
    $autodiscover->handle();    
} elseif ( isset($_GET['wsdl']) && $_GET['wsdl'] == 'info') {
    phpinfo();
} elseif (isset($_GET['wsdl']) && $_myrequest."" == "") {
    header('Content-type: text/xml', true, 200);    
    $wsdl = new WSDL; 
       
    echo $wsdl->getWSDL();
    die();
} else {
    DbAPI::dbInit();
    $soap = new Zend_Soap_Server(WSDL::getBaseUri() . '/ws.php?wsdl=1', array('cache_wsdl' => false));
    $soap->setSoapVersion(SOAP_1_2);
    $soap->setClass("AzzurraWebServiceSoap");
    $soap->handle();
}
?>