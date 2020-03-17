<?php

namespace TestSystem\Pages;

use TestSystem\AbstractController;
use TestSystem\Auth;

class UserRegistrationController extends AbstractController
{
    public function init()
    {
        $this->setPageTitle(_('Регистрация'));

        if (Auth::getInstance()->ifLogged()) {
            header('location: /', null, 301);
            die();
        }

        return $this->render('registration', []);
    }
}
