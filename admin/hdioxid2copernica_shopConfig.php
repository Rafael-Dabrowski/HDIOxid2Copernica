<?php

class hdioxid2copernica_shopConfig extends oxAdminView
{

    protected $_sThisTemplate = "hdioxid2copernica_shopConfig.tpl";

    //  protected $_oConfig;


    public function render()
    {
        parent::render();
      
        $oSmarty = oxUtilsView::getInstance()->getSmarty(); 
        $oSmarty->assign("ajax_url", hdio2cHelper::getAjaxUrl());

        return $this->_sThisTemplate;
    }

}

?>
