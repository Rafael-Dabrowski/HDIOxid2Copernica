<?php

class hdioxid2copernica_ajax extends oxAdminView
{

    /**
     * Name of Template to Render
     * @var string 
     */
    protected $_sThisTemplate = "hdioxid2copernica_ajax.tpl";

    /**
     * Smarty Object reference
     * @var smarty 
     */
    protected $_oSmarty;

    /**
     * Init function 
     */
    public function init() {
        $this->_oSmarty = oxUtilsView::getInstance()->getSmarty();
    }

    /**
     * Renders the Output
     * @return string 
     */
    public function render() {
        parent::render();
        try {
            $scope = oxConfig::getParameter("scope");
            if ($scope != "oxid") {

                $this->doApiCall();
            } else {

                $fnc = oxConfig::getParameter("func");
                $this->$fnc();
            }
        } catch (Exception $ex) {
            echo $ex;
        }

        return $this->_sThisTemplate;
    }

    /**
     * Does the ApiCall to Copernica
     * Needs at least the POST/GET Parameter: func with the api function 
     */
    protected function doApiCall() {
        try {
            $oConfig = oxConfig::getInstance();
            $pom = CopernicaHandler::getInstance();
            $fnc = oxConfig::getParameter("func");
            if (isset($fnc)) {
                $function = oxConfig::getParameter("func");
                $aParams = oxConfig::getParameter("params");
                if (isset($aParams)) {
                    $aParams = array_merge($aParams, oxConfig::getParameter("params"));
                } else {
                    $aParams = array();
                }
                $aStruct = oxConfig::getParameter("struct");
                if (isset($aStruct)) {

                    if ($aStruct) {
                        foreach ($aStruct as $key => $value) {
                            $x = $pom->getClient()->toObject($value);
                            $aParams[$key] = $x;
                        }
                    }
                }
                $aaStruct = oxConfig::getParameter("aStruct");
                if (isset($aaStruct)) {

                    if ($aaStruct) {
                        foreach ($aaStruct as $key => $aValue) {
                            $aParams[$key] = array();
                            foreach ($aValue as $value) {
                                array_push($aParams[$key], $pom->getClient()->toObject($value));
                            }
                        }
                    }
                }
                $res = $pom->Execute($function, $aParams);
            }
        } catch (Exception $ex) {
            $this->_oSmarty->assign("json", json_encode($ex));
        }
        $this->_oSmarty->assign("json", json_encode($res));
    }

    /**
     * Checks the connection to Copernica Api Server 
     */
    protected function checkConnection() {
        try {
            $res = CopernicaHandler::checkLogin(oxConfig::getParameter("host"), oxConfig::getParameter("user"), oxConfig::getParameter("account"), oxConfig::getParameter("pass"));
            if ($res === true) {
                $this->_oSmarty->assign("json", '{"result": true}');
                $oConfig = oxConfig::getInstance();
                $oConfig->saveShopConfVar("str", "hdio2c_host", oxConfig::getParameter("host"), null, 'module:hdioxid2copernica');
                $oConfig->saveShopConfVar("str", "hdio2c_user", oxConfig::getParameter("user"), null, 'module:hdioxid2copernica');
                $oConfig->saveShopConfVar("str", "hdio2c_pass", oxConfig::getParameter("pass"), null, 'module:hdioxid2copernica');
                $oConfig->saveShopConfVar("str", "hdio2c_acc", oxConfig::getParameter("account"), null, 'module:hdioxid2copernica');

                $oSClass = new stdClass();
                $oSClass->result = true;
                $oSClass->host = $oConfig->getShopConfVar("hdio2c_host");
                $oSClass->user = $oConfig->getShopConfVar("hdio2c_user");
                $oSClass->pass = $oConfig->getShopConfVar("hdio2c_pass");
                $oSClass->acc = $oConfig->getShopConfVar("hdio2c_acc");

                $this->_oSmarty->assign("json", json_encode($oSClass));
            } else {
                $this->_oSmarty->assign("json", '{"result": "' . $res . '"}');
            }
        } catch (Exception $ex) {
            $this->returnException($ex);
        }
    }

    /**
     * Saves the Configuration to Database 
     */
    protected function saveConfig() {
        try {
            $defString = oxConfig::getParameter("obj");
            $defConfig = json_decode(str_replace("&quot;", "\"", $defString));

            $newConfig = new hdio2cConfig($defConfig->database);
            $newConfig->save();
            $res = new stdClass();
            $res->result = true;
            $res->defString = $defString;
            $res->oldConfig = $defConfig;
            $res->newConfig = $newConfig;

            $this->_oSmarty->assign("json", json_encode($res));
        } catch (Exception $e) {
            $this->returnException($e);
        }
    }

