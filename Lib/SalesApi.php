<?php

class SalesApi {
    
    protected $shipping = array("code" => "2020000000631", 
                                "description" => "Costo Spedizione");
    protected $cashOn   = array("code" => "2020000038832", 
                                "description" => "Costo Contrassegno");

	protected $_idAd = 100000000;
    
    public function getList() {
        $listaOrdini = array();
        $order = Mage::getModel("sales/order")->getCollection()
                    ->AddAttributeToFilter("status", array("in"=>Array("processing", )));    

	$order->getSelect()
	      ->joinLeft('sales_flat_order_payment', 'main_table.entity_id = sales_flat_order_payment.parent_id','method')
	      ->orWhere("method = 'banktransfer' and (status = 'pending_payment' or status = 'pending')");
//	      ->orWhere("increment_id = '100000057'")
//	      ->orWhere("increment_id = '100000058'");

        foreach ($order as $o) {
            $listaOrdini[] = $o->getIncrementId() -  $this->_idAd;
        }
        return $listaOrdini;
    }
    
    public function getRighe ($idOrdine) {
        $rows = Array();
        $ordine = Mage::getModel("sales/order")->Load(($idOrdine + $this->_idAd)."", 'increment_id');

        if ($ordine->getEntityId() > 0) { 
            $rowCount = 0;
            foreach ($ordine->getItemsCollection() as $item) {
                if (is_null($item->getParentItemId())) {
                    $row = new OrdineRigaInfo();
                    $row->IDOrdine = $ordine->getEntityId();
                    $row->IDRiga = ++$rowCount;
                    $product = Mage::getModel("catalog/product")->Load($item->getProductId());
                    $row->CodiceArticolo = $product->getSku();
                    $row->Descrizione = htmlspecialchars($item->getName());
                    $row->Qta = round($item->getQtyOrdered());
                    $row->PrezzoUnitario = ($item->getBaseRowTotal() + $item->getBaseTaxAmount() + $item->getBaseHiddenTaxAmount() + $item->getBaseWeeeTaxAppliedRowAmount() - $item->getBaseDiscountAmount()) / (round($item->getQtyOrdered()));
                    $rows[] = $row;
                }
            }
        }
        if (!is_null ($ordine->getBaseShippingInclTax()) && $ordine->getBaseShippingInclTax() != 0) {
            $row = new OrdineRigaInfo();
            $row->IDOrdine = $ordine->getEntityId();
            $row->IDRiga = ++$rowCount;
            $row->CodiceArticolo = $this->shipping["code"];
            $row->Descrizione = $this->shipping["description"];
            $row->Qta = "1";
            $row->PrezzoUnitario = $ordine->getBaseShippingInclTax();
            $rows[] = $row;
        }
        if (!is_null ($ordine->getCodFee()) && $ordine->getCodFee()!=0) {
            $row = new OrdineRigaInfo();
            $row->IDOrdine = $ordine->getEntityId();
            $row->IDRiga = ++$rowCount;
            $row->CodiceArticolo = $this->cashOn["code"];
            $row->Descrizione = $this->cashOn["description"];
            $row->Qta = "1";
            $row->PrezzoUnitario = round($ordine->getCodFee() + $ordine->getCodTaxAmount(), 2);
            $rows[] = $row;
        }

        return $rows;
    }
    
    public function getTestata($idOrdine, $setStatus = false) {
        $testataInfo = null;
        $ordine = Mage::getModel("sales/order")->Load(($idOrdine + $this->_idAd).'', 'increment_id');
        if ($ordine->getEntityId() > 0) {                      
            $testataInfo = new OrdineTestataInfo();
            $testataInfo->ID = $ordine->getIncrementId() -  $this->_idAd;
            $testataInfo->NumeroOrdine = $ordine->getEntityId();
            $testataInfo->DataOrdine = date("Y-m-d", Mage::getModel('core/date')->timestamp(strtotime($ordine->getCreatedAt()))) . "T" . date("H:i:s", Mage::getModel('core/date')->timestamp(strtotime($ordine->getCreatedAt()))) . ".00";
            $testataInfo->CodiceCliente = $ordine->getCustomerId();
            $testataInfo->IDTransazione = $ordine->getIncrementId();
            $address = $ordine->getShippingAddress();            
            $testataInfo->Destinazione = $address->getLastname() . ' ' . $address->getFirstname();
//print_r($address->getData());
//die();
            foreach ($address->getStreet() as $street) {
                //$testataInfo->Destinazione .=  htmlspecialchars($street);     
                $testataInfo->IndirizzoDestinazione .=  htmlspecialchars($street);    
            }            
            $testataInfo->CAPDestinazione = $address->getPostcode();
            $testataInfo->ComuneDestinazione = $address->getCity();
            $testataInfo->ProvinciaDestinazione = $address->getRegion();
            $testataInfo->NazioneDestinazione = $address->getCountryId();
            $testataInfo->Stato = "2";
            $testataInfo->IDSpeseSpedizione = "0";
            
            $payment = $ordine->getPayment();

            switch ($payment->getMethod()) {
                case "purchaseorder":
								case "banktransfer":
                    $testataInfo->IDMetodoPagamento = "10";
                break;
                case "cashondelivery":
                        $testataInfo->IDMetodoPagamento = "5";
                    break;
								case "paypal_standard":
												$testataInfo->IDMetodoPagamento = "9";
		    						break;
                default: //CC
                        $testataInfo->IDMetodoPagamento = "7";
                    break;
            }

            if ($ordine->getStatus() == "Send2ERP" && 1 == 0) {
                $reader = Mage::getSingleton('core/resource')->getConnection('core_read');
                $select = new Varien_Db_Select($reader);
                $select->from(array('ea' => Mage::getSingleton('core/resource')->getTableName('sales_flat_order_status_history')))
                       ->where("parent_id = " . $ordine->getEntityId());
                foreach ($reader->fetchAll($select) as $status) {
                    if ($status['status'] == "Send2ERP") {
                        $testataInfo->OrdineLetto = date("Y/m/d", $status['created_at']) . "T" . date("H:i:s", $status['created_at']) . ".00";
                        break;
                    }
                }
            } 
            
        }
        return $testataInfo;
    }

    public function setLetti($orderId, $sendMail = false) {
        $ordine = Mage::getModel("sales/order")->Load(($orderId + $this->_idAd)."", 'increment_id');
        if ($ordine->getEntityId() > 0 && $ordine->getStatus() != "Send2ERP") {                                  
            if ($sendMail) {
                $order->sendNewOrderEmail();
            }
            $ordine->setState("processing", "Send2ERP", $comment = 'Scaricato automaticamente dal Gestionale', true);
            $ordine->save();
        }
    }
    
}

?>
