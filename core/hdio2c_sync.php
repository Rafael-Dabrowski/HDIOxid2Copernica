<?php

class hdio2c_sync extends oxBase
{

    /**
     * The Table where the items should be stored
     * @var string  
     */
    protected $_sCoreTable = 'hdio2c_sync';
    protected $_aSkipSaveFields = null;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxnews';

    public function __construct() {
        parent::__construct();
        $this->init($this->_sCoreTable);
    }

    /**
     * Fields that shouldn be affected by updates.
     */
    protected function _skipSaveFields() {
        $this->_aSkipSaveFields = array();
        $this->_aSkipSaveFields[] = 'added';
        $this->_aSkipSaveFields[] = 'modified';
    }

    /**
     * On Insert
     * @return bool
     */
    protected function _insert() {
        $now = date('Y-m-d H:i:s', time());
        $this->_aSkipSaveFields[] = 'added';
        $this->hdio2c_sync__added = new oxField($now);
        $this->hdio2c_sync__modified = new oxField($now);
        return parent::_insert();
    }

    /**
     * On Update
     * @return bool
     */
    protected function _update() {
        $this->_skipSaveFields();
        return parent::_update();
    }

    /**
     * Saves a Sync entry to Database
     * @param string $type
     * @param string $sOxid
     * @param array $aParams
     */
    public static function saveSync($type, $sOxid, $aParams = array()) {
        $sync = oxNew("hdio2c_sync");
        /* @var $sync hdio2c_sync */
        $params = array();
        $params["hdio2c_sync__type"] = $type;

        $o = new stdClass();
        $o->oxid = $sOxid;

        foreach ($aParams as $key => $value) {
            $o->$key = $value;
        }

        $params["hdio2c_sync__object"] = serialize($o);
        $sync->assign($params);
        $sync->save();
    }

    /**
     * returns the Type of this Syncitem
     * @return string
     */
    public function getType() {
        return $this->hdio2c_sync__type->value;
    }

    /**
     * Returns the Object with information to this sync process
     * @return mixed
     */
    public function getObject() {
        return unserialize($this->hdio2c_sync__object->rawValue);
    }

    protected function setState($state) {
        $this->hdio2c_sync__state->value = $state;
        $this->save();
    }

    public function sync() {
        $cop = CopernicaHandler::getInstance();

        return $this->{$this->getType()}($cop);
    }

    protected function collectUserInfo($oUser, $fieldMatching) {
        $aFields = array();
        foreach ($fieldMatching as $internal => $field) {
            switch ($internal) {
                case "OXID":
                    $aFields[$field->id] = $oUser->getId();
                    break;
                case "Firstname":
                    $aFields[$field->id] = $oUser->oxuser__oxfname->value;
                    break;
                case "Lastname":
                    $aFields[$field->id] = $oUser->oxuser__oxlname->value;
                    break;
                case "Salutation":
                    $aFields[$field->id] = $oUser->oxuser__oxsal->value;
                    break;
                case "Email":
                    $aFields[$field->id] = $oUser->oxuser__oxusername->value;
                    break;
                case "Group":
                    $Groups = "";
                    foreach ($oUser->getUserGroups() as $Group) {
                        /* @var $Group oxGroups */

                        $Groups.= $Group->oxgroups__oxtitle->value . "; ";
                    }

                    $aFields[$field->id] = $Groups;
                    break;
                case "CustomerId":
                    $aFields[$field->id] = $oUser->oxuser__oxcustnr->value;
                    break;
                case "Birthday":
                    $aFields[$field->id] = $oUser->oxuser__oxbirthdate->value;
                    break;
                case "Registered":
                    $aFields[$field->id] = $oUser->oxuser__oxcreate->value;
                    break;
                case "Bonus":
                    $aFields[$field->id] = $oUser->getBoni();
                    break;
            }
        }
        return $aFields;
    }

    /**
     * 
     * @param oxarticle $oProduct
     * @param type $aFieldMatching
     * @return type
     */
    protected function collectProductInfos($oProduct, $aFieldMatching) {
        $aFields = array();
        foreach ($aFieldMatching as $internal => $field) {
            switch ($internal) {
                case "OXID":
                    $aFields[$field->id] = $oProduct->getId();
                    break;
                case "SKU":
                    $aFields[$field->id] = $oProduct->oxarticles__oxartnum->value;
                    break;
                case "Name":
                    $aFields[$field->id] = $oProduct->oxarticles__oxtitle->value;
                    break;
                case "Description":
                    $aFields[$field->id] = $oProduct->oxarticles__oxshortdesc->value;
                    break;
                case "Price":
                    $aFields[$field->id] = $oProduct->getPrice()->getBruttoPrice();
                    break;
                case "VAT":
                    $aFields[$field->id] = $oProduct->getPrice()->getVat();
                    break;
                case "Thumbnail":
                    $aFields[$field->id] = $oProduct->getThumbnailUrl();
                    break;
                case "Icon":
                    $aFields[$field->id] = $oProduct->getIconUrl();
                    break;
                case "Picture":
                    $aFields[$field->id] = $oProduct->getPictureUrl();
                    break;
                case "Variant":
                    $aFields[$field->id] = $oProduct->oxarticles__oxvarname->value;
                    break;
                case "URL":
                    $aFields[$field->id] = $oProduct->getMainLink();
                    break;
            }
        }
        return $aFields;
    }

