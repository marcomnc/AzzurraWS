<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Debug
 *
 * @author marcoma
 */
class Debug {
    //put your code here
    const MAIL_ADDRESS_TO_NOTIFY = "marco@mancinellimarco.it";   
    
    protected static $_date;
    protected static $_session;
    protected static $_name;    

    public function __construct() {
        $this->_setName();
    }
    
    protected function _setName() {
        if ($this->_session != $_SERVER["session_id"]) {
            $this->_session = $_SERVER["session_id"];
        }
        if ($this->_date != date('Y-m-d')) {
            $this->_date = date('Y-m-d');
        }
        $this->_name = "AzzurraWS_" . $this->_date . "_" . $this->_session . ".log";
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function  Log($mixed, $type=Zend_log::DEBUG) {
        myLog($mixed, $type, $this->_name, true);
        if ($type == Zend_Log::DEBUG) {            
            mail(self::MAIL_ADDRESS_TO_NOTIFY,"WS AZZURRA ERRORE!",print_r($mixed, true),'From: root@mcgroup.endesis.com');
        }
    }
    
}

?>
