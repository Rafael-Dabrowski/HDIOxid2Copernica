<?php
$sMetaDataVersion = '1.0';

$aModule = array(
    'id' => 'hdioxid2copernica',
    'title' => 'HDIOxid2Copernica',
    'description' => 'Bietet Möglichkeiten Die daten zwischen Oxid und Copernica zu synchronisieren und Kampagnen zu starten.',
    'version' => '0.1.0',
    'author' => 'Rafael Dabrowski / HEINER DIRECT GmbH & Co. KG',
    'email' => 'rafael.dabrowski@heiner-direct.com',
    'extend' => array(
        'oxmaintenance' => 'hdioxid2copernica/core/hdio2c_cron',
        'oxuser' => 'hdioxid2copernica/core/hdio2c_oxuser',
        'oxorder' => 'hdioxid2copernica/core/hdio2c_oxorder',
        'oxnewssubscribed' => 'hdioxid2copernica/core/hdio2c_oxnewssubscribed',
        'oxuserbasket' => 'hdioxid2copernica/core/hdio2c_oxuserbasket',
        'oxbasket' => 'hdioxid2copernica/core/hdio2c_oxbasket'
    ),
    'files' => array(
        'CopernicaHandler' => 'hdioxid2copernica/api/copernicaHandler.php',
        'hdioxid2copernica_config' => 'hdioxid2copernica/admin/hdioxid2copernica_config.php',
        'hdioxid2copernica_shopConfig' => 'hdioxid2copernica/admin/hdioxid2copernica_shopConfig.php',
        'hdioxid2copernica_productNewsletter' => 'hdioxid2copernica/admin/hdioxid2copernica_productNewsletter.php',
        'hdioxid2copernica_statistics' => 'hdioxid2copernica/admin/hdioxid2copernica_statistics.php',
        'hdioxid2copernica_ajax' => 'hdioxid2copernica/admin/hdioxid2copernica_ajax.php',
        'hdio2cConfig' => 'hdioxid2copernica/core/hdio2cConfig.php',
        'hdio2cHelper' => 'hdioxid2copernica/core/hdio2cHelper.php',
        'hdio2c_sync' => 'hdioxid2copernica/core/hdio2c_sync.php',
        'hdio2c_syncList' => 'hdioxid2copernica/core/hdio2c_syncList.php'
    ),
    'settings' => array(
        array('group' => 'main', 'name' => 'hdio2c_host', 'type' => 'str', 'value' => ''),
        array('group' => 'main', 'name' => 'hdio2c_user', 'type' => 'str', 'value' => ''),
        array('group' => 'main', 'name' => 'hdio2c_pass', 'type' => 'str', 'value' => ''),
        array('group' => 'main', 'name' => 'hdio2c_acc', 'type' => 'str', 'value' => ''),
        array('group' => 'DBSettings', 'name' => 'hdio2c_db', 'type' => 'str', 'value' => ''),
        array('group' => 'DBSettings', 'name' => 'hdio2c_basketcol', 'type' => 'int', 'value' => ''),
        array('group' => 'DBSettings', 'name' => 'hdio2c_ordercol', 'type' => 'int', 'value' => ''),
        array('group' => 'DBSettings', 'name' => 'hdio2c_orderitemcol', 'type' => 'int', 'value' => ''),
        array('group' => 'DBSettings', 'name' => 'hdio2c_addresscol', 'type' => 'int', 'value' => '')
    ),
    'templates' => array(
        "hdioxid2copernica_config.tpl" => "hdioxid2copernica/out/admin/tpl/hdioxid2copernica_config.tpl",
        "hdioxid2copernica_ajax.tpl" => "hdioxid2copernica/out/admin/tpl/hdioxid2copernica_ajax.tpl",
        "hdioxid2copernica_shopConfig.tpl" => "hdioxid2copernica/out/admin/tpl/hdioxid2copernica_shopConfig.tpl",
        "hdioxid2copernica_productNewsletter.tpl" => "hdioxid2copernica/out/admin/tpl/hdioxid2copernica_productNewsletter.tpl",
        "hdioxid2copernica_statistics.tpl" => "hdioxid2copernica/out/admin/tpl/hdioxid2copernica_statistics.tpl"
    ),
    'blocks' => array(
        array('template' => 'headitem.tpl', 'block' => 'admin_headitem_incjs', 'file' => 'hdio2cjs.tpl'),
        array('template' => 'headitem.tpl', 'block' => 'admin_headitem_inccss', 'file' => 'hdio2ccss.tpl'),
    )
);
?>
