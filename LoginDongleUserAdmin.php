<?php

class LoginDongleUserAdmin
{
    const MAXLEN_CHALLENGE = 20;
    const MAXLEN_ANSWER    = 20;

    const NAME_PREFIX_POST = 'logindongle_';
    const NAME_GET = 'logindongle';

    /**
     * @var LoginDongleOption
     */
    protected $option;

    public function __construct()
    {
        require_once 'LoginDongleOption.php';
        $this->option = new LoginDongleOption();

        add_action('profile_personal_options', array($this, 'handleGET')); //owner view
        //add_action('personal_options_update', array($this, 'handlePOST')); //"

        add_action('edit_user_profile', array($this, 'handleGET'));        //admin view
        add_action('profile_update', array($this, 'handlePOST'));          //"
    }

	/**
     * Returns the default settings for the user option.
     *
     * @return struct
     */
    protected function defaultSettings()
    {
        $hash = strtoupper(md5(time()));
        $result = array(
            'challenge' => substr($hash,  0, 5),
            'response'  => substr($hash, -3, 3),
        );
        return $result;
    }

    /**
     * Returns an encoded version of the given $decoded URI component.
     * The encoding is very similar to the result of the javascript encodeURIComponent() function.
     *
     * @param string $decoded
     * @return string
     */
    static protected function encodeURIComponent($decoded)
    {
        //exceptions
        //-_.~      <-- http://www.php.net/manual/en/function.rawurlencode.php
        //-_.~!*'() <-- https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/encodeURIComponent

        //exceptions to exceptions
        //for some reason, not only each + gets converted to an underscore but also each dot

        $result = rawurlencode($decoded);
        $result = str_replace(
            array('%21', '%2A', '%27', '%28', '%29',   '.'),
            array(  '!',   '*',   "'",   '(',   ')', '%2E'),
            $result);
        return $result;
    }

    /**
     * Returns a pair (challenge, response) for when POST is used to login
     *
     * @param struct $userdata
     */
    static public function fieldFormPOST($userdata)
    {
        //wp_magic_quotes() is called by WP from settings.php for having uniformly escaped values
    	//over servers with magic quotes both on and off, BUT it 'forgets' to do the same for keys,
    	//so before comparing Login Dongle db options against $_POST contents, I need to add
    	//slashes to the response always, but to the challenge only if magic quotes are really on
    	$challenge = self::challengeFieldName($userdata['challenge']);
    	if (get_magic_quotes_gpc())
    	{
    	    $challenge = addslashes($challenge);
    	}
    	$response  = addslashes($userdata['response']);
    	$result = array($challenge, $response);
    	return $result;
    }

    /**
     * Returns a pair (challenge, response) for when XMLRPC is used to login.
     *
     * It will be an inspectionable param in the XMLRPC URL, so it's better to use
     *   .../xmlrpc.php?logindongle=[hash(challenge,response)]
     * than
     *   .../xmlrpc.php?[challenge]=[response]
     * which is easier to remember and write but is not needed because this will be written once,
     * when the URL of the XMLRPC server is configured in the XMLRPC client.
     *
     * @param struct $userdata
     */
    static public function fieldXmlrpcPOST($userdata)
    {
        $challenge = self::NAME_GET;
        $response = array_intersect_key($userdata, array_flip(array('challenge', 'response')));
        ksort($response); //needed to guarantee a trustful serialize
        $response = serialize($response);
        $response = sha1($response);
        $result = array($challenge, $response);
        return $result;
    }

    /**
     * For some reason, a space is correctly urlencoded and sent as a plus by the browser but I get
     * it in the $_POST as an underscore. (ModSecurity?) To solve the problem and prevent PHP from
     * misinterpreting other special field chars like [], I'm using encodeURIComponent.
     *
     * @param string $challenge
     * @return string
     */
    static protected function challengeFieldName($challenge)
    {
        $result = self::encodeURIComponent(self::NAME_PREFIX_POST . $challenge);
        return $result;
    }

    /**
     * Returns the code for the bookmarklet, based on the given challenge.
     *
     * @param struct $data
     * @return string
     */
    protected function bookmarklet( $data )
    {
    	$challenge = $data['challenge'];
    	if ('' == $challenge)
    	{
    	    return '';
    	}
    	$fieldName = self::challengeFieldName($challenge);
    	$loginUrl  = LoginDongle::scriptUrl(wp_login_url());
    	$loginPath = preg_replace('@^' . preg_quote($_SERVER['HTTP_HOST'], '@') . '/@', '', $loginUrl);
    	require_once 'JShrink.php';
	    $result = JShrink::minify(file_get_contents(dirname(LOGIN_DONGLE_FILENAME) . '/bookmarklet.js'));
	    //dbgx_trace_var( $result, $var_name = '$result' );

        $result = preg_replace('@\bLOGIN_URL\b@',  addslashes($loginUrl),  $result);
    	$result = preg_replace('@\bLOGIN_PATH\b@', addslashes($loginPath), $result);
    	$result = preg_replace('@\bCHALLENGE\b@',  addslashes($challenge), $result);
        $result = preg_replace('@\bFIELD_NAME\b@', addslashes($fieldName), $result);

    	return $result;
    }

