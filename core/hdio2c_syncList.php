<?php

class hdio2c_syncList extends oxList
{

    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'hdio2c_sync';

    /**
     * Returns the First {$number} not modified Sync entries; 
     * 
     * @param int $number
     */
    public function getSyncItems($number = 50) {
        $select = "SELECT * FROM hdio2c_sync WHERE state IS NULL ORDER BY added LIMIT {$number}";
        $this->selectString($select);
    }

}

?>
