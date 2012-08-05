<?php

require_once("asyncsoapclient.php");

/**
 * Class for Easier handling of PomAsyncSoapClient and connection to Copernica
 */
class CopernicaHandler
{

    /**
     * Coerpnica SoapClient
     * @var PomAsyncSoapClient
     */
    protected $oSoapClient;

    /**
     * Config object
     * @var hdio2cConfig 
     */
    protected $oDbConfig;

    /**
     * Oxid Config Object
     * @var oxConfig 
     */
    protected $oConfig;

    /**
     * Oxid Language Object
     * @var oxLang 
     */
    protected $oLang;

    /**
     * Constructor 
     */
    public function __construct()
    {
        $this->check();
    }

    /**
     * Getter for Copernica client
     * @return PomAsyncSoapClient 
     */
    public function getClient()
    {
        if (!is_object($this->oSoapClient))
        {
            $this->oSoapClient = new PomAsyncSoapClient(
                            $this->getConfig("hdio2c_host"),
                            $this->getConfig("hdio2c_user"),
                            $this->getConfig("hdio2c_acc"),
                            $this->getConfig("hdio2c_pass")
            );
        }
        return $this->oSoapClient;
    }

    /**
     * Gets the Copernica DatabaseConfig
     * @return hdio2cConfig 
     */
    protected function getDBConfig()
    {
        if (!isset($this->oDbConfig))
        {
            $config = $this->getConfig()->getShopConfVar("hdio2cConfig");
            if ($config !== null)
            {
                $this->oDbConfig = $config;
            } else
            {
                throw oxNew("oxException", $this->translate("HDIO2C_NO_DBCONFIG"));
            }
        }
        return $this->oDbConfig;
    }

    /**
     * Checks if a Connection to Copernica is possible with provided credentials
     * @param string $host Host URL for Copernica
     * @param string $user Username
     * @param string $account Accountname
     * @param string $pass Password
     * @return string|true true if successful else Errormessage
     */
    public static function checkLogin($host, $user, $account, $pass)
    {
        try
        {
            $oLang = oxLang::getInstance();
            $client = new PomAsyncSoapClient($host, $user, $account, $pass);

            if (!is_object($client))
                return $oLang->translateString("COPERNICAERROR_UNREACHABLE");

// check for invalid login
            $objarray = get_object_vars($client);

// return API Error
            if (isset($objarray['__soap_fault']))
                return $oLang->translateString("COPERNICAERROR_LOGINFAILURE");
        } catch (oxException $ex)
        {
            return $ex->getMessage();
        }

        return true;
    }

    /**
     * Checks if Connection is Possible
     * @return boolean
     * @throws oxException 
     */
    public function check()
    {
        if (!is_object($this->getClient()))
            throw oxNew("oxException", $this->translate("COPERNICAERROR_UNREACHABLE"));

// check for invalid login
        $objarray = get_object_vars($this->getClient());

// return API Error
        if (isset($objarray['__soap_fault']))
            throw oxNew("oxException", $this->translate("COPERNICAERROR_LOGINFAILURE"));

        return true;
    }

    /**
     * Returns an oxConfig Instance or the Parameter Value if provided
     * @param string $param
     * @return oxConfig|string if $param is provided will return string
     */
    public function getConfig($param = null)
    {
        if (!isset($this->oConfig))
        {
            $this->oConfig = oxConfig::getInstance();
        }

        if ($param == null)
        {
            return $this->oConfig;
        } else
        {
            return $this->oConfig->getShopConfVar($param);
        }
    }

    /**
     * Gets the Databse ID
     * @return integer 
     */
    protected function getDbId()
    {
        return $this->getDBConfig()->account->id;
    }

    /**
     * Gets the Collection Id.
     * @param string $collection Possible Collections are "orders", "orderItems", "basket", "addresses"
     */
    protected function getCollectionId($collection)
    {
        if ($collection == "orders" || $collection == "orderItems" || $collection == "basket" || $collection == "addresses")
        {
            return $this->getDbConfig()->$collection->id;
        }
        throw new oxException('Wrong Value for $collection, Possible Collections are "Order", "OrderItems", "Basket", "Address" ');
    }

