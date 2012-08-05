<?php

/**
 * Description of hdio2c_user
 *
 * @author Entwicklung
 */
class hdio2c_oxuser extends hdio2c_oxuser_parent
{

    /**
     * Saves the User to SyncList
     * @param bool $new
     */
    protected function saveUser2Copernica($new = false) {
        $type = "userUpdate";
        if ($new) {
            $type = "newUser";
        }
        hdio2c_sync::saveSync($new?"newUser":"userUpdate", $this->getId());
    }


    /**
     * Hooking onto User Creation Function 
     * @see oxUser::createUser()
     */
    public function createUser() {
        $ret = parent::createUser();

        if ($ret) {
            $this->saveUser2Copernica(true);
        }
        return $ret;
    }

    /**
     * Hooking onto Change Userdata Function 
     * @see oxUser::changeUserData()
     */
    public function changeUserData($sUser, $sPassword, $sPassword2, $aInvAddress, $aDelAddres) {
        $ret = parent::changeUserData($sUser, $sPassword, $sPassword2, $aInvAddress, $aDelAddres);

        if ($ret) {
            $this->saveUser2Copernica(false);
        }
        return $ret;
    }
    
}

?>