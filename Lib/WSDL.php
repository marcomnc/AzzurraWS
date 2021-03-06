<?php

class WSDL {
    
    //const NAME_SPACE = "http://www.azzurra.it/externalWs/AzzurraWS";
	const NAME_SPACE =  "http://keypass.it/azzurra";
    
//put your code here
    private $_wsdl = '';
    private $_filename = '';
    
    public function __construct() {    	
        $this->_filename = __DIR__ . '/../Data/azzurra.wsdl';
    }
    
    public function getWSDL() {
        if ($this->_wsdl == "") {
            $this->_setWSDLfromFile();
            //$this->_setWSDL();
        }

        return $this->_wsdl;
    }

		public static function getBaseUri() {
			
			$protocols = preg_split("/\//", $_SERVER['SERVER_PROTOCOL']);

			$protocol = "http";
			if (isset($protocols[0])) {
				$protocol = strtolower($protocols[0]);	
			}
			
			$scripts = preg_split("/\//", $_SERVER['REQUEST_URI']);
                         $baseUri = "";
			for ($i = 0; $i < sizeof($scripts)-1; $i++) {
				if ($scripts[$i] != "")
					$baseUri .= "/" . $scripts[$i];
			}
			
			$baseUri = "$protocol://". $_SERVER['HTTP_HOST'] . $baseUri;
			
			return $baseUri;
		}

		private function _setWSDLfromFile() {

			$wsdl = file_get_contents($this->_filename);
			
			$baseUri = self::getBaseUri();
                        $baseNs = self::NAME_SPACE;
                        //$baseNs = self::getBaseUri();

			$this->_wsdl = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . str_replace("#base_uri#", $baseUri, str_replace("#base_ns#", $baseNs, $wsdl));			
		}
    