    protected function newUser($cop) {

        $oUser = oxNew("oxuser");
        /* @var $oUser oxUser */
        if ($oUser->load($this->getObject()->oxid)) {
            $this->setState(1);
            $oConfig = hdio2cConfig::getConfig();
            $aParams = array(
                "id" => $oConfig->account->id,
                "fields" => $this->collectUserInfo($oUser, $oConfig->account->fieldMatching));
            $cop->Execute("database_createProfile", $aParams, false);
            return $aParams;
        }
        return $this->getObject();
    }

    /**
     * Handles saved Baskets
     * @todo Update of a Product
     * @param CopernicaHandler $cop
     * @return mixed
     */
    protected function savedbasket($cop) {
        $oBasket = oxNew("oxuserbasket");
        if ($oBasket->load($this->getObject()->oxid)) {
            $this->setState(1);
            /* @var $oBasket oxUserBasket */
            $uid = $oBasket->oxuserbaskets__oxuserid->value;
            $oConfig = hdio2cConfig::getConfig();
            $cUser = $cop->Execute("database_searchProfile", array(
                "id" => $oConfig->account->id,
                "requirements" => array(
                    "fieldname" => $oConfig->account->fieldMatching["OXID"]->id,
                    "operator" => '=',
                    'value' => $uid
                )
                    )
            );
            if ($cUser->length == 0) {
                $this->setState(null);
                return -1;
            }
            $subProfiles = $cop->Execute("profile_searchSubprofiles", array(
                "id" => $cUser->profile[0]->id,
                "allproperties" => 1,
                "requirements" => array(
                    "fieldname" => $oConfig->basket->fieldMatching["BasketType"]->id,
                    "operator" => "=",
                    "value" => "savedbasket"
                )
                    ));

            foreach ($oBasket->getItems() as $oBasketItem) {
                /* @var $oBasketItem oxUSerBasketItem */
                $found = 0;
                $needUpdate = false;
                foreach ($subProfiles->profile as $subprofile) {
                    if ($subprofile->{$oConfig->basket->fieldMatching["OXID"]} == $oBasketItem->getId()) {
                        $found = $subprofile->id;
                        if ($subprofile->{$oConfig->basket->fieldMatching["Quantity"]} != $oBasketItem->oxuserbasketitems__oxamount->value) {
                            $needUpdate = true;
                        }
                        if ($subprofile->{$oConfig->basket->fieldMatching["Price"]} != $oBasketItem->getArticle("savedbasket")->getPrice()->getBruttoPrice()) {
                            $needUpdate = true;
                        }
                    }
                }
                if ($found > 0) {
                    if ($needUpdate) {
                        // HEAR GOES UPDATE PROCESS
                    }
                } else {
                    $aProduct = $this->collectProductInfo(
                            $oBasketItem->getArticle("savedbasket"), $oConfig->basket->fieldMatching
                    );
                    $aParams = array(
                    "id" => $cUser->profile[0]->id,
                    "fields" => $aProduct
                    );
                    $cop->Execute("profile_createSubprofile", $aParams, false);
                }

                return $aParams;
            }
        }
    }

    protected function newOrder($cop) {
        
    }

    protected function noticelist($cop) {
        
    }

    protected function wishlist($cop) {
        
    }

    protected function newsletterOptIn($cop) {
        
    }

    protected function emailOptIn($cop) {
        
    }

    protected function updateUser($cop) {
        $this->setState(1);
        $oUser = oxNew("oxuser");
        /* @var $oUser oxUser */
        $oUser->load($this->getObject()->oxid);
        $oConfig = hdio2cConfig::getConfig();
        $aParams = array(
            "id" => $oConfig->account->id,
            "requirements" => array(
                "fieldname" => $oConfig->account->fieldMatching["OXID"]->id,
                "operator" => '=',
                'value' => $oUser->getId()
            ),
            "fields" => $this->collectUserInfo($oUser, $oConfig->account->fieldMatching));
        return $cop->Execute("database_updateProfiles", $aParams, false);
    }

}

?>
