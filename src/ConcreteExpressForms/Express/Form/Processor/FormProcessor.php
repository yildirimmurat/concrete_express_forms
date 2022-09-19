<?php
namespace ConcreteExpressForms\Express\Form\Processor;

use Symfony\Component\HttpFoundation\Request;
use Concrete\Core\Express\Form\Processor\StandardProcessor;
use ConcreteExpressForms\Express\Form\Validator\FormValidator;

class FormProcessor extends StandardProcessor
{
    public function getValidator(Request $request)
    {
        $validator = new FormValidator($this->app, $this->app->make('error'), $request);
        return $validator;
    }
}