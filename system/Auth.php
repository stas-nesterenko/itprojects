<?php


namespace TestSystem;


class Auth
{
    /**
     * Хранит экземпляр Auth
     * @var
     */
    private static $_instance;

    /**
     * Auth constructor.
     */
    private function __construct() {}

    /**
     *
     */
    private function __clone() {}


    /**
     * Возвращает текущий экземпляр Auth
     * @return Auth
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    /**
     * Возвращает текущий статус авторизации
     * @return bool
     */
    public function ifLogged()
    {
        return isset($_SESSION['auth']['user_id']);
    }

    /**
     * Инициализирует пользователя в системе
     * @param int $user_id ид пользователя
     */
    public function logMeIn($user_id)
    {
        unset($_SESSION['auth']);
        $_SESSION['auth']['user_id'] = $user_id;
    }

    /**
     * Очищает пользовательские данные из сессии
     */
    public function logMeOut()
    {
        unset($_SESSION['auth']);
    }
}
