<?php

namespace TestSystem\Pages;

use TestSystem\AbstractController;
use TestSystem\Auth;

class UserLogoutController extends AbstractController
{
    public function init()
    {
        Auth::getInstance()->logMeOut();
        header('location: ' . SITE_URL . CURRENT_LANG . '/login', null, 301);
        die();
    }
}
