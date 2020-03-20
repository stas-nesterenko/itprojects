<?php

namespace TestSystem\Pages;

use TestSystem\AbstractFormController;
use TestSystem\Auth;
use TestSystem\FileField;
use TestSystem\TextField;

class UserRegistrationController extends AbstractFormController
{
    public function init()
    {
        if (!empty($_POST)) {
            return $this->submit();
        }

        $this->setPageTitle(_('Регистрация'));

        if (Auth::getInstance()->ifLogged()) {
            header('location: ' . SITE_URL . CURRENT_LANG, null, 301);
            die();
        }

        return $this->render('registration', ['validationRules' => $this->form->getValidationRules()]);
    }

    protected function  createAvailableFields()
    {
        $name = new TextField('name');
        $name->setMinLength(2)
            ->setMaxLength(20)
            ->setRegex('/^[a-zA-Zа-яА-Я]*$/u');

        $this->form->addField($name);

        $name = new TextField('secondName');
        $name->setMinLength(2)
            ->setMaxLength(20)
            ->setRegex('/^[a-zA-Zа-яА-Я]*$/u');

        $this->form->addField($name);

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

        $name = new TextField('passwordConfirm');
        $name->setMinLength(2)
            ->setMaxLength(20)
            ->setRegex('/^[\/\[\]\`\*\}\{\~\$\%\^\*\|\(\)\№\+\=\#\&\'\"\?\!\@\_\,\.\:\;\-\—\\\ a-zA-Z0-9а-яА-Я]*$/u');

        $this->form->addField($name);

        $name = new FileField('image');
        $name->setMaxSize(5242880)
            ->setType('image/gif')
            ->setType('image/png')
            ->setType('image/jpeg');

        $this->form->addField($name);
    }


    protected function submit() {
        if ($this->form->valid()) {
            if ($_POST['password'] != $_POST['passwordConfirm']) {
                $this->form->setError('passwordConfirm', _('не совпадает с введенным паролем'));
                goto out;
            }

            if (\DB::table('users')->select('*')->findAll('email', $_POST['email'])) {
                $this->form->setError('email', _('пользователь с таким email уже зарегистрирован'));
                goto out;
            }

            $user = $this->form->getValues();
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
            $this->form->setLocation(SITE_URL . CURRENT_LANG);
        }

        out:

        header('Content-Type: application/json');
        return $this->form->getResponse();
    }
}
