<?php

/**
 * This Class represents the Database and Collection Configuration for Copernica 
 */
class hdio2cConfig
{

    /**
     * Saves informations about Account Configuration
     * @var hdio2cCollection 
     */
    public $account = null;

    /**
     * Saves information about Order Configuration
     * @var hdio2cCollection 
     */
    public $orders = null;

    /**
     * Saves information about OrderItems Configuration
     * @var hdio2cCollection 
     */
    public $orderItems = null;

    /**
     * Saves information about Basket Configuration
     * @var hdio2cCollection 
     */
    public $basket = null;

    /**
     * Saves information about Address Configuration
     * @var hdio2cCollection 
     */
    public $addresses = null;

    /**
     * Saves information about CampaignsProducts Configuration
     * @var hdio2cCollection 
     */
    public $products = null;

    /**
     * Constructor for Object, $obj is an anonymus Object provided Knockouts 
     * ConfigModel
     * @param type $obj [optional]
     */
    public function __construct($obj = null) {
        if ($obj === null) {
            $this->account = new hdio2cCollection();
            $this->orders = new hdio2cCollection();
            $this->orderItems = new hdio2cCollection();
            $this->basket = new hdio2cCollection();
            $this->addresses = new hdio2cCollection();
            $this->products = new hdio2cCollection();
        } else {
            $this->account = new hdio2cCollection($obj->account);
            $this->orders = new hdio2cCollection($obj->orders);
            $this->orderItems = new hdio2cCollection($obj->orderItems);
            $this->basket = new hdio2cCollection($obj->basket);
            $this->addresses = new hdio2cCollection($obj->addresses);
            $this->products = new hdio2cCollection($obj->products);
        }
    }

    /**
     * Gets the Config From Database
     * @return hdio2cConfig 
     */
    public static function getConfig() {
        return unserialize(oxConfig::getInstance()->getShopConfVar('hdio2cConfig'));
    }

    /**
     * Writes the Config to Database.
     * @param hdio2cConfig $config 
     */
    public static function saveConfig($config) {
        oxConfig::getInstance()->saveShopConfVar("string", 'hdio2cConfig', serialize($config), null, "module:hdioxid2copernica");
    }

    /**
     * Writes the Config to Database.
     * 
     */
    public function save() {
        $this->saveConfig($this);
    }

}

class hdio2cField
{

    /**
     * The ID of the Field
     * @var integer 
     */
    public $id;

    /**
     * The Name of the Field
     * @var string 
     */
    public $name;

    function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

}

/**
 * This Class represents a Collection with its Name ID and the FieldsMapping 
 */
class hdio2cCollection
{

    /**
     * The Copernica ID of the Collection
     * @var integer 
     */
    public $id;

    /**
     * The Name of the Database
     * @var string 
     */
    public $name;

    /**
     * The Field Mapping: Field => CopernicaFieldId
     * @var hdio2cField[]
     */
    public $fieldMatching = array();

    function __construct($obj = null) {
        if ($obj !== null) {
            $this->id = $obj->collection->id;
            $this->name = $obj->collection->name;
            foreach ($obj->fieldMatching as $match) {
                $field = new hdio2cField($match->match->id, $match->match->name);
                $this->fieldMatching[$match->internal] = $field;
            }
        }
    }

}

?>
