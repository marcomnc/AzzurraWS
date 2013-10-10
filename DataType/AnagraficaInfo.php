<?php


class AnagraficaInfo {
    /**
     *
     * @var int
     */
    public $ID;
    /**
     *
     * @var string
     */
    public $Cognome;
    /**
     *
     * @var string
     */
    public $Nome;
    /**
     *
     * @var string
     */
    public $RagioneSociale;
    /**
     *
     * @var string
     */
    public $Riferimento;
    /**
     *
     * @var string
     */
    public $Indirizzo;
    /**
     *
     * @var string
     */
    public $CAP;
    /**
     *
     * @var string
     */
    public $Comune;
    /**
     *
     * @var string
     */
    public $Frazione;
    /**
     *
     * @var string
     */
    public $Provincia;
    /**
     *
     * @var string
     */
    public $Nazione;
    /**
     *
     * @var string
     */
    public $CodiceFiscale;
    /**
     *
     * @var string
     */
    public $PartitaIVA;
    /**
     *
     * @var string
     */
    public $Mail;
    /**
     *
     * @var string
     */
    public $Telefono;
    /**
     *
     * @var string
     */
    public $Fax;
    /**
     *
     * @var int;
     */
    public $Tipo = 0;
}


class AnagraficaInfoProduttoreInfo extends AnagraficaInfo {

    public function __construct() {
        $this->IDAnagrafica = 0;
        $this->Codice = "";
        $this->NomeProduttore = "";
        $this->Descrizione = "";
    }    
}

class AnagraficaInfoClienteInfo extends AnagraficaInfo {

    public function __construct() {
        $this->Tipo = 2;
        $this->Codice = "";
        $this->IDAnagrafica = 0;
    }    
}

?>