    /**
     * Gets Selection from Copernica Database
     */
    protected function getSelections() {
        try {
            $cop = CopernicaHandler::getInstance();
            $res = $cop->Execute("database_views", array("id" => hdio2cConfig::getConfig()->account->id, "allproperties" => true));
            $return = new stdClass();
            $return->result = true;
            $return->selections = $res->items;
            $this->_oSmarty->assign("json", json_encode($return));
        } catch (Exception $e) {
            $this->returnException($e);
        }
    }

    /**
     * Searches for Products by given GET/POST => query
     */
    protected function findProducts() {
        $query = oxConfig::getParameter("query");

        $oSearch = oxNew("oxsearch");

        $result = $oSearch->getSearchArticles($query);
        $iFound = $oSearch->getSearchArticleCount($query);

        $return = new stdClass();
        $return->result = true;
        $return->found = $iFound;
        $pageSize = oxConfig::getInstance()->getConfigParam('iNrofCatArticles');
        $pageSize = $pageSize ? $pageSize : 10;
        $return->pageSize = $pageSize;
        $articles = array();
        foreach ($result as $oArticle) {
            $article = new stdClass();
            $article->id = $oArticle->getProductId();
            $article->name = $oArticle->oxarticles__oxtitle->value;
            $article->artnum = $oArticle->oxarticles__oxartnum->value;
            $article->price = $oArticle->getFPrice();
            $article->description = $oArticle->oxarticles__oxshortdesc->value;
            $article->thumbnail = $oArticle->getIconUrl();
            $articles[] = $article;
        }
        $return->products = $articles;
        $this->_oSmarty->assign("json", json_encode($return));
    }

    /**
     * Creates the whole Database an Collection Structure in Copernica 
     */
    protected function createDatabaseAndCollections() {
        $step = 0;
        $start = microtime(true);
        $config = new hdio2cConfig();
        try {
            $collections = array("orders" => "Orders", "orderItems" => "OrderItems", "basket" => "Basket", "addresses" => "Addresses");

            $cop = CopernicaHandler::getInstance();

            $dbRes = $cop->createDatabase(oxConfig::getParameter("name"));
            if ($dbRes != false) {
                $config->account->id = $dbRes->id;
                $config->account->name = $dbRes->name;
            } else {
                throw new Exception("Database Creation failed");
            }
            $step = 1;

            $dbRes = $cop->createDatabase(oxConfig::getParameter("productName"));
            if ($dbRes != false) {
                $config->products->id = $dbRes->id;
                $config->products->name = $dbRes->name;
            } else {
                throw new Exception("ProductDatabase Creation failed");
            }
            $step = 2;

            foreach ($collections as $key => $value) {
                $col = $cop->createCollection($dbRes->id, $value);
                if ($col != false) {
                    $config->$key->id = $col->id;
                    $config->$key->name = $col->name;
                } else {
                    throw new Exception("Collection: $value Creation failed");
                }
            }
            $fieldResult = array();
            foreach (hdio2cHelper::$fields as $key => $value) {
                $type = "collection";
                if ($key == "account" || $key == "products") {
                    $type = "database";
                }
                $fieldResult[$key] = array();
                foreach ($value as $name => $fieldType) {
                    $fieldResult[$key][$name] = $cop->createField($type, $config->$key->id, $name, false, $fieldType);
                }
            }
            $cop->getClient()->run();
            foreach ($fieldResult as $collection => $value) {
                foreach ($value as $name => $resID) {
                    $config->$collection->fieldMatching[$name] = new hdio2cField($cop->result($resID)->id, $cop->result($resID)->name);
                }
            }

            $this->_oSmarty->assign("json", '{"result": true, "time":' . (microtime(true) - $start) . ', "config":' . json_encode($config) . '}');
        } catch (Exception $ex) {
            $this->returnException($ex, "time elapsed:" . (microtime(true) - $start));
        }
        $config->save();
    }

    /**
     * Get items yet to have been synced
     */
    protected function getSyncInfo() {

        $oSyncList = oxNew('hdio2c_syncList');
        /* @var $oSyncList hdio2c_syncList */
        $oSyncList->getSyncItems();

        $info = new stdClass();
        $info->toSync = $oSyncList->count();
        $info->objects = array();
        foreach ($oSyncList as $item) {
            $info->objects[] = array('name' => $item->getId(), 'operation' => $item->hdio2c_sync__type->value, 'object' => $item->hdio2c_sync__object->value, 'date' => $item->hdio2c_sync__added->value);
        }
        //$info->objects = array_reverse($info->objects);
        $this->_oSmarty->assign("json", json_encode($info));
    }

