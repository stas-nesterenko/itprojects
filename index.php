<?php

require_once('./vendor/autoload.php');
require_once('./conf.php');

if(get_magic_quotes_gpc())
{
    function stripslashes_array($array)
    {
        return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
    }

    $_COOKIE = stripslashes_array($_COOKIE);
    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_REQUEST = stripslashes_array($_REQUEST);
}

echo TestSystem\Core::getInstance()->run();
