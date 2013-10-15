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
           
       }
       
   }
    
}

?>
