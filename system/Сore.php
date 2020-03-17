<?php

namespace TestSystem;

use Exception;
use Pixie\Connection;
use TestSystem\Pages\NotFoundController;

/**
 * Управляющий синглтон
 *
 * Отвечает за первичную инициализацию системы.
 */
class Core
{
    /**
     * Содержит экземпляр объекта
     * @var
     */
    private static $_instance;

    /**
     * Core constructor.
     */
    private function __construct()
    {
    }

    /**
     *
     */
    private function __clone()
    {
    }

    /**
     * Возвращает экземпляр объекта
     * @return Core
     */
    public static function getInstance()
    {
        if (self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    /**
     * Устанавливает временную зону, кодировку, запускает сессию.
     * @return bool
     */
    private function init()
    {
        date_default_timezone_set('Europe/Kiev');
        mb_internal_encoding('UTF-8');

        new Connection('mysql', [
            'driver'    => 'mysql',
            'host'      => SQL_HOST,
            'database'  => SQL_BASE,
            'username'  => SQL_LOGIN,
            'password'  => SQL_PWD,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'options'   => [
                \PDO::ATTR_TIMEOUT => 5,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ], 'DB');

        session_start();

        return true;
    }


    /**
     * Инициализирует установку языковых настроек страницы
     * @return string $_REQUEST_URI с вычетом языковой метки
     * @throws Exception 404
     */
    private function getRequestUrl()
    {
        $request_uri = array_values(array_filter(preg_split('/\//', $_SERVER['REQUEST_URI'])));

        $out_url = strtok($_SERVER['REQUEST_URI'], '?');

        if (isset(SITE_LANG[$request_uri[0]])) {
            $this->setLocalParams($request_uri[0]);
            $out_url = str_replace('/' . $request_uri[0] . '/', '/', $out_url);
        } elseif (empty($request_uri)) {
            $this->setLocalParams(DEFAULT_LANG);
        } else {
            $this->setLocalParams(DEFAULT_LANG);
            throw new Exception(_('Страница не найдена'), 404);
        }

        return $out_url;
    }

    /**
     * Устанавливает настройки локали
     * @param string $language языковая метка
     */
    private function setLocalParams($language)
    {
        define('CURRENT_LANG', $language);

        setlocale(LC_ALL, SITE_LANG[CURRENT_LANG] . '.UTF-8');
        setlocale(LC_NUMERIC, 'en_EN.UTF-8');
        putenv('LANG=' . SITE_LANG[CURRENT_LANG]);
        putenv('LANGUAGE=' . SITE_LANG[CURRENT_LANG]);
        bindtextdomain('messages', "./Locale");
    }

    /**
     * Выполняет метод класа согласно совпадению шаблона роута с REQUEST_URI.
     * @return string html страницы
     * @throws Exception 404
     */
    public function run()
    {
        if (!$this->init())
            return 'Error in init();';

        try {
            $request_uri = $this->getRequestUrl();

            $routes = include './system/routes.php';

            if ($controllerName = $routes[$request_uri]) {
                $controllerName = 'TestSystem\\Pages\\' . $controllerName;
                $controller = new $controllerName();
            } else {
                throw new Exception(_('Страница не найдена'), 404);
            }
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case 404:
                    $controller = new NotFoundController();
                    break;
                default:
                    die($e->getMessage());
                    break;
            }
        }

        return $controller->init();
    }
}
