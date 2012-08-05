<?php

class hdio2c_oxorder extends hdio2c_oxorder_parent
{
   /**
    * Hooking onto finalaize Order Function 
    * @see oxOrder::finalizeOrder()
    */
    public function finalizeOrder($oBasket, $oUser, $blRecalculatingOrder = false)
    {
        $ret = parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder); 
        hdio2c_sync::saveSync("NewOrder", $this->getId(), array("ret" => $ret));
        return $ret; 
    }
}

?>
