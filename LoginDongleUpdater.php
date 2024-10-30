<?php 

class LoginDongleUpdater
{
    protected $option;
    
    /**
     * Retrieves the version of this plugin.
     * 
     * @return string
     */
    protected function codeVersion()
    {
        $plugin_data = get_file_data(LOGIN_DONGLE_FILENAME, array('Version' => 'Version'), 'plugin');
        $result = $plugin_data['Version'];
        return $result;
    }
    
    /**
     * Retrieves the version of the data of this plugin.
     * 
     * @return string
     */
    protected function dataVersion()
    {
        $sitedata = $this->option->keysGet(array('message', 'version'));
        if (isset($sitedata['message']))
        {
            return isset($sitedata['version']) ? $sitedata['version'] : '1';
        }
        return $this->codeVersion();
    }
    
    /**
     * Returns TRUE if data version is lower than code version.
     * 
     */
    protected function needUpdate()
    {
        $dataVersion  = $this->dataVersion();
        $checkVersion = $this->codeVersion();
        $result = Ando_Version::compare("$dataVersion < $checkVersion");
        return $result;
    }
    
    /**
     * Stores the version of the data of this plugin.
     * 
     * @return string
     */
    protected function dataVersionUpdate($version)
    {
        $this->option->keysSet(array('version' => $version));
    }
    
    /**
     * up to before v1.3.0 user settings did not exist and admin's 'challenge' and 'response'
     * were stored among site settings into the login_dongle option, alongside 'message' 
     */
    protected function moveOptionsFromSiteToUser()
    {
        $users = get_users(array('fields' => array('ID','user_login')));
        echo 'Fixing user options for up to ' . count($users) . ' users...' . "\n";
        $sitedata = $this->option->get();
        if (! isset($sitedata['challenge']))
        {
            echo 'nothing to do. (challenge not set)' . "\n";
            return;
        }
        $userdata['challenge'] = $sitedata['challenge'];
        $userdata['response']  = $sitedata['response'];
        foreach ($users as $user) 
        {
            echo '  - for user ' . $user->user_login . ': ';
            $this->option->set($userdata, $user->user_login);
            echo 'done' . "\n";
        }
        
        echo 'Fixing site options: ' . "\n";
        unset($sitedata['challenge'], $sitedata['response']);
        $this->option->set($sitedata);
        echo 'done' . "\n";
    }
    
    /**
     * up to before v1.5.0 user settings included only 'challenge' and 'response'
     * but now there is a 'field' structure that contains precomputed values
     */
    protected function addFieldStructureToOptions()
    {
        require_once 'LoginDongleUserAdmin.php';
        $users = get_users(array('fields' => array('ID','user_login')));
        echo 'Fixing user options for up to ' . count($users) . ' users...' . "\n";
        foreach ($users as $user) 
        {
            $username = $user->user_login;
            echo '  - for user ' . $username . ': ';
            $data = $this->option->get($username);
            if (! isset($data['challenge']))
            {
                echo 'nothing to do (challenge not set)' . "\n";
                continue;
            }
            $data['field']['form']   = LoginDongleUserAdmin::fieldFormPOST($data);
            $data['field']['xmlrpc'] = LoginDongleUserAdmin::fieldXmlrpcPOST($data);
            $this->option->keysSet($data, $username);
            echo 'done' . "\n";
        }
    }
    
    /**
     * Updates to the given $checkVersion using the method with the given $update name.
     * 
     * @param string $checkVersion
     * @param string $update
     */
    protected function updateToVersion($checkVersion, $update)
    {
        $dataVersion = $this->dataVersion();
        if (Ando_Version::compare("$dataVersion < $checkVersion"))
        {
            if (is_callable(array($this, $update)))
            {
                $args = func_get_args();
                $args = array_splice($args, 2);
                call_user_func_array(array($this, $update), $args);
            }
            $this->dataVersionUpdate($checkVersion);
        }
    }
    
    /**
     * Updates anything from previous versions.
     * Sends an email to the admin with a report.
     * 
     * This method is a handler bound to the activation hook.
     */
    protected function update()
    {
        $dataVersion = $this->dataVersion(); //version before updating
        
        ob_start(); ///////////////////////////////activation event handlers cannot output anything
        
        $this->updateToVersion('1.3.0', 'moveOptionsFromSiteToUser');
        $this->updateToVersion('1.5.0', 'addFieldStructureToOptions');
        
        $output = ob_get_clean(); ///////////////////////////////so I save everything to a log file
        
        $version = $this->codeVersion();
        $this->dataVersionUpdate($version);
        
        if (Ando_Version::compare("$dataVersion < $version"))
        {
            $data = 'The Login Dongle plugin has been updated to version ' . $version . '. Details follow.' . "\n" . $output;
            LoginDongle::logToFile($data, __FILE__);
        }
    }
    
    public function __construct()
    {
        require_once 'Ando/Version.php';
        require_once 'LoginDongleOption.php';
        $this->option = new LoginDongleOption();
        
        //register_activation_hook(LOGIN_DONGLE_FILENAME, array($this, 'update'));
        if ($this->needUpdate())
        {
            $this->update();
        }
    }
}
