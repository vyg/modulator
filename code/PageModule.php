<?php

/**
 * Class PageModule.
 */
class PageModule extends DataObject
{
    public static $label = 'Page module';
    public static $description = 'The base class for all module types. You should override this description.';
    public static $category = 'General';

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
        'Page' => 'ModularPage',
    );

    /**
     * @var string
     */
    private static $default_sort = 'Order';

    /**
     * @var array
     */
    private static $extensions = array(
        'VersionedDataObject',
    );

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
            Requirements::css(MODULATOR_PATH.'/css/PageModule.css');
            Requirements::javascript(MODULATOR_PATH.'/javascript/PageModule.js');

            $allowedModules = array();

            // Determine the type of the parent page
            $currentPageID = Session::get('CMSMain.currentPage');

            if ($currentPageID) {
                $currentPage = SiteTree::get_by_id('SiteTree', $currentPageID);

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
        if ($this->ClassName == 'PageModule' && !$this->exists() && !empty($this->NewClassName)) {
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
     * Remove the live stage on delete, otherwise content is orphaned in live and cannot be removed.
     */
    protected function onAfterDelete()
    {
        // Look up all of the associated tables
        $ancestry = Classinfo::ancestry(get_called_class());

        foreach ($ancestry as $class) {
            if (Classinfo::hasTable($class)) {

                // Live table
                DB::query(sprintf('DELETE FROM "%s_Live" WHERE ID = %s LIMIT 1', $class, $this->ID));

                // Version history
                DB::query(sprintf('DELETE FROM "%s_versions" WHERE RecordID = %s', $class, $this->ID));
            }
        }

        parent::onAfterDelete();
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
