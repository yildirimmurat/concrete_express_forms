<?php
namespace ConcreteExpressForms\Express\Form\Context;

use Concrete\Core\Express\Form\Context\FrontendFormContext as CoreFrontendFormContext;
use Concrete\Core\Filesystem\TemplateLocator;
use ConcreteExpressForms\Attribute\Context\FrontendFormContext as ContextFrontendFormContext;

class FrontendFormContext extends CoreFrontendFormContext
{
    public function setLocation(TemplateLocator $locator)
    {
        $locator = parent::setLocation($locator);
        $locator->prependLocation([DIRNAME_ELEMENTS .
            DIRECTORY_SEPARATOR .
            DIRNAME_EXPRESS .
            DIRECTORY_SEPARATOR .
            DIRNAME_EXPRESS_FORM_CONTROLS .
            DIRECTORY_SEPARATOR .
            DIRNAME_EXPRESS_FORM_CONTROLS // not a typo
        , 'concrete_express_forms']);
        return $locator;
    }

    public function getAttributeContext()
    {
        return new ContextFrontendFormContext();

    }
}