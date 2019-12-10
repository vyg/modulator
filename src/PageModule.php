<?php

namespace Voyage\Modulator;

use SilverStripe\ORM\DataObject;
use Voyage\Modulator\PageModule;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\TextField;
use Voyage\Modulator\ModularPage;
use SilverStripe\Control\Director;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Forms\RequiredFields;

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

    private static $hide_ancestor = PageModule::class;

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

        $labelField = new TextField('Title', 'Label');
        $labelField->setDescription('A reference name for this block, not displayed on the website');
        $fields->addFieldToTab('Root.Main', $labelField, 'Heading');

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
        return FormField::name_to_label($this->obj('ClassName')->ShortName);
    }
}
