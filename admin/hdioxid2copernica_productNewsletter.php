<?php

class hdioxid2copernica_productNewsletter extends oxAdminView
{

    protected $_sThisTemplate = "hdioxid2copernica_productNewsletter.tpl";

    //  protected $_oConfig;


    public function render()
    {
        parent::render();
        $oConfig = hdio2cConfig::getConfig(); 
       
        $oSmarty = oxUtilsView::getInstance()->getSmarty();
        $oSmarty->assign("ajax_url", hdio2cHelper::getAjaxUrl());
        $oSmarty->assign("newsletterConfig", $oConfig->products);
        return $this->_sThisTemplate;
    }

}

?>
