<?php


namespace TestSystem;


abstract class AbstractFormController extends AbstractController
{
    /**
     * The underlying container instance.
     *
     * @var Form
     */
    protected $form;

    public function __construct()
    {
        parent::__construct();
        $this->form = new Form();
        $this->createAvailableFields();
    }

    /**
     * наполняет форму доступными полями
     * @return mixed
     */
    abstract protected function createAvailableFields();

    /**
     * отвечает за обработку запроса формы
     * @return mixed
     */
    abstract protected function submit();
}