    /**
     * Returns the code for the XMLRPC URL, based on the given challenge.
     *
     * @param struct $data
     * @return string
     */
    protected function xmlrpcUrl( $data )
    {
    	if ('' == $data['challenge'])
    	{
    	    return '';
    	}
    	list($challenge, $response) = self::fieldXmlrpcPOST($data);
    	$challenge = self::encodeURIComponent($challenge);
    	$response = self::encodeURIComponent($response);
    	//$result = get_bloginfo('pingback_url');
    	$result = site_url('xmlrpc.php', 'rpc');
    	$result = "$result?$challenge=$response";
    	return $result;
    }

    protected function echo_toggle($class, $show, $hide)
    {
?>
<script type="text/javascript">
jQuery(function($){
	$('a.<?php echo $class; ?>').click(function(e) {
		e.preventDefault();
		$('.<?php echo $class; ?>').toggle();
	});
});
</script>
<a class="<?php echo $class; ?>" href="#"><?php echo $show; ?></a>
<a class="<?php echo $class; ?>" href="#" style="display: none;"><?php echo $hide; ?></a>
<?php
    }

    /**
     * Echoes the html for the user settings page.
     *
     * This method is a handler bound to the profile_personal_options and edit_user_profile action.
     *
     * @param WP_User $user
     */
    public function handleGET($user)
    {
        $username = $user->user_login;
        $data = $this->option->get($username);
        if (! (is_array($data) && isset($data['challenge']) && isset($data['response'])))
        {
            //$data = $this->defaultSettings();
            $data = array(
                'challenge' => '',
                'response'  => '',
            );
        }
	    $code = '';
        if (! empty($data['challenge']))
        {
            $code = $this->bookmarklet($data);
            $dongle_pc = 'javascript:' . htmlspecialchars(rawurlencode($code), ENT_COMPAT); //safest
            $dongle_sp = 'javascript:' . htmlspecialchars($code, ENT_COMPAT);

            $code = $this->xmlrpcUrl($data);
            $xmlrpc_url = htmlspecialchars($code, ENT_COMPAT);
        }

        $__send = LoginDongle::__('Send on next update');
        $__show_more = LoginDongle::__('Show more');
        $__hide_more = LoginDongle::__('Show less');

?>
<div style="margin: 2em 0;">
    <h3>Login Dongle</h3>
<p><?php LoginDongle::_e(

'Login Dongle makes the login for your personal use only, by means of a conventional challenge &gt;&gt; response mechanism.'

); ?> <?php $this->echo_toggle('logindongle_help', $__show_more, $__hide_more); ?></p>
<p class="logindongle_help" style="display: none;"><?php LoginDongle::_e(

'The challenge and its expected response resemble passwords, but they are fundamentally different.
This data is supposed to have enough complexity to deter the casual user of your PC, if any. 
So feel free to use simple pairs, but such that only you can easily associate the response to the challenge, 
while others will have a harder time. E.G. color &gt;&gt; ful. 
(For your security, please do not use this example literally!).'

); ?></p>
<p class="logindongle_help" style="display: none;"><?php LoginDongle::_e(

'You yourself won\'t be able to log into your blog without your most current login dongle,
unless you disable this plugin right from the file system, 
as described in the <a href="http://wordpress.org/extend/plugins/login-dongle/faq/" target="_blank">FAQ</a>.'

); ?></p>
<p class="logindongle_help" style="display: none;"><?php printf(LoginDongle::_x(

'To be able to log into your blog using a different browser or a different system in the future,
check the <em>%s</em> option. After saving, you\'ll receive the bookmarklet code by email.'

, '%s is "Send on next update"'), $__send); ?></p>
<p class="logindongle_help" style="display: none;"><strong><?php LoginDongle::_e(

'Whenever you change the challenge or the response, after saving remember to refresh the generated
codes wherever you use them.'

); ?></strong></p>
	<table class="form-table">
    	<tbody>
    		<tr valign="top">
    			<th scope="row"><?php LoginDongle::_e('Challenge'); ?></th>
    			<td>
    				<input type="text" id="challenge"
						name="<?php echo LoginDongle::OPTION_NAME; ?>[challenge]"
						value="<?php echo htmlspecialchars($data['challenge']); ?>"
						size="<?php echo (self::MAXLEN_CHALLENGE + 2); ?>"
						maxlength="<?php echo self::MAXLEN_CHALLENGE; ?>"
					/>
					<span class="description"><?php printf(LoginDongle::__('1 to %s chars.'), self::MAXLEN_CHALLENGE); ?>
					<?php LoginDongle::_e('Leave this empty to be able to log in without a login dongle.'); ?></span>
				</td>
			</tr>
    		<tr valign="top">
    			<th scope="row"><?php LoginDongle::_e('Response'); ?></th>
    			<td>
    				<input type="text" id="response"
						name="<?php echo LoginDongle::OPTION_NAME; ?>[response]"
						value="<?php echo htmlspecialchars($data['response']); ?>"
						size="<?php echo (self::MAXLEN_ANSWER + 2); ?>"
						maxlength="<?php echo self::MAXLEN_ANSWER; ?>"
					/>
					<span class="description"><?php printf(LoginDongle::__('1 to %s chars.'), self::MAXLEN_ANSWER); ?>
						<?php LoginDongle::_e("You'll have to remember where you put capital/non-capital letters."); ?></span>
				</td>
			</tr>
<?php
        if ($code) //I don't show the bookmaklets if no challenge is set
        {
?>
    		<tr valign="top" style="border-top: 1px solid silver;">
    			<th scope="row"><?php LoginDongle::_e('Generated codes'); ?></th>
    			<td><input type="checkbox" name="login_dongle_send" /> <?php echo $__send; ?></td>
    		</tr>
			<tr valign="top">
    			<th scope="row" style="padding-left: 20px;">
        			<?php LoginDongle::_e('Raw bookmarklet'); ?><br />
    				<span class="description"><?php LoginDongle::_e('better for SmartPhones'); ?></span>
    			</th>
    			<td>
    				<span class="description"><?php printf(LoginDongle::__(

'Drag and drop this <strong><a href="%s" title="Log In">link</a></strong> into your bookmarks bar
or copy the following code and paste it into the URL field of a new bookmark.'

    			), $dongle_sp); ?></span>
    				<?php $this->echo_toggle('logindongle_sp', $__show_more, $__hide_more); ?><br />
    				<textarea class="logindongle_sp" disabled="disabled" style="display: none;"><?php echo $dongle_sp; ?></textarea>
				</td>
			</tr>
			<tr valign="top">
    			<th scope="row" style="padding-left: 20px;">
    			    <?php LoginDongle::_e('Encoded bookmarklet'); ?><br />
    			    <span class="description"><?php LoginDongle::_e('better for PCs'); ?></span>
			    </th>
    			<td>
    				<span class="description"><?php printf(LoginDongle::__(

'Drag and drop this <strong><a href="%s" title="Log In">link</a></strong> into your bookmarks bar
or copy the following code and paste it into the URL field of a new bookmark.'

    			), $dongle_pc); ?></span>
    				<?php $this->echo_toggle('logindongle_pc', $__show_more, $__hide_more); ?><br />
    				<textarea class="logindongle_pc" disabled="disabled" style="display: none;"><?php echo $dongle_pc; ?></textarea>
				</td>
			</tr>
    		<tr valign="top">
    			<th scope="row" style="padding-left: 20px;"><?php LoginDongle::_e('XML-RPC endpoint'); ?></th>
    			<td>
    			    <span class="description"><?php LoginDongle::_e(

'Configure the following URL into all the third-party apps accessing your blog by means of XML-RPC
(E.G. <a href="http://wordpress.org/extend/mobile/" target="_blank">mobile apps</a>, 
<a href="http://en.support.wordpress.com/xml-rpc/" target="_blank">offline editing tools</a>, and 
so on).'

        			); ?></span>
    				<?php $this->echo_toggle('logindongle_api', $__show_more, $__hide_more); ?><br />
        			<textarea class="logindongle_api" disabled="disabled" style="display: none;"><?php echo $xmlrpc_url; ?></textarea></td>
			</tr>
<?php
        }
?>
		</tbody>
	</table>
</div>
<?php
    }