    private function _setWSDL () {
        $this->_wsdl = '<?xml version="1.0" encoding="utf-8"?>
<wsdl:definitions xmlns:http="http://schemas.xmlsoap.org/wsdl/http/" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/" xmlns:tns="http://keypass.it/azzurra/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:tm="http://microsoft.com/wsdl/mime/textMatching/" xmlns:s="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/" targetNamespace="http://keypass.it/azzurra/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">
  <wsdl:types>
    <s:schema elementFormDefault="qualified" targetNamespace="http://keypass.it/azzurra/">
      <s:element name="SelezionaArticolo">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="ArticoloId" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="SelezionaArticoloResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaArticoloResult" type="tns:ArticoloInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:complexType name="ArticoloInfo">
        <s:complexContent mixed="false">
          <s:extension base="tns:BaseInfo">
            <s:sequence>
              <s:element minOccurs="1" maxOccurs="1" name="ID" type="s:int" />
              <s:element minOccurs="0" maxOccurs="1" name="CodiceArticolo" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Descrizione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Scheda" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Immagine1" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Immagine2" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Um1" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Um2" type="s:string" />
              <s:element minOccurs="1" maxOccurs="1" name="IDProduttore" type="s:int" />
              <s:element minOccurs="1" maxOccurs="1" name="IDCategoria" type="s:int" />
              <s:element minOccurs="1" maxOccurs="1" name="IDMerceologia" type="s:int" />
              <s:element minOccurs="1" maxOccurs="1" name="IDSottoMerceologia" type="s:int" />
              <s:element minOccurs="1" maxOccurs="1" name="PrezzoUnitario" type="s:double" />
              <s:element minOccurs="1" maxOccurs="1" name="QtaDisponibile" type="s:int" />
              <s:element minOccurs="1" maxOccurs="1" name="QtaMaxOrdinabile" type="s:int" />
              <s:element minOccurs="1" maxOccurs="1" name="FattoreConversione" type="s:double" />
              <s:element minOccurs="0" maxOccurs="1" name="Promozione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="PrezzoPromozione" type="s:double" />
              <s:element minOccurs="0" maxOccurs="1" name="InEvidenza" type="s:int" />
              <s:element minOccurs="0" maxOccurs="1" name="IDRiconoscimenti" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="GradoAlcolico" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="IDDenominazioni" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="IDVitigni" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Annata" type="s:string" />
            </s:sequence>
          </s:extension>
        </s:complexContent>
      </s:complexType>
      <s:complexType name="BaseInfo" />
      <s:element name="EliminaArticolo">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="ArticoloId" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="EliminaArticoloResponse">
        <s:complexType />
      </s:element>
      <s:element name="InserisciArticolo">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="articolo" type="tns:ArticoloInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="InserisciArticoloResponse">
        <s:complexType />
      </s:element>
      <s:element name="AggiornaArticolo">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="articolo" type="tns:ArticoloInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="AggiornaArticoloResponse">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCodiciArticolo">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCodiciArticoloResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaCodiciArticoloResult" type="tns:ArrayOfInt" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:complexType name="ArrayOfInt">
        <s:sequence>
          <s:element minOccurs="0" maxOccurs="unbounded" name="int" type="s:int" />
        </s:sequence>
      </s:complexType>
      <s:element name="AggiornaQtaDisponibileArticolo">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="idArticolo" type="s:int" />
            <s:element minOccurs="1" maxOccurs="1" name="nuovaQta" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="AggiornaQtaDisponibileArticoloResponse">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCliente">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="ClienteId" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="SelezionaClienteResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaClienteResult" type="tns:ClienteInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:complexType name="ClienteInfo">
        <s:complexContent mixed="false">
          <s:extension base="tns:AnagraficaInfo">
            <s:sequence>
              <s:element minOccurs="0" maxOccurs="1" name="Codice" type="s:string" />
              <s:element minOccurs="1" maxOccurs="1" name="IDAnagrafica" type="s:int" />
            </s:sequence>
          </s:extension>
        </s:complexContent>
      </s:complexType>
      <s:complexType name="AnagraficaInfo">
        <s:complexContent mixed="false">
          <s:extension base="tns:BaseInfo">
            <s:sequence>
              <s:element minOccurs="1" maxOccurs="1" name="ID" type="s:int" />
              <s:element minOccurs="0" maxOccurs="1" name="Cognome" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Nome" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="RagioneSociale" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Riferimento" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Indirizzo" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="CAP" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Comune" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Frazione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Provincia" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Nazione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="CodiceFiscale" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="PartitaIVA" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Mail" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Telefono" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Fax" type="s:string" />
              <s:element minOccurs="1" maxOccurs="1" name="Tipo" type="s:int" />
            </s:sequence>
          </s:extension>
        </s:complexContent>
      </s:complexType>
      <s:element name="EliminaCliente">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="ClienteId" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="EliminaClienteResponse">
        <s:complexType />
      </s:element>
      <s:element name="AggiornaCliente">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="cliente" type="tns:ClienteInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="AggiornaClienteResponse">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCodiciCliente">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCodiciClienteResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaCodiciClienteResult" type="tns:ArrayOfInt" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="SelezionaProduttore">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="Codice" type="s:string" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="SelezionaProduttoreResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaProduttoreResult" type="tns:ProduttoreInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:complexType name="ProduttoreInfo">
        <s:complexContent mixed="false">
          <s:extension base="tns:AnagraficaInfo">
            <s:sequence>
              <s:element minOccurs="1" maxOccurs="1" name="IDAnagrafica" type="s:int" />
              <s:element minOccurs="0" maxOccurs="1" name="Codice" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="NomeProduttore" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Descrizione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Immagine" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="ImmagineBase64" type="s:string" />
            </s:sequence>
          </s:extension>
        </s:complexContent>
      </s:complexType>
      <s:element name="EliminaProduttore">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="Codice" type="s:string" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="EliminaProduttoreResponse">
        <s:complexType />
      </s:element>
      <s:element name="InserisciProduttore">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="produttore" type="tns:ProduttoreInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="InserisciProduttoreResponse">
        <s:complexType />
      </s:element>
      <s:element name="AggiornaTabellaGenerica">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="produttore" type="tns:TabellaGenerica" />
          </s:sequence>
        </s:complexType>          
      </s:element>      
      <s:element name="AggiornaTabellaGenericaResponse">
        <s:complexType />
      </s:element>
      <s:complexType name="TabellaGenerica">
        <s:complexContent mixed="false">
          <s:extension base="tns:BaseInfo">
            <s:sequence>
              <s:element minOccurs="1" maxOccurs="1" name="IDTabella" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Codice" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Descrizione" type="s:string" />              
            </s:sequence>          
          </s:extension>
        </s:complexContent>
      </s:complexType>
      <s:element name="AggiornaProduttore">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="produttore" type="tns:ProduttoreInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="AggiornaProduttoreResponse">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCodiciProduttore">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCodiciProduttoreResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaCodiciProduttoreResult" type="tns:ArrayOfString" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:complexType name="ArrayOfString">
        <s:sequence>
          <s:element minOccurs="0" maxOccurs="unbounded" name="string" nillable="true" type="s:string" />
        </s:sequence>
      </s:complexType>
      <s:element name="SelezionaCategoria">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="CategoriaId" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="SelezionaCategoriaResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaCategoriaResult" type="tns:CategoriaInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:complexType name="CategoriaInfo">
        <s:complexContent mixed="false">
          <s:extension base="tns:BaseInfo">
            <s:sequence>
              <s:element minOccurs="1" maxOccurs="1" name="ID" type="s:int" />
              <s:element minOccurs="0" maxOccurs="1" name="Nome" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Descrizione" type="s:string" />
              <s:element minOccurs="1" maxOccurs="1" name="ParentID" type="s:int" />
            </s:sequence>
          </s:extension>
        </s:complexContent>
      </s:complexType>
      <s:element name="EliminaCategoria">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="CategoriaId" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="EliminaCategoriaResponse">
        <s:complexType />
      </s:element>
      <s:element name="InserisciCategoria">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="categoria" type="tns:CategoriaInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="InserisciCategoriaResponse">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCodiciCategoria">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCodiciCategoriaResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaCodiciCategoriaResult" type="tns:ArrayOfInt" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="SelezionaMerceologia">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="MerceologiaId" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="SelezionaMerceologiaResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaMerceologiaResult" type="tns:MerceologiaInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:complexType name="MerceologiaInfo">
        <s:complexContent mixed="false">
          <s:extension base="tns:BaseInfo">
            <s:sequence>
              <s:element minOccurs="1" maxOccurs="1" name="ID" type="s:int" />
              <s:element minOccurs="0" maxOccurs="1" name="Nome" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Descrizione" type="s:string" />
              <s:element minOccurs="1" maxOccurs="1" name="ParentID" type="s:int" />
            </s:sequence>
          </s:extension>
        </s:complexContent>
      </s:complexType>
      <s:element name="EliminaMerceologia">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="MerceologiaId" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="EliminaMerceologiaResponse">
        <s:complexType />
      </s:element>
      <s:element name="InserisciMerceologia">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="merceologia" type="tns:MerceologiaInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="InserisciMerceologiaResponse">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCodiciMerceologia">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaCodiciMerceologiaResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaCodiciMerceologiaResult" type="tns:ArrayOfInt" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="SelezionaOrdineTestata">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="idOrdine" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="SelezionaOrdineTestataResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaOrdineTestataResult" type="tns:OrdineTestataInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:complexType name="OrdineTestataInfo">
        <s:complexContent mixed="false">
          <s:extension base="tns:BaseInfo">
            <s:sequence>
              <s:element minOccurs="1" maxOccurs="1" name="ID" type="s:int" />
              <s:element minOccurs="1" maxOccurs="1" name="NumeroOrdine" type="s:int" />
              <s:element minOccurs="1" maxOccurs="1" name="DataOrdine" type="s:dateTime" />
              <s:element minOccurs="0" maxOccurs="1" name="IDTransazione" type="s:string" />
              <s:element minOccurs="1" maxOccurs="1" name="CodiceCliente" type="s:int" />
              <s:element minOccurs="0" maxOccurs="1" name="Destinazione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="IndirizzoDestinazione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="CAPDestinazione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="ComuneDestinazione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="ProvinciaDestinazione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="NazioneDestinazione" type="s:string" />
              <s:element minOccurs="1" maxOccurs="1" name="OrdineLetto" type="s:dateTime" />
              <s:element minOccurs="0" maxOccurs="1" name="Stato" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="IDSpeseSpedizione" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="IDMetodoPagamento" type="s:string" />
            </s:sequence>
          </s:extension>
        </s:complexContent>
      </s:complexType>
      <s:element name="SelezionaOrdineRighe">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="1" maxOccurs="1" name="idOrdine" type="s:int" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="SelezionaOrdineRigheResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaOrdineRigheResult" type="tns:ArrayOfOrdineRigaInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:complexType name="ArrayOfOrdineRigaInfo">
        <s:sequence>
          <s:element minOccurs="0" maxOccurs="unbounded" name="OrdineRigaInfo" nillable="true" type="tns:OrdineRigaInfo" />
        </s:sequence>
      </s:complexType>
      <s:complexType name="OrdineRigaInfo">
        <s:complexContent mixed="false">
          <s:extension base="tns:BaseInfo">
            <s:sequence>
              <s:element minOccurs="1" maxOccurs="1" name="IDOrdine" type="s:int" />
              <s:element minOccurs="1" maxOccurs="1" name="IDRiga" type="s:int" />
              <s:element minOccurs="0" maxOccurs="1" name="CodiceArticolo" type="s:string" />
              <s:element minOccurs="0" maxOccurs="1" name="Descrizione" type="s:string" />
              <s:element minOccurs="1" maxOccurs="1" name="Qta" type="s:int" />
              <s:element minOccurs="1" maxOccurs="1" name="PrezzoUnitario" type="s:double" />
            </s:sequence>
          </s:extension>
        </s:complexContent>
      </s:complexType>
      <s:element name="SelezionaOrdiniNuovi">
        <s:complexType />
      </s:element>
      <s:element name="SelezionaOrdiniNuoviResponse">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="SelezionaOrdiniNuoviResult" type="tns:ArrayOfInt" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="AggiornaOrdine">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="ordine" type="tns:OrdineTestataInfo" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="AggiornaOrdineResponse">
        <s:complexType />
      </s:element>
      <s:element name="ImpostaOrdiniLetti">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="elencoOrdini" type="tns:ArrayOfInt" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="ImpostaOrdiniLettiResponse">
        <s:complexType />
      </s:element>
      <s:element name="CaricaImmagine">
        <s:complexType>
          <s:sequence>
            <s:element minOccurs="0" maxOccurs="1" name="NomeFileImmagine" type="s:string" />
            <s:element minOccurs="0" maxOccurs="1" name="DatiImmagineInBase64" type="s:string" />
          </s:sequence>
        </s:complexType>
      </s:element>
      <s:element name="CaricaImmagineResponse">
        <s:complexType />
      </s:element>
    </s:schema>
  </wsdl:types>
  <wsdl:message name="SelezionaArticoloSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaArticolo" />
  </wsdl:message>
  <wsdl:message name="SelezionaArticoloSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaArticoloResponse" />
  </wsdl:message>
  <wsdl:message name="EliminaArticoloSoapIn">
    <wsdl:part name="parameters" element="tns:EliminaArticolo" />
  </wsdl:message>
  <wsdl:message name="EliminaArticoloSoapOut">
    <wsdl:part name="parameters" element="tns:EliminaArticoloResponse" />
  </wsdl:message>
  <wsdl:message name="InserisciArticoloSoapIn">
    <wsdl:part name="parameters" element="tns:InserisciArticolo" />
  </wsdl:message>
  <wsdl:message name="InserisciArticoloSoapOut">
    <wsdl:part name="parameters" element="tns:InserisciArticoloResponse" />
  </wsdl:message>
  <wsdl:message name="AggiornaArticoloSoapIn">
    <wsdl:part name="parameters" element="tns:AggiornaArticolo" />
  </wsdl:message>
  <wsdl:message name="AggiornaArticoloSoapOut">
    <wsdl:part name="parameters" element="tns:AggiornaArticoloResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaCodiciArticoloSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaCodiciArticolo" />
  </wsdl:message>
  <wsdl:message name="SelezionaCodiciArticoloSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaCodiciArticoloResponse" />
  </wsdl:message>
  <wsdl:message name="AggiornaQtaDisponibileArticoloSoapIn">
    <wsdl:part name="parameters" element="tns:AggiornaQtaDisponibileArticolo" />
  </wsdl:message>
  <wsdl:message name="AggiornaQtaDisponibileArticoloSoapOut">
    <wsdl:part name="parameters" element="tns:AggiornaQtaDisponibileArticoloResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaClienteSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaCliente" />
  </wsdl:message>
  <wsdl:message name="SelezionaClienteSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaClienteResponse" />
  </wsdl:message>
  <wsdl:message name="EliminaClienteSoapIn">
    <wsdl:part name="parameters" element="tns:EliminaCliente" />
  </wsdl:message>
  <wsdl:message name="EliminaClienteSoapOut">
    <wsdl:part name="parameters" element="tns:EliminaClienteResponse" />
  </wsdl:message>
  <wsdl:message name="AggiornaClienteSoapIn">
    <wsdl:part name="parameters" element="tns:AggiornaCliente" />
  </wsdl:message>
  <wsdl:message name="AggiornaClienteSoapOut">
    <wsdl:part name="parameters" element="tns:AggiornaClienteResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaCodiciClienteSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaCodiciCliente" />
  </wsdl:message>
  <wsdl:message name="SelezionaCodiciClienteSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaCodiciClienteResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaProduttoreSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaProduttore" />
  </wsdl:message>
  <wsdl:message name="SelezionaProduttoreSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaProduttoreResponse" />
  </wsdl:message>
  <wsdl:message name="EliminaProduttoreSoapIn">
    <wsdl:part name="parameters" element="tns:EliminaProduttore" />
  </wsdl:message>
  <wsdl:message name="EliminaProduttoreSoapOut">
    <wsdl:part name="parameters" element="tns:EliminaProduttoreResponse" />
  </wsdl:message>
  <wsdl:message name="InserisciProduttoreSoapIn">
    <wsdl:part name="parameters" element="tns:InserisciProduttore" />
  </wsdl:message>
  <wsdl:message name="InserisciProduttoreSoapOut">
    <wsdl:part name="parameters" element="tns:InserisciProduttoreResponse" />
  </wsdl:message>
  <wsdl:message name="AggiornaProduttoreSoapIn">
    <wsdl:part name="parameters" element="tns:AggiornaProduttore" />
  </wsdl:message>
  <wsdl:message name="AggiornaProduttoreSoapOut">
    <wsdl:part name="parameters" element="tns:AggiornaProduttoreResponse" />
  </wsdl:message>
  <wsdl:message name="AggiornaTabellaGenericaSoapIn">
    <wsdl:part name="parameters" element="tns:AggiornaTabellaGenerica" />
  </wsdl:message>
  <wsdl:message name="AggiornaTabellaGenericaSoapOut">
    <wsdl:part name="parameters" element="tns:AggiornaTabellaGenericaResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaCodiciProduttoreSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaCodiciProduttore" />
  </wsdl:message>
  <wsdl:message name="SelezionaCodiciProduttoreSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaCodiciProduttoreResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaCategoriaSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaCategoria" />
  </wsdl:message>
  <wsdl:message name="SelezionaCategoriaSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaCategoriaResponse" />
  </wsdl:message>
  <wsdl:message name="EliminaCategoriaSoapIn">
    <wsdl:part name="parameters" element="tns:EliminaCategoria" />
  </wsdl:message>
  <wsdl:message name="EliminaCategoriaSoapOut">
    <wsdl:part name="parameters" element="tns:EliminaCategoriaResponse" />
  </wsdl:message>
  <wsdl:message name="InserisciCategoriaSoapIn">
    <wsdl:part name="parameters" element="tns:InserisciCategoria" />
  </wsdl:message>
  <wsdl:message name="InserisciCategoriaSoapOut">
    <wsdl:part name="parameters" element="tns:InserisciCategoriaResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaCodiciCategoriaSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaCodiciCategoria" />
  </wsdl:message>
  <wsdl:message name="SelezionaCodiciCategoriaSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaCodiciCategoriaResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaMerceologiaSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaMerceologia" />
  </wsdl:message>
  <wsdl:message name="SelezionaMerceologiaSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaMerceologiaResponse" />
  </wsdl:message>
  <wsdl:message name="EliminaMerceologiaSoapIn">
    <wsdl:part name="parameters" element="tns:EliminaMerceologia" />
  </wsdl:message>
  <wsdl:message name="EliminaMerceologiaSoapOut">
    <wsdl:part name="parameters" element="tns:EliminaMerceologiaResponse" />
  </wsdl:message>
  <wsdl:message name="InserisciMerceologiaSoapIn">
    <wsdl:part name="parameters" element="tns:InserisciMerceologia" />
  </wsdl:message>
  <wsdl:message name="InserisciMerceologiaSoapOut">
    <wsdl:part name="parameters" element="tns:InserisciMerceologiaResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaCodiciMerceologiaSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaCodiciMerceologia" />
  </wsdl:message>
  <wsdl:message name="SelezionaCodiciMerceologiaSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaCodiciMerceologiaResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaOrdineTestataSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaOrdineTestata" />
  </wsdl:message>
  <wsdl:message name="SelezionaOrdineTestataSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaOrdineTestataResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaOrdineRigheSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaOrdineRighe" />
  </wsdl:message>
  <wsdl:message name="SelezionaOrdineRigheSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaOrdineRigheResponse" />
  </wsdl:message>
  <wsdl:message name="SelezionaOrdiniNuoviSoapIn">
    <wsdl:part name="parameters" element="tns:SelezionaOrdiniNuovi" />
  </wsdl:message>
  <wsdl:message name="SelezionaOrdiniNuoviSoapOut">
    <wsdl:part name="parameters" element="tns:SelezionaOrdiniNuoviResponse" />
  </wsdl:message>
  <wsdl:message name="AggiornaOrdineSoapIn">
    <wsdl:part name="parameters" element="tns:AggiornaOrdine" />
  </wsdl:message>
  <wsdl:message name="AggiornaOrdineSoapOut">
    <wsdl:part name="parameters" element="tns:AggiornaOrdineResponse" />
  </wsdl:message>
  <wsdl:message name="ImpostaOrdiniLettiSoapIn">
    <wsdl:part name="parameters" element="tns:ImpostaOrdiniLetti" />
  </wsdl:message>
  <wsdl:message name="ImpostaOrdiniLettiSoapOut">
    <wsdl:part name="parameters" element="tns:ImpostaOrdiniLettiResponse" />
  </wsdl:message>
  <wsdl:message name="CaricaImmagineSoapIn">
    <wsdl:part name="parameters" element="tns:CaricaImmagine" />
  </wsdl:message>
  <wsdl:message name="CaricaImmagineSoapOut">
    <wsdl:part name="parameters" element="tns:CaricaImmagineResponse" />
  </wsdl:message>
  <wsdl:portType name="AzzurraWebServiceSoap">
    <wsdl:operation name="SelezionaArticolo">
      <wsdl:input message="tns:SelezionaArticoloSoapIn" />
      <wsdl:output message="tns:SelezionaArticoloSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="EliminaArticolo">
      <wsdl:input message="tns:EliminaArticoloSoapIn" />
      <wsdl:output message="tns:EliminaArticoloSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="InserisciArticolo">
      <wsdl:input message="tns:InserisciArticoloSoapIn" />
      <wsdl:output message="tns:InserisciArticoloSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="AggiornaArticolo">
      <wsdl:input message="tns:AggiornaArticoloSoapIn" />
      <wsdl:output message="tns:AggiornaArticoloSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciArticolo">
      <wsdl:input message="tns:SelezionaCodiciArticoloSoapIn" />
      <wsdl:output message="tns:SelezionaCodiciArticoloSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="AggiornaQtaDisponibileArticolo">
      <wsdl:input message="tns:AggiornaQtaDisponibileArticoloSoapIn" />
      <wsdl:output message="tns:AggiornaQtaDisponibileArticoloSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaCliente">
      <wsdl:input message="tns:SelezionaClienteSoapIn" />
      <wsdl:output message="tns:SelezionaClienteSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="EliminaCliente">
      <wsdl:input message="tns:EliminaClienteSoapIn" />
      <wsdl:output message="tns:EliminaClienteSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="AggiornaCliente">
      <wsdl:input message="tns:AggiornaClienteSoapIn" />
      <wsdl:output message="tns:AggiornaClienteSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciCliente">
      <wsdl:input message="tns:SelezionaCodiciClienteSoapIn" />
      <wsdl:output message="tns:SelezionaCodiciClienteSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaProduttore">
      <wsdl:input message="tns:SelezionaProduttoreSoapIn" />
      <wsdl:output message="tns:SelezionaProduttoreSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="EliminaProduttore">
      <wsdl:input message="tns:EliminaProduttoreSoapIn" />
      <wsdl:output message="tns:EliminaProduttoreSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="InserisciProduttore">
      <wsdl:input message="tns:InserisciProduttoreSoapIn" />
      <wsdl:output message="tns:InserisciProduttoreSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="AggiornaProduttore">
      <wsdl:input message="tns:AggiornaProduttoreSoapIn" />
      <wsdl:output message="tns:AggiornaProduttoreSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciProduttore">
      <wsdl:input message="tns:SelezionaCodiciProduttoreSoapIn" />
      <wsdl:output message="tns:SelezionaCodiciProduttoreSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaCategoria">
      <wsdl:input message="tns:SelezionaCategoriaSoapIn" />
      <wsdl:output message="tns:SelezionaCategoriaSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="EliminaCategoria">
      <wsdl:input message="tns:EliminaCategoriaSoapIn" />
      <wsdl:output message="tns:EliminaCategoriaSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="InserisciCategoria">
      <wsdl:input message="tns:InserisciCategoriaSoapIn" />
      <wsdl:output message="tns:InserisciCategoriaSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciCategoria">
      <wsdl:input message="tns:SelezionaCodiciCategoriaSoapIn" />
      <wsdl:output message="tns:SelezionaCodiciCategoriaSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaMerceologia">
      <wsdl:input message="tns:SelezionaMerceologiaSoapIn" />
      <wsdl:output message="tns:SelezionaMerceologiaSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="EliminaMerceologia">
      <wsdl:input message="tns:EliminaMerceologiaSoapIn" />
      <wsdl:output message="tns:EliminaMerceologiaSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="InserisciMerceologia">
      <wsdl:input message="tns:InserisciMerceologiaSoapIn" />
      <wsdl:output message="tns:InserisciMerceologiaSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciMerceologia">
      <wsdl:input message="tns:SelezionaCodiciMerceologiaSoapIn" />
      <wsdl:output message="tns:SelezionaCodiciMerceologiaSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaOrdineTestata">
      <wsdl:input message="tns:SelezionaOrdineTestataSoapIn" />
      <wsdl:output message="tns:SelezionaOrdineTestataSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaOrdineRighe">
      <wsdl:input message="tns:SelezionaOrdineRigheSoapIn" />
      <wsdl:output message="tns:SelezionaOrdineRigheSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="SelezionaOrdiniNuovi">
      <wsdl:input message="tns:SelezionaOrdiniNuoviSoapIn" />
      <wsdl:output message="tns:SelezionaOrdiniNuoviSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="AggiornaOrdine">
      <wsdl:input message="tns:AggiornaOrdineSoapIn" />
      <wsdl:output message="tns:AggiornaOrdineSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="ImpostaOrdiniLetti">
      <wsdl:input message="tns:ImpostaOrdiniLettiSoapIn" />
      <wsdl:output message="tns:ImpostaOrdiniLettiSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="CaricaImmagine">
      <wsdl:input message="tns:CaricaImmagineSoapIn" />
      <wsdl:output message="tns:CaricaImmagineSoapOut" />
    </wsdl:operation>
    <wsdl:operation name="AggiornaTabellaGenerica">
      <wsdl:input message="tns:AggiornaTabellaGenericaSoapIn" />
      <wsdl:output message="tns:AggiornaTabellaGenericaSoapOut" />
    </wsdl:operation>
  </wsdl:portType>
  <wsdl:binding name="AzzurraWebServiceSoap" type="tns:AzzurraWebServiceSoap">
    <soap:binding transport="http://schemas.xmlsoap.org/soap/http" />
    <wsdl:operation name="SelezionaArticolo">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaArticolo" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="EliminaArticolo">
      <soap:operation soapAction="http://keypass.it/azzurra/EliminaArticolo" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="InserisciArticolo">
      <soap:operation soapAction="http://keypass.it/azzurra/InserisciArticolo" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaArticolo">
      <soap:operation soapAction="http://keypass.it/azzurra/AggiornaArticolo" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciArticolo">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaCodiciArticolo" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaQtaDisponibileArticolo">
      <soap:operation soapAction="http://keypass.it/azzurra/AggiornaQtaDisponibileArticolo" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCliente">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaCliente" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="EliminaCliente">
      <soap:operation soapAction="http://keypass.it/azzurra/EliminaCliente" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaCliente">
      <soap:operation soapAction="http://keypass.it/azzurra/AggiornaCliente" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciCliente">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaCodiciCliente" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaProduttore">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaProduttore" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="EliminaProduttore">
      <soap:operation soapAction="http://keypass.it/azzurra/EliminaProduttore" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="InserisciProduttore">
      <soap:operation soapAction="http://keypass.it/azzurra/InserisciProduttore" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaProduttore">
      <soap:operation soapAction="http://keypass.it/azzurra/AggiornaProduttore" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciProduttore">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaCodiciProduttore" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCategoria">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaCategoria" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="EliminaCategoria">
      <soap:operation soapAction="http://keypass.it/azzurra/EliminaCategoria" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="InserisciCategoria">
      <soap:operation soapAction="http://keypass.it/azzurra/InserisciCategoria" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciCategoria">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaCodiciCategoria" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaMerceologia">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaMerceologia" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="EliminaMerceologia">
      <soap:operation soapAction="http://keypass.it/azzurra/EliminaMerceologia" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="InserisciMerceologia">
      <soap:operation soapAction="http://keypass.it/azzurra/InserisciMerceologia" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciMerceologia">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaCodiciMerceologia" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaOrdineTestata">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaOrdineTestata" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaOrdineRighe">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaOrdineRighe" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaOrdiniNuovi">
      <soap:operation soapAction="http://keypass.it/azzurra/SelezionaOrdiniNuovi" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaOrdine">
      <soap:operation soapAction="http://keypass.it/azzurra/AggiornaOrdine" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="ImpostaOrdiniLetti">
      <soap:operation soapAction="http://keypass.it/azzurra/ImpostaOrdiniLetti" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="CaricaImmagine">
      <soap:operation soapAction="http://keypass.it/azzurra/CaricaImmagine" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaTabellaGenerica">
      <soap:operation soapAction="http://keypass.it/azzurra/AggiornaTabellaGenerica" style="document" />
      <wsdl:input>
        <soap:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
  </wsdl:binding>
  <wsdl:binding name="AzzurraWebServiceSoap12" type="tns:AzzurraWebServiceSoap">
    <soap12:binding transport="http://schemas.xmlsoap.org/soap/http" />
    <wsdl:operation name="SelezionaArticolo">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaArticolo" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="EliminaArticolo">
      <soap12:operation soapAction="http://keypass.it/azzurra/EliminaArticolo" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="InserisciArticolo">
      <soap12:operation soapAction="http://keypass.it/azzurra/InserisciArticolo" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaArticolo">
      <soap12:operation soapAction="http://keypass.it/azzurra/AggiornaArticolo" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciArticolo">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaCodiciArticolo" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaQtaDisponibileArticolo">
      <soap12:operation soapAction="http://keypass.it/azzurra/AggiornaQtaDisponibileArticolo" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCliente">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaCliente" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="EliminaCliente">
      <soap12:operation soapAction="http://keypass.it/azzurra/EliminaCliente" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaCliente">
      <soap12:operation soapAction="http://keypass.it/azzurra/AggiornaCliente" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciCliente">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaCodiciCliente" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaProduttore">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaProduttore" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="EliminaProduttore">
      <soap12:operation soapAction="http://keypass.it/azzurra/EliminaProduttore" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="InserisciProduttore">
      <soap12:operation soapAction="http://keypass.it/azzurra/InserisciProduttore" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaProduttore">
      <soap12:operation soapAction="http://keypass.it/azzurra/AggiornaProduttore" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciProduttore">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaCodiciProduttore" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCategoria">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaCategoria" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="EliminaCategoria">
      <soap12:operation soapAction="http://keypass.it/azzurra/EliminaCategoria" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="InserisciCategoria">
      <soap12:operation soapAction="http://keypass.it/azzurra/InserisciCategoria" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciCategoria">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaCodiciCategoria" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaMerceologia">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaMerceologia" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="EliminaMerceologia">
      <soap12:operation soapAction="http://keypass.it/azzurra/EliminaMerceologia" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="InserisciMerceologia">
      <soap12:operation soapAction="http://keypass.it/azzurra/InserisciMerceologia" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaCodiciMerceologia">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaCodiciMerceologia" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaOrdineTestata">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaOrdineTestata" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaOrdineRighe">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaOrdineRighe" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="SelezionaOrdiniNuovi">
      <soap12:operation soapAction="http://keypass.it/azzurra/SelezionaOrdiniNuovi" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaOrdine">
      <soap12:operation soapAction="http://keypass.it/azzurra/AggiornaOrdine" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="ImpostaOrdiniLetti">
      <soap12:operation soapAction="http://keypass.it/azzurra/ImpostaOrdiniLetti" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="CaricaImmagine">
      <soap12:operation soapAction="http://keypass.it/azzurra/CaricaImmagine" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="AggiornaTabellaGenerica">
      <soap12:operation soapAction="http://keypass.it/azzurra/AggiornaTabellaGenerica" style="document" />
      <wsdl:input>
        <soap12:body use="literal" />
      </wsdl:input>
      <wsdl:output>
        <soap12:body use="literal" />
      </wsdl:output>
    </wsdl:operation>
  </wsdl:binding>
  <wsdl:service name="AzzurraWebService">
    <wsdl:port name="AzzurraWebServiceSoap" binding="tns:AzzurraWebServiceSoap">
      <soap:address location="http://' . $_SERVER['HTTP_HOST'] . '/externalWs/azzurra/?wsdl=1" />
    </wsdl:port>
    <wsdl:port name="AzzurraWebServiceSoap12" binding="tns:AzzurraWebServiceSoap12">
      <soap12:address location="http://' . $_SERVER['HTTP_HOST'] . '/externalWs/azzurra/?wsdl=1" />
    </wsdl:port>
  </wsdl:service>
</wsdl:definitions>';

    }
    
}

?>
