<?php


namespace TestSystem;


class FileField extends AbstractField
{
    private $maxSize;
    private $types;

    /**
     * устанавливает максимальный размер файла
     * @param $value
     * @return $this
     */
    public function setMaxSize($value)
    {
        $this->maxSize = $value;
        return $this;
    }

    /**
     * устанавливает тип файла
     * @param $value
     * @return $this
     */
    public function setType($value)
    {
        $this->types[] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    function valid()
    {
        if (isset($_FILES[$this->name]) && $_FILES[$this->name]['tmp_name']) {
            if (
                !in_array($_FILES[$this->name]['type'], $this->types) ||
                $_FILES[$this->name]['size'] > $this->maxSize
            ) {
               return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    function getError()
    {
        if (isset($_FILES[$this->name]) && $_FILES[$this->name]['tmp_name']) {
            if (!in_array($_FILES[$this->name]['type'], $this->types)) {
                return _('некорректный формат файла');
            } else if ($_FILES[$this->name]['size'] > $this->maxSize) {
                return _('максимальный размер файла - 5 MB');
            }
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    function getValidationRule()
    {
        return ['types' => implode(',', $this->types)];
    }
}
