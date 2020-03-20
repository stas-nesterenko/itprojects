<?php

namespace TestSystem\Pages;

use TestSystem\AbstractFormController;
use TestSystem\Auth;
use TestSystem\TextField;

class UserLoginController extends AbstractFormController
{
    public function init()
    {
        if (!empty($_POST)) {
            return $this->submit();
        }

        $this->setPageTitle(_('Авторизация'));

        if (Auth::getInstance()->ifLogged()) {
            header('location: ' . SITE_URL . CURRENT_LANG, null, 301);
            die();
        }

        return $this->render('login',  ['validationRules' => $this->form->getValidationRules()]);
    }

    protected function createAvailableFields()
    {
        $name = new TextField('email');
        $name->setMinLength(2)
            ->setMaxLength(40)
            ->setRegex('/^$|^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD');

        $this->form->addField($name);

        $name = new TextField('password');
        $name->setMinLength(2)
            ->setMaxLength(20)
            ->setRegex('/^[\/\[\]\`\*\}\{\~\$\%\^\*\|\(\)\№\+\=\#\&\'\"\?\!\@\_\,\.\:\;\-\—\\\ a-zA-Z0-9а-яА-Я]*$/u');

        $this->form->addField($name);
    }

    protected function submit()
    {
        if ($this->form->valid()) {
            if ($user = \DB::table('users')->select(['password', 'id'])->where('email', '=', $_POST['email'])->get()) {
                if (password_verify($_POST['password'], $user[0]->password)) {
                    Auth::getInstance()->logMeIn($user[0]->id);
                    $this->form->setLocation(SITE_URL . CURRENT_LANG);
                } else {
                    $this->form->setError('password', _('введен неверный пароль'));

                }
            } else {
                $this->form->setError('email', _('пользователь с таким Email не найден'));
            }
        }

        header('Content-Type: application/json');
        return $this->form->getResponse();
    }
}
