<?php

class LoginDongle
{
    const OPTION_NAME = 'login_dongle';                //used in the db
    const TRANSLATIONS_DOMAIN = 'login_dongle';
    
    static public function loadTranslations()
    {
        load_plugin_textdomain(self::TRANSLATIONS_DOMAIN, '', basename(dirname(LOGIN_DONGLE_FILENAME)) . '/translations');
    }
    
    static public function __( $text )
    {
        return __( $text, self::TRANSLATIONS_DOMAIN );
    }
    
    static public function _e( $text )
    {
        return _e( $text, self::TRANSLATIONS_DOMAIN );
    }
    
    static public function _x( $text, $context )
    {
        return _x( $text, $context, self::TRANSLATIONS_DOMAIN );
    }
    
    static public function _ex( $text, $context )
    {
        return _ex( $text, $context, self::TRANSLATIONS_DOMAIN );
    }
    
    /**
     * Writes the given $data value to the given $file name.
     * Used for development.
     * 
     * @param mixed $data
     * @param string $file
     */
    static public function logToFile($data, $file = 'log')
    {
        $data = "\n\n" . date('Y-m-d H:i:s') . ' - ' . var_export($data, true);
        if (! $file)
        {
            $file = uniqid('log_');
        }
        $file = basename($file, '.txt');
        $path = dirname(__FILE__) . "/$file.txt";
        file_put_contents($path, $data, FILE_APPEND);
    }
    
    /**
     * Returns the part of the given $url from the host name up to before the query string.
     * 
     * @param string $url
     * @return string
     */
    static public function scriptUrl( $url )
    {
        $result = preg_replace('@^https?://@', '', $url);
    	$result = preg_replace('@\?.*@', '', $result);
    	return $result;
    }
}
