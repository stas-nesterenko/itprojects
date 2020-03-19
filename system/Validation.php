<?php

namespace TestSystem;

class Validation
{
    private $fields;
    private $files;
    private $request;

    public function __construct($fields, $files = [], $request_type = 'post')
    {
        $this->fields = $fields;
        $this->files = $files;
        $this->request = $request_type == 'post' ? $_POST : $_GET;
    }

    /**
     * проводит валидацию запроса
     * @return bool
     */
    public function valid()
    {
        foreach ($this->fields as $fieldName => $fieldParams) {
            if (
                !isset($this->request) ||
                mb_strlen($this->request[$fieldName]) < $fieldParams['min'] ||
                !preg_match($fieldParams['regex'], $this->request[$fieldName])
            ) {
                return false;
            }
        }

        if (!empty($this->files)) {
            foreach ($this->files as $fileName => $fileParams) {
                if (isset($_FILES[$fileName]) && $_FILES[$fileName]['tmp_name']) {
                    if (
                        !in_array($_FILES[$fileName]['type'], $fileParams['types']) ||
                        $_FILES[$fileName]['size'] > $fileParams['max']
                    ) {
                       return false;
                    }
                }
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
        $response = [];

        foreach ($this->fields as $fieldName => $fieldParams) {
            if (!isset($this->request)) {
                $response['field_error'][$fieldName] = _('поле обязательно к заполнению');
            } else if (mb_strlen($this->request[$fieldName]) < $fieldParams['min']) {
                $response['field_error'][$fieldName] = _('слишком короткое значение');
            } else if (mb_strlen($this->request[$fieldName]) > $fieldParams['max']) {
                $response['field_error'][$fieldName] = _('слишком длинное значение');
            } else if (!preg_match($fieldParams['regex'], $this->request[$fieldName])) {
                $response['field_error'][$fieldName] = _('значение введено с ошибкой');
            }
        }

        if (!empty($this->files)) {
            foreach ($this->files as $fileName => $fileParams) {
                if (isset($_FILES[$fileName]) && $_FILES[$fileName]['tmp_name']) {
                    if (!in_array($_FILES[$fileName]['type'], $fileParams['types'])) {
                        $response['field_error'][$fileName] = _('некорректный формат файла');
                    } else if ($_FILES[$fileName]['size'] > $fileParams['max']) {
                        $response['field_error'][$fileName] = _('максимальный размер файла - 5 MB');
                    }
                }
            }
        }

        return $response;
    }
}
