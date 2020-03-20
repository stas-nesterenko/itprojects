<?php

namespace TestSystem;

class Form
{

    private $fields;
    private $files;
    private $response;

    public function addField(AbstractField $field)
    {
        $this->fields[] = $field;

    }

    /**
     * проводит валидацию запроса
     * @return bool
     */
    public function valid()
    {
        foreach ($this->fields as $field) {
            if (!$field->valid()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Возвращает массив с ошибками
     * @return array
     */
    public function getErrors()
    {
        foreach ($this->fields as $field) {
            if (!$field->valid()) {
                $this->response['field_error'][$field->getName()] = $field->getError();
            }
        }

        return $this->response;
    }

    /**
     * устанавливает ошибку
     * @param $fieldName
     * @param $error
     */
    public function setError($fieldName, $error)
    {
        $this->response['field_error'][$fieldName] = $error;
    }

    /**
     * устанавливает путь перенаправления
     * @param $path
     */
    public function setLocation($path)
    {
        $this->response['location'] = $path;
    }

    /**
     * возвращает ответ формы
     * @return mixed
     */
    public function getResponse()
    {
        if (!$this->valid()) {
            $this->getErrors();
        }

        return json_encode($this->response);
    }

    /**
     * возвращает значения валидных полей
     * @return array
     */
    public function getValues()
    {
        $values = [];
        foreach ($this->fields as $field) {
            if ($field->valid()) {
                $values[$field->getName()] = $field->getValue();
            }
        }

        return $values;
    }

    /**
     * возвращает правила валидации полей для js
     * @return []
     */
    public function getValidationRules()
    {
        $rules = [];

        foreach ($this->fields as $field) {
            $rules[$field->getName()] = $field->getValidationRule();
        }

        return $rules;
    }
}
