<?php
/**
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
<soap:Body>
<SelezionaCodiciArticoloResponse xmlns="http://keypass.it/azzurra/">
<SelezionaCodiciArticoloResult>
<int>59</int>
<int>684</int>
<int>384</int>
<int>229</int>
</SelezionaCodiciArticoloResult>
</SelezionaCodiciArticoloResponse>
</soap:Body>
</soap:Envelope>
 */
class XmlOutPut {
    //put your code here
    private $_xmlOpen = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body>';
    private $_xmlClose = '</soap:Body></soap:Envelope>';
    private $_response = Array();
    private $_result = Array();

    public function __construct() {
        
    }
        
    public function setResponse($response) {
        $this->_response['open'] = "<$response xmlns=\"http://keypass.it/azzurra/\">";
        $this->_response['close'] = "</$response>";
        $this->_response['4null'] = "<$response xmlns=\"http://keypass.it/azzurra/\" />";
    }
    
    public function setResult($result) {
        $this->_result['open'] = "<$result>";
        $this->_result['close'] = "</$result>";
    }
    
    
    public function ArrayOfInt ($arrInt) {
        return $this->_ArrayOf($arrInt, "int");
        
    }
    public function ArrayOfString ($arrString) {
        return $this->_ArrayOf($arrString, "string");
        
    }
    
    /**
     * Ritorna un oggetto semplice (non array). Se non passo argomenti prepara un
     * response null
     * 
     * @param type $obj
     * @return type 
     */
    public function SimpleObject($obj = null) {
        $_body = "";
        if (!is_null($obj)) {
            $_properties = get_object_vars($obj);
            foreach ($_properties as $k=>$v) {
                $_body .= "<$k>$v</$k>";
            }
        }
        return $this->_setOutPut(($_body));
    }

    public function ComplexObject($obj) {
        $_body = "";        
        foreach ($obj as $o) {
            $_body .= "<". get_class($o) . ">";
            $_properties = get_object_vars($o);
            foreach ($_properties as $k=>$v) {
                $_body .= "<$k>$v</$k>";
            }
            $_body .= "</". get_class($o) . ">";
        }
        
        return $this->_setOutPut(($_body));
    }
    
    private function _ArrayOf ($array, $type) {
        $_body = "";
        foreach ($array as $item) {
            $_body .= "<$type>$item</$type>";
        }
        return $this->_setOutPut(($_body));
    }
    
    private function _setOutPut($body) {
        $_out = "";
        $_out .= $this->_xmlOpen;        
        if (!is_null ($body) && $body != "") {
            $_out .= $this->_response['open'];
            $_out .= $this->_result['open'];
            $_out .= $body;        
            $_out .= $this->_result['close'];
            $_out .= $this->_response['close'];
        } else {
            $_out .= $this->_response['4null'];
        }
        
        $_out .= $this->_xmlClose; 
myLog($_out, Zend_Log::DEBUG, "", true);               
        return $_out;
        
    }
    
    public function setHeder() {
        header('Content-type: text/xml', true, 200);
        header('Cache-Control:private, max-age=0');
        header('Content-Type:text/xml; charset=utf-8');
	header('Content-Encoding: gzip');
        //header('Set-Cookie:.ASPXANONYMOUS=ztnn_EjozQEkAAAANWNmNDNiMDgtNjM0ZC00OGJkLThkZWEtZDUyZThiYWYwMzA10; expires=Tue, 01-Jan-2015 17:54:04 GMT; path=/; HttpOnly');
        header('Server:Microsoft-IIS/7.5');
        header('X-AspNet-Version:2.0.50727');
        header('X-Powered-By:ASP.NET');

    }
}

?>
