<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of hdio2c_oxuserbasket
 *
 * @author Entwicklung
 */
class hdio2c_oxuserbasket extends hdio2c_oxuserbasket_parent
{

    /**
     * Hooking onto Add Item to Userbasket Function 
     * @see oxuserbasket::addItemToBasket()
     */
    public function addItemToBasket($sProductId = null, $dAmaount = null, $aSel = null, $blOverride = false, $aPersParam = null) {
        $ret = parent::addItemToBasket($sProductId, $dAmaount, $aSel, $blOverride, $aPersParam);
        $basketType = $this->oxuserbaskets__oxtitle->value;
        if ($basketType != 'savedbasket') {
            hdio2c_sync::saveSync($basketType, $this->getId());
        }
        return $ret;
    }

    /**
     * Hooking onto delete Userbasket function
     * @see oxuserbasket::delete()
     */
    public function delete($sOXID = null) {
        $ret = parent::delete($sOXID);
        $basketType = $this->oxuserbaskets__oxtitle->value;
        if ($basketType != 'savedbasket') {
            hdio2c_sync::saveSync("deleteUserbasket", $this->getId());
        }
        return $ret;
    }

}

?>