    /**
     * Saves POST data coming from the user settings page.
     *
     * This method is a handler bound to the profile_update action.
     *
     * @param integer $user_id
     */
    public function handlePOST($user_id)
    {
        $data = stripslashes_deep($_POST[LoginDongle::OPTION_NAME]);
        $data['challenge'] = substr(trim($data['challenge']), 0, self::MAXLEN_CHALLENGE);
        $data['response']  = substr(trim($data['response']),  0, self::MAXLEN_ANSWER);

        $user = new WP_User($user_id);
        $username = $user->user_login;
        $email = $user->user_email;

        $data['field']['form']   = self::fieldFormPOST($data);
        $data['field']['xmlrpc'] = self::fieldXmlrpcPOST($data);
        $this->option->keysSet($data, $username);
        if (! ($data['challenge'] && $_POST['login_dongle_send']))
        {
            return;
        }
        $bookmarklet = $this->bookmarklet($data);
        $xmlrpc_url = $this->xmlrpcUrl($data);
        require_once 'LoginDongleMail.php';
        LoginDongleMail::send($email, ': ' . sprintf(LoginDongle::_x("encoded version of %s's bookmarklet source (better for PCs)", '%s is a username'), $username), 'javascript:' . rawurlencode($bookmarklet));
        LoginDongleMail::send($email, ': ' . sprintf(LoginDongle::_x("raw version of %s's bookmarklet source (better for SmartPhones)", '%s is a username'), $username), 'javascript:' . $bookmarklet);
        LoginDongleMail::send($email, ': ' . sprintf(LoginDongle::_x("XML-RPC URL for %s", '%s is a username'), $username), $xmlrpc_url);
    }
}
