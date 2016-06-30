<?php

/**
 * Class PageModuleVersionedDataObjectDetailsForm.
 */
class PageModuleVersionedDataObjectDetailsForm extends VersionedDataObjectDetailsForm
{
}

/**
 * Class PageModuleVersionedDataObjectDetailsForm_ItemRequest.
 */
class PageModuleVersionedDataObjectDetailsForm_ItemRequest extends VersionedDataObjectDetailsForm_ItemRequest
{
    private static $allowed_actions = array(
        'edit',
        'view',
        'ItemEditForm',
    );

    /**
     * Additional magic happens here. Trick LeftAndMain into thinking we're a previewable SiteTree object.
     * 
     * @return Form
     */
    public function ItemEditForm()
    {
        Requirements::javascript(MODULATOR_PATH.'/javascript/LeftAndMain.Preview.js');

        $form = parent::ItemEditForm();

        $record = $this->getRecord();

        // Hide the 'Save & publish' button if we're on a brand new module.
        if ($record && $record->ID == 0) {
            $actions = $form->Actions();

            // Remove the publish button on the pre-module state
            $actions->removeByName('action_publish');

            // Remove the save action if there are no sub-classes to instantiate
            $classes = ClassInfo::subclassesFor('PageModule');
            unset($classes['PageModule']);

            if (!count($classes)) {
                $actions->removeByName('action_save');
            }
        }

        // Enable CMS preview
        // .cms-previewable enables the preview panel in the front-end
        // .cms-pagemodule CSS class is used by our javascript to handle previews
        if ($form && is_object($form)) {
            $form->addExtraClass('cms-previewable cms-pagemodule');
        }

        // Creat a navigaor and point it at the parent page
        $navigator = new SilverStripeNavigator($this->record->Page());

        $navField = new LiteralField('SilverStripeNavigator', $navigator->renderWith('LeftAndMain_SilverStripeNavigator'));
        $navField->setAllowHTML(true);

        $fields = $form->Fields();

        $fields->push($navField);

        return $form;
    }
}
