<?php

namespace Voyage\Modulator;

use Voyage\Modulator\Utilities;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use Voyage\Modulator\ModularPage;
use SilverStripe\Forms\HiddenField;
use SilverStripe\View\Requirements;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\GroupedDropdownField;



/**
 * Class PageModule.
 */
class PageModule extends DataObject
{
    public static $label = 'Page module';
    public static $description = 'The base class for all module types. You should override this description.';
    public static $category = 'General';

    private static $table_name = "PageModule";

    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar(128)',
        'Order' => 'Int',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Page' => ModularPage::class,
    );

    /**
     * @var string
     */
    private static $default_sort = 'Order';

    private static $extensions = [
        Versioned::class
    ];

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Summary' => 'Summary',
    );

    /**
     * @var array
     */
    private static $searchable_fields = array();


    /**
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields('Title', 'Type');
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        if (!$this->exists()) {

            // The new module state
            // Requirements::css('touchcast/modulator:css/PageModule.css');
            Requirements::javascript('touchcast/modulator:javascript/PageModule.js');

            $allowedModules = array();

            // Determine the type of the parent page
            $session = Utilities::getSession();
            $currentPageID = $session->get('SilverStripe\CMS\Controllers\CMSMain.currentPage');

            if ($currentPageID) {
                $currentPage = SiteTree::get_by_id($currentPageID);

                if ($currentPage) {
                    $currentPageClass = $currentPage->ClassName;

                    // Get the list of allowed modules for this page type
                    if (class_exists($currentPageClass) && method_exists($currentPageClass, 'getAllowedModules')) {
                        $allowedModules = $currentPageClass::getAllowedModules();
                    }
                }
            }

            $classList = array();

            foreach ($allowedModules as $class) {
                $instance = new $class();

                $classList[$class::$category][$class] = sprintf('%s - %s', $class::$label, $class::$description);
            }

            $fields = new FieldList();

            if (!count($allowedModules)) {
                $typeField = new LiteralField('Type', '<span class="message required">There are no module types defined, please create some.</span>');

                $fields->push($typeField);
            } else {
                $labelField = new TextField('Title', 'Label');
                $labelField->setDescription('A reference name for this block, not displayed on the website');
                $fields->push($labelField);

                $typeField = new GroupedDropdownField('NewClassName', 'Type', $classList);
                $typeField->setDescription('The type of module determines what content and functionality it will provide');
                $fields->push($typeField);
            }

            $this->extend('updateCMSFields', $fields);
        } else {
            // Existing module state
            $fields = parent::getCMSFields();

            // Don't expose Order to the CMS
            $fields->removeFieldFromTab('Root.Main', 'Order');
            $fields->removeFieldFromTab('Root.Main', 'PageID');

            // Helps us keep track of preview focus
            $fields->addFieldToTab('Root.Main', new HiddenField('ModulatorID', 'ModulatorID', $this->ID));
        }

        return $fields;
    }

    /**
     * @return string
     */
    public function Content()
    {
        return $this->renderWith(array($this->ClassName));
    }

    /**
     * Gets the page link for the parent page.
     *
     * @return string
     */
    public function Link()
    {
        return $this->Page()->Link();
    }

    /**
     * Where the magic happens. Convert the module from the default base class to the chosen type.
     */
    public function onBeforeWrite()
    {
        if ($this->ClassName == PageModule::class && !$this->exists() && !empty($this->NewClassName)) {
            $instance = $this->newClassInstance($this->NewClassName);
            $this->ClassName = $this->NewClassName;

            // New modules should default to the bottom of the page
            $this->Order = 1;

            if ($this->Page()->exists()) {
                $lastModule = $this->Page()->Modules()->sort('Order DESC')->limit(1)->first();

                if ($lastModule) {
                    $this->Order = $lastModule->Order + 1;
                }
            }
        }

        parent::onBeforeWrite();
    }

    /**
     * Hook to supply module text content to the parent page element for indexing in searches.
     * Override in sub-class.
     *
     * @return string
     */
    public function getSearchBody()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getSummaryContent()
    {
        return '';
    }
}
