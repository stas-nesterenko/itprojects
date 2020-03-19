<?php

namespace TestSystem\Pages;

use TestSystem\AbstractController;
use TestSystem\Auth;

class UserLoginController extends AbstractController
{
    public function init()
    {
        if (!empty($_POST)) {
            return $this->login();
        }

        $this->setPageTitle(_('Авторизация'));

        if (Auth::getInstance()->ifLogged()) {
            header('location: ' . SITE_URL . CURRENT_LANG . '/personal', null, 301);
            die();
        }

        return $this->view->render('login');
    }

    private function login()
    {
        $response = [];

        $fields = [
            'email' => [
                'min' => 6,
                'max' => 255,
                'regex' => '/^$|^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD'
            ],
            'password' => [
                'min' => 6,
                'max' => 255,
                'regex' => '/^[\/\[\]\`\*\}\{\~\$\%\^\*\|\(\)\№\+\=\#\&\'\"\?\!\@\_\,\.\:\;\-\—\\\ a-zA-Z0-9а-яА-Я]*$/u'
            ],
        ];

        foreach ($fields as $fieldName => $fieldParams) {
            if (!isset($_POST)) {
                $response['field_error'][$fieldName] = _('поле обязательно к заполнению');
            } else if (mb_strlen($_POST[$fieldName]) < $fieldParams['min']) {
                $response['field_error'][$fieldName] = _('слишком короткое значение');
            } else if (mb_strlen($_POST[$fieldName]) > $fieldParams['max']) {
                $response['field_error'][$fieldName] = _('слишком длинное значение');
            } else if (!preg_match($fieldParams['regex'], $_POST[$fieldName])) {
                $response['field_error'][$fieldName] = _('значение введенно с ошибкой');
            }
        }

        if (!isset($response['field_error'])) {
            if ($user = \DB::table('users')->select(['password', 'id'])->where('email', '=', $_POST['email'])->get()) {
                if (!password_verify($_POST['password'], $user[0]->password)) {
                    $response['field_error']['password'] = _('введен неверный пароль');
                }
            } else {
                $response['field_error']['email'] = _('пользователь с таким Email не найден');
            }
        }

        if (!isset($response['field_error']) && isset($user[0])) {
            Auth::getInstance()->logMeIn($user[0]->id);
            $response['location'] = SITE_URL . CURRENT_LANG;
        }

        header('Content-Type: application/json');
        return json_encode($response);
    }
}
