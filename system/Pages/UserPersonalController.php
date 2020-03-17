<?php

namespace TestSystem\Pages;

use TestSystem\AbstractController;
use TestSystem\Auth;

class UserPersonalController extends AbstractController
{
    public function init()
    {
        $this->setPageTitle(_('Личный кабинет'));

        if (!Auth::getInstance()->ifLogged()) {
            header('location: ' . SITE_URL . CURRENT_LANG . '/login', null, 301);
            die();
        }

        $user = \DB::table('users')->find(Auth::getInstance()->getUserId());

        return $this->view->render('personal', ['user' => $user]);
    }
}
