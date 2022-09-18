<?php

namespace Concrete\Package\ConcreteExpressForms;

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Entity\Express\Association;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Support\Facade\Express;
use Helpers\Express as ExpressHelper;
use Concrete\Core\Support\Facade\Application as App;

class Controller extends Package
{
    protected $pkgHandle = 'concrete_express_forms';
    protected $appVersionRequired = '^8.2';
    protected $pkgVersion = '0.1.0';
    protected $pkg;
    protected $db;
    protected $pkgAutoloaderRegistries = array(
        'src/Helpers' => 'Helpers',
    );
    public function getPackageDescription()
    {
        return t("Package for Concrete Express Forms");
    }

    public function getPackageName()
    {
        return t("Concrete Express Forms");
    }

    public function on_start()
    {
        Config::set('concrete.external.news', false);
        Config::set('concrete.external.news_overlay', false);
        Config::set('concrete.accessibility.display_help_system', false);

    }

    public function install()
    {
        $this->pkg = parent::install();
        $this->db = Database::connection();
    
        $this->createExpressObjects();
    }

    public function upgrade()
    {
        $result = parent::upgrade();
        $this->pkg = $this->app->make(PackageService::class)->getByHandle($this->pkgHandle);
        $this->db = Database::connection();
        

        return $result;
    }

    protected function createExpressObjects() {
        $details = ExpressHelper::getDetails();
        $this->createExpressObjectsWithJson($details);
    }

    protected function createExpressObjectsWithJson($details) {
        $this->createExpressObjectBuilder($details);  
        
        $this->addExpressEntries($details);

        $this->buildExpressForm($this->main_object, $details);
        $this->buildMultipleExpressForms($this->associated_objects, $details);        
    }

    protected function addExpressEntries($details) {
        $entryBuilder = Express::buildEntry($details['build']['handler']);
        $targetEntries = [];
        foreach ($details['entryDetails'] as $entryDetail) {
            $associationDetail = $entryDetail['association'];
            if ($associationDetail) {
                $target_id = $associationDetail['target'];
                if (isset($details['associations'][$target_id])) {
                    $associationType = $details['associations'][$target_id]['type'];
                    if ($associationType === 'OneToMany') {
                        $target_entity_handler = $details['associations'][$target_id]['build']['handler'];
                        $target_entity_plural_handler = $details['associations'][$target_id]['build']['plural_handler'];
                        $entryDetails = $associationDetail['entryDetails'];
                        foreach ($entryDetails as $detail) {
                            $targetEntryBuilder = Express::buildEntry($target_entity_handler);
                            foreach ($detail['attributes'] as $attribute_key => $attribute_value) {
                                $fnc = 'set' . ucfirst(camelcase($attribute_key));
                                $targetEntryBuilder->$fnc($attribute_value);
                            }
                            $targetEntry = $targetEntryBuilder->save();
                            $targetEntries[] = $targetEntry;
                        }
                    }
                }
            }
            foreach($entryDetail['attributes'] as $attribute_key => $attribute_value) {
                $fnc = 'set' . ucfirst(camelcase($attribute_key));
                $entryBuilder->$fnc($attribute_value);
            }
            $entry = $entryBuilder->save();
            $fnc = 'set' . ucfirst(camelcase($target_entity_plural_handler)); // todo
            $entry->associateEntries()->$fnc($targetEntries);
        }
    }

    protected function buildExpressForm($object, $details) {
        foreach($details['forms'] as $formDetail) {
            $form_builder_object = $object->buildForm($formDetail['name']);
            foreach($formDetail['field_sets'] as $field_set) {
                $form_object = $form_builder_object->addFieldSet($field_set['name']);
                foreach ($field_set['controls'] as $control) {
                    $form_object->addAttributeKeyControl($control);
                }
            }
            $form_builder_object->save();
        }
    }

    protected function buildMultipleExpressForms($objects, $details) {
        foreach ($objects as $object) {
            foreach($details['associations']['ingredients']['forms'] as $formDetail) { // todo:
                $form_builder_object = $object->buildForm($formDetail['name']);
                foreach($formDetail['field_sets'] as $field_set) {
                    $form_object = $form_builder_object->addFieldSet($field_set['name']);
                    foreach ($field_set['controls'] as $control) {
                        $form_object->addAttributeKeyControl($control);
                    }
                    $form_object->addAssociationControl($details['build']['handler']);
                }
                $form_builder_object->save();
            }
        }
    }

    protected function createExpressObjectBuilder(array $details) {
        $main_object = $this->createObjectBuilder($details);

        foreach($details['associations'] as $associationDetail) {
            $object = $this->createObjectBuilder($associationDetail);
        }

        $main_object->buildAssociation()->addOneToMany($object)->save();

        $this->main_object = $main_object;
        if (!isset($this->associated_objects)) {
            $this->associated_objects = [];
        }
        $this->associated_objects[] = $object;
    }

    protected function createObjectBuilder(array $details) {
        $obj = Express::buildObject(
            $details['build']['handler'],
            $details['build']['plural_handler'],
            $details['build']['name'],
            $this->pkg
        );
        foreach ($details['attributes'] as $attribute) {
            $obj->addAttribute(
                $attribute['type'],
                $attribute['name'],
                $attribute['handler']
            );
        }
        
        return $obj;
    }
}
