<?php

class LoginDongleOption
{
    /**
     * Returns the site/user option name.
     * 
     * @param string $username
     * @return string
     */
    protected function name($username = NULL)
    {
        $result = LoginDongle::OPTION_NAME . ($username ? ' ' . $username : '');
        return $result;
    }
    
    /**
     * Removes the site/user option.
     * 
     * @param string $username
     */
    public function remove($username = NULL)
    {
        if (is_numeric($username))
        {
            $user = new WP_User($username);
            $username = $user->user_login;
        }
        $name = $this->name($username);
        delete_option($name);
    }
    
    /**
     * Removes site option and each user's.
     * 
     * FIXME remove all users' options using a query with "LIKE 'login_dongle %'" otherwise some garbage is not removed
     *       garbage only affects installations that occurred before 1.4.1 (when 'delete_user' hook was first acted upon)
     *       in such a case, if users were deleted without first deleting their challenge (by far the most common scenario)
     *       then their option would not get removed by this method because the related username did not get into the loop
     * 
     * @param string $username
     */
    public function remove_all()
    {
        $users = get_users(array('fields' => array('ID','user_login')));
        foreach ($users as $user) 
        {
            $this->remove($user->user_login);
        }
        $this->remove();
    }
    
    /**
     * Retrieves the site/user option value. 
     * 
     * @param string $username
     * @return mixed
     */
    public function get($username = NULL)
    {
        $name = $this->name($username);
        $result = get_option($name, array());
        return $result;
    }
    
    /**
     * Replaces the site/user option with the given $value.
     * The site option is set with the autoload flag to yes.
     * The user options are set with the autoload flag to no.
     * 
     * @param mixed $value
     * @param string $username
     */
    public function set($value, $username = NULL)
    {
        $name = $this->name($username);
        delete_option($name);
        $autoload = $username ? 'no' : 'yes';
        add_option($name, $value, NULL, $autoload);
    }
    
    /**
     * Retrieves the values of the given option $keys
     * 
     * @param array $keys
     * @param string $username
     */
    public function keysGet($keys, $username = NULL)
    {
        $result = array_fill_keys($keys, NULL);
        $data = $this->get($username);
        $result = array_merge($result, $data);
        return $result;
    }
    
    /**
     * Sets the given $values to their option keys
     * 
     * @param struct $values
     * @param string $username
     */
    public function keysSet($values, $username = NULL)
    {
        $data = $this->get($username);
        $data = array_merge($data, $values);
        $this->set($data, $username);
    }
}
