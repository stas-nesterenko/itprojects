<?php


namespace TestSystem;


abstract class AbstractField
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * валидирует поле
     * @return boolean
     */
    abstract function valid();

    /**
     * возвращает сообщение ошибки
     * @return string
     */
    abstract function getError();

    /**
     * возвращает правила валидации
     * @return array
     */
    abstract function getValidationRule();

    /**
     * возвращает имя поля
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * возвращает зачение поля
     * @return string
     */
    public function getValue()
    {
        return $_POST[$this->name];
    }
}
