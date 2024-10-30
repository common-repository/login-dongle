<?php
/**
 * Login Dongle Plugin
 * 
 * @author Andrea Ercolino, http://notelog.com
 */

//TODO try to implement handleFormPOST and handleXmlrpcPOST by hooking into the the 'authenticate' filter (see http://core.trac.wordpress.org/ticket/20704) 

require 'LoginDongle.php';

class LoginDonglePlugin
{
    /**
     * @var LoginDongleOption
     */
    protected $option;
    
    /**
     * Constructor
     * 
     * This binds events to handlers
     */
    public function __construct()
    {
        LoginDongle::loadTranslations();
        
        require_once 'LoginDongleOption.php';
        $this->option = new LoginDongleOption();
        add_action('delete_user', array($this->option, 'remove'));
        
        require_once 'LoginDongleUpdater.php';
        new LoginDongleUpdater();
        
        require_once 'LoginDongleSiteAdmin.php';
        new LoginDongleSiteAdmin();
        
        require_once 'LoginDongleUserAdmin.php';
        new LoginDongleUserAdmin();
        
        add_action('login_enqueue_scripts', array($this, 'handleFormGET'));
        add_action('login_init', array($this, 'handleFormPOST'), 0); //highest priority
        
        add_filter('wp_xmlrpc_server_class', array($this, 'wpXmlrpcServerClass_filter'), 0);
        add_action('login_init_xmlrpc', array($this, 'handleXmlrpcPOST'), 0); //highest priority
    }
    
    /**
     * Loads the jQuery library into the login page, so the bookmarklet can use it.
     * 
     * This method is a handler bound to the login_enqueue_scripts action.
     */
    public function handleFormGET()
    {
        wp_enqueue_script('jquery');
    }
    
    /**
     * Validates POST data. 
     * If the challenge/response field is not properly set, it dies with the configured message.
     * 
     * Action for login_init.
     * 
     * WARNING
     * Since version 1.3.0 this method dies only if the POST user has configured a login dongle.
     * Up to version 1.2.2 this method died only if the SITE admin had configured a login dongle.
     */
    public function handleFormPOST()
    {
        if ('POST' != $_SERVER['REQUEST_METHOD'])
    	{
    		return; //if the request is not a POST
    	}
    	$login_url   = LoginDongle::scriptUrl(wp_login_url());
    	$current_url = LoginDongle::scriptUrl($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    	if ($current_url != $login_url)
        {
            return; //if the page is not the login
        }
        $username = sanitize_user(isset($_POST['log']) ? $_POST['log'] : $_POST['user_login']);
    	if (empty($username))
    	{
    	    return; //if a username is not given
    	}
    	$userdata = $this->option->get($username);
    	if (! isset($userdata['challenge']) || empty($userdata['challenge']))
    	{
    	    return; //if a challenge is not configured
    	}
    	list($challenge, $response) = $userdata['field']['form'];
    	$isValid = isset($_POST[$challenge]) && $_POST[$challenge] == $response;
    	unset($_POST[$challenge], $_REQUEST[$challenge]);
    	if (! $isValid) 
    	{ 
    	    $sitedata = $this->option->get();
    		wp_die($sitedata['message']);
    	}
    }
    
    /**
     * Validates XMLRPC data.
     * If the challenge/response field is not properly set, it dies with the configured message.
     * 
     * Action for login_init_xmlrpc.
     * 
     */
    public function handleXmlrpcPOST( $username )
    {
        if (! XMLRPC_REQUEST)
        {
            return; //if the request is not XMLRPC
        }
        $username = sanitize_user($username);
    	if (empty($username))
    	{
    	    return; //if a username is not given
    	}
    	$userdata = $this->option->get($username);
    	if (! isset($userdata['challenge']) || empty($userdata['challenge']))
    	{
    	    return; //if a challenge is not configured
    	}
    	list($challenge, $response) = $userdata['field']['xmlrpc'];
       	$isValid = isset($_GET[$challenge]) && $_GET[$challenge] == $response;
       	global $wp_version;
       	if (Ando_Version::compare("$wp_version >= 3.4"))
       	{
       	    //before 3.4 wordpress wrongfully managed some XML-RPC calls (e.g. wp.getComments)
    	    unset($_GET[$challenge], $_REQUEST[$challenge]);
       	}
    	if (! $isValid) 
    	{ 
    	    $sitedata = $this->option->get();
    		wp_die($sitedata['message']);
    	}
    }
    
    /**
     * Loads the XMLRPC replacement class and returns its name.
     * 
     * Filter for wp_xmlrpc_server_class.
     * 
     */
    public function wpXmlrpcServerClass_filter()
    {
        require_once 'LoginDongleXmlrpcServer.php';
        return 'LoginDongleXmlrpcServer';
    }
    
    /**
     * Uninstalls this plugin, cleaning up all data.
     * This is called from uninstall.php without instatiating an object of this class.
     * 
     */
    static public function uninstall()
    {
        require_once 'LoginDongleOption.php';
        $option = new LoginDongleOption();
        $option->remove_all();
    }
}
