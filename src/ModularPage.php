<?php

namespace Voyage\Modulator;

use Page;
use PageController;
use SilverStripe\Core\ClassInfo;
use Voyage\Modulator\PageModule;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\GridField\GridField;
use Voyage\Modulator\GridFieldConfig_ModuleEditor;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class ModularPage.
 */
class ModularPage extends Page
{
    /**
     * @var array
     */
    private static $db = [];

    /**
     * @var array
     */
    private static $table_name = "ModularPage";

    /**
     * @var array
     */
    private static $has_many = [
        'Modules' => PageModule::class,
    ];

    private static $owns = [
        'Modules'
    ];

    /**
     * @var array
     */
    public static $allowed_modules = [];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // The SiteTree Content field is used to hold the search index, never displayed
        $fields->replaceField('Content', LiteralField::create('Content', ''));

        // Modules can only be added to pages which exist
        if ($this->exists()) {
            $config = GridFieldConfig_ModuleEditor::create();

            if ($this->Modules()->Count()) {
                $config->addComponent(new GridFieldOrderableRows('Order'));
            }

            $gridField = new GridField('Modules', 'Content blocks', $this->Modules(), $config);

            $fields->addFieldToTab('Root.Main', $gridField, 'Metadata');
        } else {
            $warningField = new LiteralField('Type', '<p class="message warning">You need to save this page before you can add modules to it.</p>');

            $fields->addFieldToTab('Root.Main', $warningField, 'Metadata');
        }

        return $fields;
    }

    /**
     * Iterate through all the modules and add their content to the parent page, so it can be found in searches.
     */
    public function onBeforeWrite()
    {
        $pageClass = get_called_class();

        // Behaviour can be disabled via the config
        $writeContent = Config::inst()->get($pageClass, 'write_content');

        // If a custom config doesn't exist, check ModularPage
        if (is_null($writeContent)) {
            $writeContent = Config::inst()->get(__CLASS__, 'write_content');
        }

        if ($writeContent) {
            $classes = ClassInfo::subclassesFor(__CLASS__);

            // Only run this code if we're on a valid instance of this class.
            // Fixes bug when changaing page type via the CMS (e.g. ModularPage -> Page)
            if (in_array($this->ClassName, $classes)) {
                if ($this->Modules()->Count()) {
                    $searchBody = '';

                    foreach ($this->Modules() as $module) {
                        $searchBody .= $module->getSearchBody().PHP_EOL;
                    }

                    $this->Content = $searchBody;
                }
            }
        }

        parent::onBeforeWrite();
    }

    /**
     * Build the list of allowed modules for this page type.
     *
     * @return array the list of class names to be used
     */
    public static function getAllowedModules()
    {
        $pageClass = get_called_class();

        // Allow an alternate module base class to be specified, per page type
        $baseClass = Config::inst()->get($pageClass, 'base_class');

        // If a custom config doesn't exist, check ModularPage
        if (empty($baseClass)) {
            $baseClass = Config::inst()->get(__CLASS__, 'base_class');
        }

        // If no config exists, use defaults
        if (empty($baseClass)) {
            $baseClass = PageModule::class;
        }

        $classes = ClassInfo::subclassesFor($baseClass);

        // Don't let them choose the base class
        unset($classes[$baseClass]);

        // Remove any classes not on the whitelist
        if (count(static::$allowed_modules)) {
            foreach ($classes as $class) {
                if (!in_array($class, static::$allowed_modules)) {
                    unset($classes[$class]);
                }
            }
        }

        return $classes;
    }
}

/**
 * Class ModularPageController.
 */
class ModularPageController extends PageController
{
    /**
     * return ArrayList.
     */
    public function ActiveModules()
    {
        // If the CMS has asked to focus on 1 module for a preview, just show that.
        if ($this->request->getVar('moduleFocus')) {
            return $this->Modules()->filter('ID', $this->request->getVar('moduleFocus'));
        }

        $modules = $this->Modules();

        return $modules;
    }

    /**
     * Override the $Content template variable so its never used.
     * Content should come from <% loop $ActiveModules %>.
     *
     * @return string
     */
    public function Content()
    {
        return '';
    }
}
