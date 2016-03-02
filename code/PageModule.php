<?php

/**
 * Class PageModule
 */
class PageModule extends DataObject
{
    public static $label = 'Page module';
    public static $icon = 'modulator/images/module-generic.png';
    public static $description = 'The base class for all module types. You should override this description.';

    private static $db = array(
        'Title' => 'Varchar(128)',
        'ExtraClasses' => 'Varchar(128)',
        'Order' => 'Int',
        'Active' => 'Boolean',
    );

    private static $has_one = array(
        'Page' => 'ModularPage',
    );

    private static $default_sort = 'Order';

    private static $extensions = array(
        'VersionedDataObject',
    );

    private static $summary_fields = array(
        'Summary' => 'Summary',
    );

    private static $searchable_fields = array();

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        if ($this->ID == 0) {
            // The new module state
            Requirements::css(MODULATOR_PATH.'/css/PageModule.css');
            Requirements::javascript(MODULATOR_PATH.'/javascript/PageModule.js');

            $classes = ClassInfo::subclassesFor('PageModule');

            // Don't let them choose the base class
            unset($classes['PageModule']);

            $classList = array();

            foreach ($classes as $class) {
                $instance = new $class();

                $classList[$class] = '<img src="'.$instance::$icon.'"><strong>'.$class::$label.'</strong><p>'.$class::$description.'</p>';
            }

            $labelField = new TextField('Title', 'Label');
            $labelField->setDescription('A reference name for this block, not displayed on the website');

            $typeField = new OptionSetField('NewClassName', 'Type', $classList);
            $typeField->setDescription('The type of module determines what content and functionality it will provide');

            $fields = new FieldList(
                $labelField,
                $typeField
            );
        } else {
            // Existing module state
            $fields = parent::getCMSFields();

            // Don't expose Order to the CMS
            $fields->removeFieldFromTab('Root.Main', 'Order');
        }

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * @return string
     */
    public function Content()
    {
        return $this->renderWith(array($this->ClassName, 'PageModule'));
    }

    /**
     * Where the magic happens. Convert the module from the default base class to the chosen type.
     */
    public function onBeforeWrite()
    {
        if ($this->ClassName == 'PageModule' && $this->ID == 0) {
            $instance = $this->newClassInstance($this->NewClassName);
            $this->ClassName = $this->NewClassName;
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
