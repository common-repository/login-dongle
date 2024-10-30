<?php

class LoginDongleMail
{
    /**
     * Returns the html ContentType.
     * Used by sendmail as a WordPress filter.
     * 
     * @return string
     */
    static public function htmlContentType()
    {
        return 'text/html';
    }
    
    /**
     * Sends a mail.
     * 
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string $type     Anything different from text/html is like text/plain
     */
    static public function send($to, $subject, $body, $type = 'text/plain')
    {
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $subject = sprintf('[%s] Login Dongle%s', $blogname, $subject);
        if (self::htmlContentType() == $type) 
        {
            $class = __CLASS__;
            add_filter('wp_mail_content_type', array($class, 'htmlContentType'));
            wp_mail($to, $subject, $body);
            remove_filter('wp_mail_content_type', array($class, 'htmlContentType'));
        }
        else 
        {
            wp_mail($to, $subject, $body);
        }
    }
}
