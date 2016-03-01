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
     * @return Form
     */
    public function ItemEditForm()
    {
        $form = parent::ItemEditForm();

        $record = $this->getRecord();

        // Hide the 'Save & publish' button if we're on a brand new module.
        if ($record->ID == 0) {
            $actions = $form->Actions();

            $actions->removeByName('action_publish');
        }

        return $form;
    }
}