    /**
     * Creates all Syncitems from all previus actions in Shop
     * @todo Newsletter subscriptions
     */
    protected function initialSync() {
        $oUsers = oxNew("oxuserlist");
        /* @var $oUsers oxuserlist */
        $oUsers->getList();
        $cUser = 0;
        $cOrders = 0;
        $cSavedBaskets = 0;
        $cWishLists = 0;
        $cNoticeList = 0;
        $cNewsletterOptIns = 0;
        $cEmailOptIns = 0;
        foreach ($oUsers as $oUser) {
            $cUser++;
            hdio2c_sync::saveSync("newUser", $oUser->getId());

            $savedBasket = $oUser->getBasket('savedbasket');
            if ($sid = $savedBasket->getId()) {
                hdio2c_sync::saveSync("savedbasket", $savedBasket->getId());
                $cSavedBaskets++;
            }

            $noticeList = $oUser->getBasket('noticelist');
            if ($wid = $noticeList->getId()) {
                hdio2c_sync::saveSync("noticelist", $wid);
                $cNoticeList++;
            }

            $wishList = $oUser->getBasket('wishlist');
            if ($nid = $wishList->getId()) {
                hdio2c_sync::saveSync("wishlist", $nid);
                $cWishLists++;
            }

            $oOrders = $oUser->getOrders();
            foreach ($oOrders as $oOrder) {
                $cOrders++;
                hdio2c_sync::saveSync("newOrder", $oOrder->getId());
            }
            $oNewsletter = $oUser->getNewsSubscription();
            if ($oNewsletter->getOptinStatus() > 0) {
                $cNewsletterOptIns++;
                hdio2c_sync::saveSync("newsletterOptIn", $oUser->getId());
                if ($oNewsletter->getOptInEmailStatus() > 0) {
                    $cEmailOptIns++;
                    hdio2c_sync::saveSync("emailOptIn", $oUser->getId());
                }
            }
        }

        $result = new stdClass();
        $result->Users = $cUser;
        $result->Orders = $cOrders;
        $result->SavedBaskets = $cSavedBaskets;
        $result->WishLists = $cWishLists;
        $result->NoticeLists = $cNoticeList;
        $result->NewsletterAbos = $cNewsletterOptIns;
        $result->NewsletterAbosWithDoubleOtIn = $cEmailOptIns;
        $this->_oSmarty->assign("json", json_encode($result));
    }

    /**
     * Clears the Sync Database
     */
    protected function flushDb() {
        oxDb::getDb()->Execute("TRUNCATE TABLE hdio2c_sync");
    }

    protected function doSync() {
        
        $start = microtime(true);
        $aSync = oxNew('hdio2c_syncList');
        /* @var $aSync hdio2c_syncList */
        $aSync->getSyncItems();
        $array = array();
        foreach ($aSync as $sync) {
            $array[$sync->getId()] = $sync->sync();
        }
        CopernicaHandler::getInstance()->getClient()->run(); 
        $o = new stdClass(); 
        $o->result = true; 
        $o->time = (microtime(true) - $start);
        $o->results = $array;
        $this->_oSmarty->assign("json", json_encode($o));

    }

    /**
     * Saves a new Newsletter Campaign to Copernica 
     */
    protected function saveNlCampaign() {
        $sCampaign = oxConfig::getParameter("config", true);
        $campaignConfig = json_decode($sCampaign);
        $config = hdio2cConfig::getConfig()->products;
        $cop = CopernicaHandler::getInstance();
        $creates = array();

        foreach ($campaignConfig->selectedProducts as $product) {
            $campaign = hdio2cHelper::oxid2CopernicaProduct($product->id, "products");
            $campaign[$config->fieldMatching['Campaign']->name] = $campaignConfig->name;
            $creates[] = $campaign;
            $cop->Execute("database_createProfile", array("id" => $config->id, 'fields' => $campaign), false);
        }

        $cop->getClient()->run();
        $res = new stdClass();
        $res->result = true;
        $res->created = $creates;
        $res->config = $campaignConfig;
        $res->sCampaign = $sCampaign;

        $this->_oSmarty->assign("json", json_encode($res));
    }

    /**
     * Formates the Exception to be read in JS 
     * @param Exception $ex
     * @param string $text 
     */
    protected function returnException($ex, $text = "") {
        if ($text != "") {
            $text = ', "info": "' . $text . '"';
        }
        $this->_oSmarty->assign("json", '{"result": false, "message": "' . $ex->getMessage() . '"' . $text . '}');
    }

}

?>
