<?php

namespace ConcreteExpressForms\Express\Controller;

use Concrete\Core\Express\Controller\StandardController;
use Concrete\Core\Express\Form\Context\FrontendFormContext as CoreFrontendFormContext;
use Concrete\Core\Form\Context\Registry\ContextRegistry;

use ConcreteExpressForms\Express\Form\Processor\FormProcessor;
use ConcreteExpressForms\Express\Form\Context\FrontendFormContext;

class FormController extends StandardController
{
    public function getContextRegistry()
    {
        return new ContextRegistry([
            CoreFrontendFormContext::class => new FrontendFormContext()
        ]);
    }
    public function getFormProcessor()
    {
        return $this->app->make(FormProcessor::class);
    }
}