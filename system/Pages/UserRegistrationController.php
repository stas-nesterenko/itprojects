<?php

namespace TestSystem\Pages;

use TestSystem\AbstractController;
use TestSystem\Auth;
use TestSystem\Validation;

class UserRegistrationController extends AbstractController
{
    public function init()
    {
        if (!empty($_POST)) {
            return $this->saveUser();
        }

        $this->setPageTitle(_('Регистрация'));

        if (Auth::getInstance()->ifLogged()) {
            header('location: ' . SITE_URL . CURRENT_LANG, null, 301);
            die();
        }

        return $this->render('registration', []);
    }

    private function saveUser() {
        header('Content-Type: application/json');

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

        $Validation = new Validation($fields, $files);

        if (!$Validation->valid()) {
            return json_encode($Validation->getErrors());
        }

        if ($_POST['password'] != $_POST['passwordConfirm']) {
            return json_encode([
                'field_error' => [
                    'passwordConfirm' => _('не совпадает с введенным паролем')
                ]
            ]);
        }

        if (\DB::table('users')->select('*')->findAll('email', $_POST['email'])) {
            return json_encode([
                'field_error' => [
                    'email' => _('пользователь с таким email уже зарегистрирован')
                ]
            ]);
        }

        $user = array_intersect_key($_POST, $fields);
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

        return json_encode(['location' => SITE_URL . CURRENT_LANG]);
    }
}
