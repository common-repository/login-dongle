<?php

class LoginDongleSiteAdmin
{
    const MAXLEN_MESSAGE   = 1000;
    
    const OPTION_GROUP = 'login_dongle_settings';
    
    const THIS_PAGE_ID = 'login_dongle_configuration';
    const NICE_SECTION_ID = 'nice_settings';
    
    /**
     * @var LoginDongleOption
     */
    protected $option;
    
    public function __construct()
    {
        require_once 'LoginDongleOption.php';
        $this->option = new LoginDongleOption();
        
        add_action('admin_menu', array($this, 'addPage'));
        add_action('admin_init', array($this, 'bindAPI'));
    }
    
	/**
     * Returns the default settings for the site option.
     * 
     * @return struct
     */
    protected function defaultSettings()
    {
        $result = array(
            'message'   => LoginDongle::__('What did you expect?'),
        );
        return $result;
    }
    
    /**
     * Creates an admin menu item and binds it to the site settings page.
     * 
     * This method is a handler bound to the admin_menu action.
     */
    public function addPage()
    {
        add_options_page(LoginDongle::__('Login Dongle Settings'), 
        		'Login Dongle', 'manage_options', self::THIS_PAGE_ID, array($this, 'handleGET'));
    }
    
    /**
     * Binds adminSiteSettings* handlers to the events of the 'simplified' admin API.
     * 
     * This method is a handler bound to the admin_init action.
     */
    public function bindAPI()
    {
        register_setting(self::OPTION_GROUP, LoginDongle::OPTION_NAME, array($this, 'handlePOST'));
        
        add_settings_section(self::NICE_SECTION_ID, LoginDongle::__('Nice Settings'), 
                array($this, 'handleGET_NiceSection_HelpText'), self::THIS_PAGE_ID);
        add_settings_field('message', LoginDongle::__('Message'), 
                array($this, 'handleGET_NiceSection_MessageField'), self::THIS_PAGE_ID, self::NICE_SECTION_ID);
    }
    
    /**
     * Echoes the html for the content of the site settings page.
     * 
     */
    public function handleGET()
    {
?>
<div>
    <h2>Login Dongle</h2>
<p><?php LoginDongle::_e(

'Login Dongle makes the login for your personal use only, by means of a conventional challenge &gt;&gt; response mechanism.'

); ?></p>
    <form action="options.php" method="post">
<?php 
        settings_fields(self::OPTION_GROUP); 
        do_settings_sections(self::THIS_PAGE_ID);
?>
        <p><br />
        <input type="submit" name="Submit" value="<?php LoginDongle::_e('Save changes'); ?>" 
        class="button" /></p>
    </form>
</div>
<?php
    }
    
    /**
     * Validates site settings POST data.
     * 
     * @param struct $data
     * @return struct
     */
    public function handlePOST( $data )
    {
        $data['message'] = substr(trim($data['message']), 0, self::MAXLEN_MESSAGE);
        $default = $this->defaultSettings();
        $sitedata = $this->option->get();
        $data = array_merge($default, $sitedata, $data);
        return $data;
    }
    
    /**
     * Echoes the html for the description of the site settings Nice section.
     * 
     */
    public function handleGET_NiceSection_HelpText()
    {
?>
<p><?php LoginDongle::_e(

'When possible attackers try to log into your blog without the current login dongle, 
they will see this message. Use HTML as you like.'

); ?></p>
<?php
    }
    
    /**
     * Echoes the html for the site settings message field
     * 
     */
    public function handleGET_NiceSection_MessageField() 
    {
        $data = $this->option->get();
        if (! (is_array($data) && isset($data['message']) && ! empty($data['message'])))
        {
            $default = $this->defaultSettings();
            $data['message'] = $default['message'];
        }
?>
<textarea id="message" name="<?php echo LoginDongle::OPTION_NAME; ?>[message]" ><?php 
    LoginDongle::_e($data['message']);
?></textarea><br />
<span class="description"><?php printf(LoginDongle::__('1 to %s chars.'), self::MAXLEN_MESSAGE); ?></span>
<?php
    }
}
