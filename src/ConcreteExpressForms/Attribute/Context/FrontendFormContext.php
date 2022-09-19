<?php

namespace ConcreteExpressForms\Attribute\Context;

use Concrete\Core\Attribute\Context\FrontendFormContext as BaseFrontendFormContext;
use Concrete\Core\Filesystem\TemplateLocator;

class FrontendFormContext extends BaseFrontendFormContext
{
    public function __construct()
    {
        parent::__construct();
        $this->preferTemplateIfAvailable('frontend', 'concrete_express_forms');
    }

    public function setLocation(TemplateLocator $locator)
    {
        $locator->setTemplate(['frontend', 'concrete_express_forms']);
        return $locator;
    }
}