<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductAPIAsync
 *
 * @author doctor
 */
class ProductAPIAsync {
    
   public function eliminaArticolo ($productId) {      
       
       $import = new ImportAPI();
       $session = $import->getSession();
       
       if (isset($session['Id']) && isset($session['UID'])) {
           $session['Prod_Delete']++;
           
           $impDelete = array('IdImport'     => $session['Id'],
                              'UID'          => $session['UID'],
                              'IdGestionale' => $productId,
                              'InsertDate'   => DbAPI::MySqlDateTime());
           
           DbAPI::SaveTable("AV_Import_Delete", $impDelete);
           DbAPI::SaveTable("AV_Import", $session, array('Id'));
           
       }
       
   }
   
   public function inserisciArticolo($productInfo) {
        
        //controllo se l'articolo esiste
        if (!property_exists($productInfo, "ID") || $productInfo->ID == 0) {
            return;
        } 
        
        $import = new ImportAPI();
        $session = $import->getSession();
       
        if (isset($session['Id']) && isset($session['UID'])) {
            $session['Prod_Insert']++;
            $impInsert = array('IdImport'     => $session['Id'],
                               'UID'          => $session['UID'],
                               'IdGestionale' => $productInfo->ID,
                               'InsertDate'   => DbAPI::MySqlDateTime(),
                               'QtaNew'       => (property_exists($productInfo, "QtaDisponibile") && $productInfo->QtaDisponibile != 0) ? $productInfo->QtaDisponibile : 0,
                               'SerializedObject'   => serialize($productInfo));
           
            DbAPI::SaveTable("AV_Import_Insert", $impInsert);
            DbAPI::SaveTable("AV_Import", $session, array('Id'));
        }        
   }
   
   public function caricaImmagine($NomeFileImmagine, $DatiImmagineInBase64) {
       
       $pApi = new ProductApi();
       
       $prodId = $pApi->_getIdbyImage($NomeFileImmagine);
       
       if (prodId > 0) {
            $import = new ImportAPI();
            $session = $import->getSession();

            if (isset($session['Id']) && isset($session['UID'])) {
                $session['Img_Insert']++;
                $imgInsert = array('IdImport'     => $session['Id'],
                                   'UID'          => $session['UID'],
                                   'IdGestionale' => $prodId,
                                   'InsertDate'   => DbAPI::MySqlDateTime(),                                   
                                   'Base64Img'    => $DatiImmagineInBase64);

                DbAPI::SaveTable("AV_Import_Image", $imgInsert);
                DbAPI::SaveTable("AV_Import", $session, array('Id'));
            }                   
       }
       
   }
    
}

?>
