<?php
/*
Plugin Name: Login Dongle
Text Domain: login_dongle
Domain Path: /translations
Plugin URI: http://wordpress.org/extend/plugins/login-dongle/
Description: The bookmark to login nobody but you. Simple and secure.
Version: 1.5.2
Author: Andrea Ercolino
Author URI: http://andowebsit.es/blog/noteslog.com/
License: GPLv2 or later
*/

define('LOGIN_DONGLE_FILENAME', __FILE__);
require 'LoginDonglePlugin.php';

//just comment the following line if you need to temporarily disable this plugin
$loginDonglePlugin = new LoginDonglePlugin();