    /**
     * Returns a Profile with given Oxid
     * @param string $oxid
     * @param boolean $run
     * @return mixed 
     */
    public function getProfile($oxid, $run = true)
    {
        return $this->Execute("Database_searchProfiles", array(
                    'id' => $this->getDbId(),
                    'requirements' =>
                    array(
                        $this->getClient()->toObject(
                                array(
                                    'fieldname' => 'oxid',
                                    'value' => $oxid,
                                    'oeprator' => '='
                                )
                        )
                        )), $run
        );
    }

    /**
     * Translates a String
     * @param string $stringtoTranslate
     * @return string 
     */
    protected function translate($stringtoTranslate)
    {
        if (!isset($this->oLang))
        {
            $this->oLang = oxLang::getInstance();
        }
        return $this->oLang->translateString($stringtoTranslate);
    }

    /**
     * Executes a call to Copernica API
     * @param string $function
     * @param array $params
     * @param bool $run
     * @return integer|mixed if $run == false will return Requestid else jsonString or encoded Object( if json == true)  
     */
    public function Execute($function, $params, $run = true)
    {
        $res = $this->getClient()->$function($params);

        if ($run)
        {
            return $this->result($res);
        }

        return $res;
    }

    /**
     * Gets the Result / can convert directly to 
     * @param integer $resID
     * @return mixed encoded Object 
     */
    public function result($resID)
    {
        return $this->getClient()->result($resID);
    }

    /**
     * Checks existance of Database
     * @param int|string $id
     * @return string|true errorMessage or 
     */
    public function checkDatabase($id)
    {
        if (!isset($id))
        {
            return oxLang::getInstance()->translateString("HDIO2C_NODB");
        }
        $res = $this->Execute("Account_Database", array("identifier" => $id));
        if (!is_object($res))
        {
            return $this->translate("HDIO2C_NODB");
        }
        return true;
    }

    /**
     * Gets all Databases of the Account
     * @param bool $allproperties
     * @return object 
     */
    public function getDatabases($allproperties = false)
    {
        $params = array();
        if ($allproperties)
        {
            $params["allproperties"] = 1;
        }
        return $this->Execute("Account_databases", $params);
    }

    /**
     * Returns a Copernica Database object
     * @param int/string $id
     * @param bool $allproperties
     * @return object 
     */
    public function getDatabase($id, $allproperties = false)
    {
        $params = array('identifier' => $id);

        if ($allproperties)
        {
            $params["allproperties"] = 1;
        }
        return $this->Execute("Account_Database", $params);
    }

    /**
     * Creates a Database
     * @param string $name
     * @param boolean $run
     * @return mixed
     * @throws oxException 
     */
    public function createDatabase($name, $run = true)
    {
        if (is_object($this->checkDatabase($name)))
        {
            throw oxNew("oxException", $this->translate("HDIO2C_DATABASE_EXISTS"));
        } else
        {
            return $this->Execute("Account_createDatabase", array("name" => $name), $run);
        }
    }
    
    
    /**
     * Creates a Collection
     * @param integer $id
     * @param string $name
     * @param boolean $run
     * @return mixed
     */
    public function createCollection($id, $name, $run = true)
    {
            return $this->Execute("Database_createCollection", array("id"=> $id,"name" => $name), $run);
    }
    
    
    /**
     *Creates a Fiel in a database or Collection
     * @param string $type Should be: database or collection
     * @param integer $id The ID of the Database or Colelction to create the Field in
     * @param string $name The name of the Field
     * @param boolean $run Defines if it executes Synchronous (true) or asynchronous(false). Default: true
     * @param string $fieldType The Fieldtype. text(default), date, big, float
     * @return mixed 
     */
    public function createField($type, $id, $name, $run = true, $fieldType = "text")
    {
        
        if(strtolower($type) == "database" || strtolower($type) == "collection")
        {
            
            $aParams = array("id" => $id, "name" => $name, "type" => $fieldType);
            if($type == "big")
            {
                $aParams["big"] = true;
            }
            if($type == "text") 
            {
                $aParams["length"] = 255; 
            }
            return $this->Execute($type."_createField", $aParams , $run);
        }
        return false; 
    }
    
    private static $_instance = null;
    /**
     * Returns instance of Copernica Handler
     * @return CopernicaHandler
     */
    public static function getInstance()
    {
        if(!self::$_instance instanceof CopernicaHandler)
        {
            self::$_instance= new CopernicaHandler();
        }
        return self::$_instance;
    }
}

?>
