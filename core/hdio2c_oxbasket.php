<?php
/**
 * Description of hdio2c_oxbasket
 *
 * @author Rafael Dabrowski
 */
class hdio2c_oxbasket extends hdio2c_oxbasket_parent
{

    /**
     * Hooking onto Add Item To Basket Function
     * @see oxBasket::addToBasket()
     */
    public function addToBasket($pid, $am, $sel = null, $persp = null, $over = false, $bundle = false, $oldbasket = null) {
        if ($this->_canSaveBasket()) {
            if ($oUser = $this->getBasketUser()) {
                $oSavedBasket = $oUser->getBasket('savedbasket');        
                hdio2c_sync::saveSync("savedbasket", $oSavedBasket->getId());
            }
        }
        $ret = parent::addToBasket($pid, $am, $sel, $persp, $over, $bundle, $oldbasket);
        return $ret;
    }
}

