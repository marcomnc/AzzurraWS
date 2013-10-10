<?php

class OrdineTestataInfo {

    /**
     * @var int
     */
    public $ID;
    /**
     * @var int
     */
    public $NumeroOrdine;
    /**
     * @var date
     */
    public $DataOrdine;    
    /**
     * @var int
     */
    public $CodiceCliente;
    /**
     * @var string
     */
    public $IDTransazione;
    /**
     * @var string
     */    
    public $Destinazione = "";
    /**
     *
     * @var string
     */
    public $IndirizzoDestinazione = "";
    /**
     * @var string
     */
    public $CAPDestinazione = "";
    /**
     * @var string
     */
    public $ComuneDestinazione = "";
    /**
     * @var string
     */
    public $ProvinciaDestinazione = "";
    /**
     * @var string
     */
    public $NazioneDestinazione = "";
    /**
     * @var date
     */
    public $OrdineLetto = "1900-01-01T00:00:00.00";
    /**
     * @var string
     */
    public $Stato = "";
    /**
     * @var string
     */
    public $IDSpeseSpedizione = "";
    /**
     * @var string
     */
    public $IDMetodoPagamento = "";
}

?>
