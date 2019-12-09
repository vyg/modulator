<?php

namespace Voyage\Modulator;

use Voyage\Modulator\Utilities;
use SilverStripe\ORM\DataObject;
use Voyage\Modulator\PageModule;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\TextField;
use Voyage\Modulator\ModularPage;
use SilverStripe\Control\Director;
use SilverStripe\Forms\HiddenField;
use SilverStripe\View\Requirements;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\GroupedDropdownField;

/**
 * Class PageModule.
 */
class PageModule extends DataObject implements CMSPreviewable
{
    public static $label = 'Page module';
    public static $description = 'The base class for all module types. You should override this description.';
    public static $category = 'General';

    private static $table_name = "PageModule";

    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(128)',
        'Order' => 'Int',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Page' => ModularPage::class,
    ];

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
    private static $summary_fields = [
        'Title',
        'NiceType' => 'Type',
    ];

    // /**
    //  * @var array
    //  */
    private static $searchable_fields = [
        'Title'
    ];


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
        $fields = parent::getCMSFields();

        // if (!$this->exists()) {

        //     // The new module state
        //     // Requirements::css('touchcast/modulator:css/PageModule.css');
        //     Requirements::javascript('touchcast/modulator:javascript/PageModule.js');

        //     $allowedModules = array();

        //     // Determine the type of the parent page
        //     $session = Utilities::getSession();
        //     $currentPageID = $session->get('SilverStripe\CMS\Controllers\CMSMain.currentPage');

        //     if ($currentPageID) {
        //         $currentPage = SiteTree::get_by_id($currentPageID);

        //         if ($currentPage) {
        //             $currentPageClass = $currentPage->ClassName;

        //             // Get the list of allowed modules for this page type
        //             if (class_exists($currentPageClass) && method_exists($currentPageClass, 'getAllowedModules')) {
        //                 $allowedModules = $currentPageClass::getAllowedModules();
        //             }
        //         }
        //     }

        //     $classList = array();

        //     foreach ($allowedModules as $class) {
        //         $instance = new $class();

        //         $classList[$class::$category][$class] = sprintf('%s - %s', $class::$label, $class::$description);
        //     }

        //     // $fields = new FieldList();

        //     if (!count($allowedModules)) {
        //         // $typeField = new LiteralField('Type', '<span class="message required">There are no module types defined, please create some.</span>');

        //         // $fields->push($typeField);
        //     } else {
        //         $labelField = new TextField('Title', 'Label');
        //         $labelField->setDescription('A reference name for this block, not displayed on the website');
        //         $fields->push($labelField);

        //         // $typeField = new GroupedDropdownField('NewClassName', 'Type', $classList);
        //         // $typeField->setDescription('The type of module determines what content and functionality it will provide');
        //         // $fields->push($typeField);
        //     }

        //     $labelField = new TextField('Title', 'Label');
        //     $labelField->setDescription('A reference name for this block, not displayed on the website');
        //     $fields->push($labelField);


        //     $this->extend('updateCMSFields', $fields);
        // } else {
        //     // Existing module state
        //     $fields = parent::getCMSFields();

        //     // Don't expose Order to the CMS
        //     $fields->removeFieldFromTab('Root.Main', 'Order');
        //     $fields->removeFieldFromTab('Root.Main', 'PageID');

        //     // Helps us keep track of preview focus
        //     $fields->addFieldToTab('Root.Main', new HiddenField('ModulatorID', 'ModulatorID', $this->ID));
        // }

        $labelField = new TextField('Title', 'Label');
        $labelField->setDescription('A reference name for this block, not displayed on the website');
        // $fields->push($labelField);
        $fields->addFieldToTab('Root.Main', $labelField);


        // Don't expose Order to the CMS
        $fields->removeFieldFromTab('Root.Main', 'Order');
        $fields->removeFieldFromTab('Root.Main', 'PageID');

        // Helps us keep track of preview focus
        $fields->addFieldToTab('Root.Main', new HiddenField('ModulatorID', 'ModulatorID', $this->ID));

        return $fields;
    }

    public function PreviewLink($action = null)
    {
        return Controller::join_links(Director::baseURL(), 'cms-preview', 'show', $this->ClassName, $this->ID);
    }

    public function getMimeType()
    {
        return 'text/html';
    }

    public function CMSEditLink()
    {
    }

    /**
     * @return string
     */
    public function Content()
    {
        return $this->renderWith(sprintf('Includes/Modules/%s', $this->ClassName));
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
    // public function onBeforeWrite()
    // {
    //     if ($this->ClassName == PageModule::class && !$this->exists() && !empty($this->NewClassName)) {
    //         $instance = $this->newClassInstance($this->NewClassName);
    //         $this->ClassName = $this->NewClassName;

    //         // New modules should default to the bottom of the page
    //         $this->Order = 1;

    //         if ($this->Page()->exists()) {
    //             $lastModule = $this->Page()->Modules()->sort('Order DESC')->limit(1)->first();

    //             if ($lastModule) {
    //                 $this->Order = $lastModule->Order + 1;
    //             }
    //         }
    //     }

    //     parent::onBeforeWrite();
    // }

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

    public function NiceType()
    {
        return FormField::name_to_label($this->obj('ClassName'));
    }
}
