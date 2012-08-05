<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of hdio2c_oxnewssubscribed
 *
 * @author Entwicklung
 */
class hdio2c_oxnewssubscribed extends hdio2c_oxnewssubscribed_parent
{

    public function saveNewsletter2Copernica($status, $function) {
        hdio2c_sync::saveSync($function, $this->getId(), array("status" => $status));
    }

    public function setOptInStatus($iStatus) {
        $ret = parent::setOptInStatus($iStatus);        
        $this->saveNewsletter2Copernica($iStatus, "newsletterOptIn");
        return $ret;
    }
    
    public function setOptInEmailStatus($iStatus)
    {
        $ret = parent::setOptInEmailStatus($iStatus);        
        $this->saveNewsletter2Copernica($iStatus, "emailOptIn");
        return $ret;
    }

}

?>
