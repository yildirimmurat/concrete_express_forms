<?php

namespace Concrete\Package\ConcreteExpressForms;

defined('C5_EXECUTE') or die(_("Access Denied."));

use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Package\PackageService;

class Controller extends Package
{
    protected $pkgHandle = 'concrete_express_forms';
    protected $appVersionRequired = '^8.2';
    protected $pkgVersion = '0.1.0';
    protected $pkg;
    protected $db;
    protected $pkgAutoloaderRegistries = array(
        // 'src/Helpers' => 'Helpers',
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
    }

    public function upgrade()
    {
        $result = parent::upgrade();
        $this->pkg = $this->app->make(PackageService::class)->getByHandle($this->pkgHandle);
        $this->db = Database::connection();

        return $result;
    }
}
