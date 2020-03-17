<?php

namespace TestSystem\Pages;

use TestSystem\AbstractController;
use TestSystem\Auth;

class UserLoginController extends AbstractController
{
    public function init()
    {
        if (Auth::getInstance()->ifLogged()) {
            header('location: ' . SITE_URL . CURRENT_LANG . '/personal', null, 301);
            die();
        }

        return $this->view->render('registration');
    }
}
