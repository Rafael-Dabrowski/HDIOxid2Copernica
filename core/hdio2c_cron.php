<?php

class hdio2c_cron extends hdio2c_cron_parent
{

    /**
     * Syncs Data with Copernica
     * @todo Cron Jobs to Sync data with Copnernica
     */
    public function doListupdate() {
        $aSync = oxNew('hdio2c_syncList');
        /* @var $aSync hdio2c_syncList */
        $aSync->getSyncItems();
        foreach ($aSync as $sync) {
           $sync->sync(); 
        }
    }

    /**
     * @see oxMaintenace::execute()
     */
    public function execute() {
        parent::execute();

        $this->doListupdate();
    }

}

?>
