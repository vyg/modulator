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
     * Additional magic happens here. Trick LeftAndMain into thinking we're a previewable SiteTree object
     * 
     * @return Form
     */
    public function ItemEditForm()
    {
        Requirements::javascript(MODULATOR_PATH.'/javascript/LeftAndMain.Preview.js');

        $form = parent::ItemEditForm();

        $record = $this->getRecord();

        // Hide the 'Save & publish' button if we're on a brand new module.
        if ($record->ID == 0) {
            $actions = $form->Actions();

            $actions->removeByName('action_publish');
        }

        // Enable CMS preview
        // The cms-pagemodule CSS class is used by our javascript to handle previews
        // TODO: Tidy this up
        $form->addExtraClass('cms-previewable cms-pagemodule');

        $navigator = new SilverStripeNavigator($this->record->Page());

        $navField = new LiteralField('SilverStripeNavigator', $navigator->renderWith('LeftAndMain_SilverStripeNavigator'));
        $navField->setAllowHTML(true);

        $fields = $form->Fields();
        $fields->push($navField);

        return $form;
    }
}
