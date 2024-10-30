<?php
/**
 * LoginDongle XMLRPC server implementation, based on WordPress'
 *
 */
class LoginDongleXmlrpcServer extends wp_xmlrpc_server 
{
	/**
	 * Fire the login_init_xmlrpc hook and log user in.
	 *
	 * @param string $username User's username.
	 * @param string $password User's password.
	 * @return mixed WP_User object if authentication passed, false otherwise
	 * 
	 * For some reason the WP devs do not want to fix this issue.
	 *   http://core.trac.wordpress.org/ticket/20704
	 */
	public function login($username, $password) 
	{
		do_action('login_init_xmlrpc', $username);
		return parent::login($username, $password);
	}
	
	/**
	 * Uses blog_charset option as encoding/charset for output xml.
	 * 
	 * @param string $xml XML root element to send.
	 * @see IXR_Server::output()
	 * 
	 * For some reason the WP devs do not want to fix this issue.
	 *   http://core.trac.wordpress.org/ticket/4794
	 */
    public function output($xml)
    {
        $format = get_option('blog_charset');
        $xml = '<?xml version="1.0" encoding="' . $format . '"?>'."\n".$xml;
        $length = strlen($xml);
        header('Connection: close');
        header('Content-Length: '.$length);
        header('Content-Type: text/xml; charset=' . $format);
        header('Date: '.date('r'));
        echo $xml;
        exit;
    }
}
