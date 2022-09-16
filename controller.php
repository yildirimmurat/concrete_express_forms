<?php

namespace Concrete\Package\ConcreteExpressForms;

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Express;
use Helpers\Express as ExpressHelper;

class Controller extends Package
{
    protected $pkgHandle = 'concrete_express_forms';
    protected $appVersionRequired = '^8.2';
    protected $pkgVersion = '0.1.2';
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
        
        $this->createExpressObjects();

        return $result;
    }

    protected function createExpressObjects() {
        $objectsDetails = ExpressHelper::getOneToManyDetails();
        $this->createOneToManyRelatedObjects($objectsDetails);
    }

    protected function createOneToManyRelatedObjects(array $objectsDetails) {
        $inversed_object = $this->createExpressObjectBuilder($objectsDetails['inversed']);
        $target_object = $this->createExpressObjectBuilder($objectsDetails['target']);

        $inversed_object->buildAssociation()->addOneToMany($target_object)->save();
        $this->buildExpressForm($inversed_object, $objectsDetails['inversed']);
        $this->buildExpressForm($target_object, $objectsDetails['target']);
    }

    protected function buildExpressForm($object, $details) {
        $form_builder_object = $object->buildForm('Form');
        $form_object = $form_builder_object->addFieldSet('Basics');
        foreach($details['attributes'] as $attribute) {
            $form_object->addAttributeKeyControl($attribute['handler']);
        }
        $form_builder_object->save();
    }

    protected function createExpressObjectBuilder(array $details) {
        $object = Express::buildObject(
            $details['build']['handler'],
            $details['build']['plural_handler'],
            $details['build']['name'],
            $this->pkg
        );
        foreach ($details['attributes'] as $attribute) {
            $object->addAttribute(
                $attribute['type'],
                $attribute['name'],
                $attribute['handler']
            );
        }

        return $object;
    }
}
