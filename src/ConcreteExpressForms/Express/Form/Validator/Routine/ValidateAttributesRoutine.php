<?php

namespace ConcreteExpressForms\Express\Form\Validator\Routine;

use Concrete\Core\Entity\Express\Form;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Express\Form\Validator\Routine\RoutineInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Support\Facade\Application as App;

class ValidateAttributesRoutine implements RoutineInterface
{
    const DEFAULT = 'default';
    const AK_ID = 'akID';
    const AT_SELECT_OPTION_VALUE = 'atSelectOptionValue';
    const NEIN = 'Nein';

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function validate(ErrorList $error, Form $form, $requestType)
    {
        $valid = true;
        $possibleFieldSets = [];
        foreach ($form->getControls() as $control) {
            $fieldSet = $control->getFieldSet()->getTitle();
            if (strpos($fieldSet, self::DEFAULT) === false && !in_array($fieldSet, $possibleFieldSets)) {
                array_push($possibleFieldSets, $fieldSet);
            } else if (strpos($fieldSet, self::DEFAULT) !== false) {
                $type = $control->getControlType();
                $validator = $type->getValidator($control);
                if (is_object($validator)) {
                    $e = $validator->validateRequest($control, $this->request);
                    if (is_object($e) && $e->has()) {
                        $valid = false;
                        $error->add($e);
                    }
                }
            }
        }

        foreach ($form->getControls() as $control) {
            $label = $control->getControlLabel();
            if (in_array($label, $possibleFieldSets)) {
                $akIDs = $this->request->get(self::AK_ID);
                $optionId = (int) $akIDs[$control->getAttributeKey()->getAttributeKeyID()][self::AT_SELECT_OPTION_VALUE];
                if ($optionId > 0) {
                    $value = (string) $this->getOptionByID($optionId)->getSelectAttributeOptionValue();

                    if ($value !== '' && $value === self::NEIN && in_array($label, $possibleFieldSets)) {
                        array_splice($possibleFieldSets, array_search($label, $possibleFieldSets) , 1);
                    }
                }
            }
        }

        foreach ($form->getControls() as $control) {
            $type = $control->getControlType();
            $validator = $type->getValidator($control);
            $fieldSet = $control->getFieldSet()->getTitle();
            if (in_array($fieldSet, $possibleFieldSets)) {
                if (is_object($validator)) {
                    $e = $validator->validateRequest($control, $this->request);
                    if (is_object($e) && $e->has()) {
                        $valid = false;
                        $error->add($e);
                    }
                }
            }
        }

        return $valid;
    }


    private function getOptionByID($id)
    {
        $entityManager = App::make(EntityManagerInterface::class);
        $repository = $entityManager->getRepository(\Concrete\Core\Entity\Attribute\Value\Value\SelectValueOption::class);
        $option = $repository->findOneBy([
            'avSelectOptionID' => $id,
        ]);

        return $option;
    }
}
