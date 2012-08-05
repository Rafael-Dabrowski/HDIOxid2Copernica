<?php

/**
 * Helper Class 
 */
class hdio2cHelper
{

    /**
     * Contains all fields and its fieldType;
     * @var array 
     */
    public static $fields = array(
        "account" => array(
            "OXID" => "text",
            "Firstname" => "text",
            "Lastname" => "text",
            "Salutation" => "text",
            "Email" => "email",
            "Newsletter" => "boolean",
            "Group" => "text",
            "CustomerId" => "text",
            "Birthday" => "date",
            "Registered" => "date",
            "Bonus" => "float"),
        "orders" => array(
            "OXID" => "text",
            "OrderNumber" => "text",
            "OrderDate" => "date",
            "PaymentType" => "text",
            "TotalBrutto" => "float",
            "TotalNetto" => "float",
            "ShippingCost" => "float",
            "VAT" => "float",
            "Discount" => "float",
            "Currency" => "text",
            "TotalWeight" => "text",
            "ShippingType" => "text"),
        "basket" => array(
            "OXID" => "text",
            "SKU" => "text",
            "Name" => "text",
            "Description" => "big",
            "Price" => "float",
            "TotalPrice" => "float",
            "VAT" => "float",
            "Thumbnail" => "text",
            "Icon" => "text",
            "Picture" => "text",
            "Variant" => "text",
            "URL" => "text",
            "Quantity" => "float",
            "PersonalizedParameter" => "text",
            "BasketType" => "text"),
        "orderItems" => array(
            "OXID" => "text",
            "OrderID" => "text",
            "SKU" => "text",
            "Name" => "text",
            "Description" => "big",
            "Price" => "text",
            "TotalPrice" => "text",
            "VAT" => "text",
            "Thumbnail" => "text",
            "Icon" => "text",
            "Picture" => "text",
            "Variant" => "text",
            "URL" => "text",
            "Quantity" => "float",
            "PersonalizedParameter" => "text"),
        "addresses" => array(
            "OXID" => "text",
            "Firstname" => "text",
            "Lastname" => "text",
            "Street" => "text",
            "Number" => "text",
            "Zipcode" => "text",
            "City" => "text",
            "Country" => "text",
            "Telephone" => "text",
            "Mobile" => "text",
            "Fax" => "text",
            "Company" => "text"),
        "products" => array(
            "Campaign" => "text",
            "OXID" => "text",
            "SKU" => "text",
            "Name" => "text",
            "Description" => "big",
            "Price" => "text",
            "VAT" => "text",
            "Thumbnail" => "text",
            "Icon" => "text",
            "Picture" => "text",
            "URL" => "text")
    );

    /**
     * The install/update Script 
     * @todo Installscript needs to be implemented
     */
    public static function install() {
        $install = dirname(__FILE__) . '/../install/install.sql';
        // echo $install; 
        if (file_exists($install)) {
            $query = file_get_contents($install);
            oxDb::getDb()->execute($query);
        }
    }

    /**
     * Returns the Ajax URL for Using in Javascript
     * @return string
     */
    public static function getAjaxUrl() {
        return str_replace("&amp;", "&", oxConfig::getInstance()->getShopCurrentUrl() . "cl=hdioxid2copernica_ajax");
    }

    /**
     * Creates an Array of Productfields for Usage in Copernica call.
     * 
     * @param string $oxid
     * @param string $collection
     * @var oxArticle $oArticle
     * @return array
     */
    public static function oxid2CopernicaProduct($oxid, $collection) {
        $field = array();

        $oArticle = oxNew("oxarticle");
        /* @var $oArticle oxArticle */
        $oArticle->load($oxid);
        foreach (hdio2cConfig::getConfig()->$collection->fieldMatching as $name => $obj) {
            switch ($name) {
                case "OXID":
                    $field[$obj->name] = $oxid;
                    break;
                case "SKU":
                    $field[$obj->name] = $oArticle->oxarticles__oxartnum->value;
                    break;
                case "Name":
                    $field[$obj->name] = $oArticle->oxarticles__oxtitle->value;
                    break;
                case "Description":
                    $field[$obj->name] = $oArticle->oxarticles__oxshortdesc->value;
                    ;
                    break;
                case "Price":
                    $field[$obj->name] = $oArticle->getFPrice();
                    break;
                case "VAT":
                    $field[$obj->name] = $oArticle->getPrice()->getVat();
                    break;
                case "Picture":
                    $field[$obj->name] = $oArticle->getPictureUrl();
                    break;
                case "Thumbnail":
                    $field[$obj->name] = $oArticle->getThumbnailUrl();
                    break;
                case "Icon":
                    $field[$obj->name] = $oArticle->getIconUrl();
                    break;
                case "URL":
                    $field[$obj->name] = $oArticle->getLink();
                    break;
                default:
                    break;
            }
        }
        return $field;
    }

}

?>
