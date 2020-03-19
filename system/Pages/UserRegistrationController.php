<?php

namespace TestSystem\Pages;

use TestSystem\AbstractController;
use TestSystem\Auth;

class UserRegistrationController extends AbstractController
{
    public function init()
    {
        if (!empty($_POST)) {
            return $this->saveUser();
        }

        $this->setPageTitle(_('Регистрация'));

        if (Auth::getInstance()->ifLogged()) {
            header('location: /', null, 301);
            die();
        }

        return $this->render('registration', []);
    }

    private function saveUser() {
        $response = [];

        $fields = [
            'name' => [
                'min' => 2,
                'max' => 20,
                'regex' => '/^[a-zA-Zа-яА-Я]*$/u'
            ],
            'secondName' => [
                'min' => 2,
                'max' => 40,
                'regex' => '/^[a-zA-Zа-яА-Я]*$/u'
            ],
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
            'passwordConfirm' => [
                'min' => 6,
                'max' => 255,
                'regex' => '/^[\/\[\]\`\*\}\{\~\$\%\^\*\|\(\)\№\+\=\#\&\'\"\?\!\@\_\,\.\:\;\-\—\\\ a-zA-Z0-9а-яА-Я]*$/u'
            ]
        ];

        $files = [
            'image' => [
                'types' => [
                    'image/gif',
                    'image/png',
                    'image/jpeg'
                ],
                'max' => 5242880
            ]
        ];

        $user = [];

        foreach ($fields as $fieldName => $fieldParams) {
            if (!isset($_POST)) {
                $response['field_error'][$fieldName] = _('поле обязательно к заполнению');
            } else if (mb_strlen($_POST[$fieldName]) < $fieldParams['min']) {
                $response['field_error'][$fieldName] = _('слишком короткое значение');
            } else if (mb_strlen($_POST[$fieldName]) > $fieldParams['max']) {
                $response['field_error'][$fieldName] = _('слишком длинное значение');
            } else if (!preg_match($fieldParams['regex'], $_POST[$fieldName])) {
                $response['field_error'][$fieldName] = _('значение введенно с ошибкой');
            } else {
                $user[$fieldName] = $_POST[$fieldName];
            }
        }

        if (
            isset($user['password']) &&
            isset($user['passwordConfirm']) &
            $user['password'] != $user['passwordConfirm']
        ) {
            $response['field_error']['passwordConfirm'] = _('не совпадает с введенным паролем');
        }

        if (
            isset($user['email']) &&
            \DB::table('users')->select('*')->findAll('email', $user['email'])
        ) {
            $response['field_error']['email'] = _('пользователь с таким email уже зарегестрирован');
        }

        foreach ($files as $fileName => $fileParams) {
            if (isset($_FILES[$fileName]) && $_FILES[$fileName]['tmp_name']) {
                if (!in_array($_FILES[$fileName]['type'], $fileParams['types'])) {
                    $response['field_error'][$fileName] = _('некорректный формат файла');
                } else if ($_FILES[$fileName]['size'] > $fileParams['max']) {
                    $response['field_error'][$fileName] = _('максимальный размер файла - 5 MB');
                }
            }
        }

        if (!isset($response['field_error'])) {
            unset($user['passwordConfirm']);
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);

            $user_id = \DB::table('users')->insert($user);

            if (isset($_FILES['image']['tmp_name'])) {
                $userStorageDir = 'public/storage/users/' . $user_id;

                mkdir($userStorageDir, 0777, true);
                if (@copy($_FILES['image']['tmp_name'], $userStorageDir . '/' . $_FILES['image']['name'])) {
                    \DB::table('users')->where('id', $user_id)->update(['image' => $_FILES['image']['name']]);
                }
            }

            Auth::getInstance()->logMeIn($user_id);

            $response['location'] = SITE_URL . CURRENT_LANG;
        }

        header('Content-Type: application/json');
        return json_encode($response);
    }
}
