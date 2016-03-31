<?php

/**
 * Class ModularPage.
 */
class ModularPage extends SiteTree
{
    private static $db = array(
    );

    private static $has_many = array(
        'Modules' => 'PageModule',
    );

    public static $allowed_modules = array();

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // The SiteTree Content field is used to hold the search index, never displayed
        $fields->replaceField('Content', LiteralField::create('Content', ''));

        // Modules can only be added to pages which exist
        if ($this->ID != 0) {
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
        if ($this->Modules()->Count()) {
            $searchBody = '';

            foreach ($this->Modules() as $module) {
                $searchBody .= $module->getSearchBody().PHP_EOL;
            }

            $this->Content = $searchBody;
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
        $classes = ClassInfo::subclassesFor('PageModule');

        // Don't let them choose the base class
        unset($classes['PageModule']);

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

    /*
     * @return FieldList
     */
    /*
    TODO: Publish all modules via primary button

    public function getCMSActions()
    {
        $actions = parent::getCMSActions();

        foreach ($actions as $action) {
            $firstAction = $action;
            break;
        }

        if ($this->canPublish() && !$this->getIsDeletedFromStage()) {
            // "publish", as with "save", it supports an alternate state to show when action is needed.
            $firstAction->push(
                $publish = FormAction::create('publishallmodules', _t('SiteTree.BUTTONPUBLISHEDALLMODULES', 'All modules published'))
                    ->setAttribute('data-icon', 'accept')
                    ->setAttribute('data-text-alternate', _t('SiteTree.BUTTONSAVEPUBLISHALLMODULES', 'Publish all modules'))
            );

            // Set up the initial state of the button to reflect the state of the underlying SiteTree object.
            // $publish->addExtraClass('ss-ui-alternate');
        }

        // Hook for extensions to add/remove actions.
        $this->extend('updateCMSActions', $actions);

        return $actions;
    }
    */
}

/**
 * Class ModularPage_Controller.
 */
class ModularPage_Controller extends ContentController
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
