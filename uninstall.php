<?php

if (basename(dirname(__FILE__)) != dirname(WP_UNINSTALL_PLUGIN))
{
    return;
}

require 'LoginDonglePlugin.php';
LoginDonglePlugin::uninstall();
