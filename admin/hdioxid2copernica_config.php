<?php

class hdioxid2copernica_config extends oxAdminView
{

    protected $_sThisTemplate = "hdioxid2copernica_config.tpl";
  //  protected $_oConfig;

    public function render()
    {
        parent::render();
        $this->_oConfig = oxConfig::getInstance();

        $oSmarty = oxUtilsView::getInstance()->getSmarty();


        $oSmarty->assign("oViewConf", $this->_aViewData["oViewConf"]);
        $oSmarty->assign("user", $this->_oConfig->getShopConfVar('hdio2c_user'));
        $oSmarty->assign("pass", $this->_oConfig->getShopConfVar('hdio2c_pass'));
        $oSmarty->assign("host", $this->_oConfig->getShopConfVar('hdio2c_host'));
        $oSmarty->assign("acc", $this->_oConfig->getShopConfVar('hdio2c_acc'));
        $oSmarty->assign("ajax_url", hdio2cHelper::getAjaxUrl());
        $oSmarty->assign("config", json_encode(hdio2cConfig::getConfig()) );

        
      //  $oConfig = unserialize($this->_oConfig->getShopConfVar("hdio2cConfig"));
      //  $oSmarty->assign("config", json_encode($oConfig));
        
        return $this->_sThisTemplate;
    }

}

?>
