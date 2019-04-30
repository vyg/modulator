<?php

namespace Voyage\Modulator;

use SilverStripe\Core\ClassInfo;
use Voyage\Modulator\PageModule;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\LiteralField;
use SilverStripe\CMS\Controllers\SilverStripeNavigator;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\CMSPreviewable;

/**
 * Class PageModuleVersionedDataObjectDetailsForm_ItemRequest.
 */
class GridFieldDetailForm_ItemRequestExtension extends Extension
{
    public function updateItemEditForm(&$form)
    {
        $fields = $form->Fields();
        if ($this->owner->record instanceof CMSPreviewable && !$fields->fieldByName('SilverStripeNavigator'))
        {
            $this->injectNavigatorAndPreview($form, $fields);
        }
    }

    /**
     * Add extra classes/javascript to form to enable preview and render with Silverstripe Navigator
     */
    private function injectNavigatorAndPreview(&$form, &$fields)
    {
        Requirements::javascript('touchcast/modulator:/javascript/LeftAndMain.Preview.js');

        $record = $this->owner->getRecord();

        // Hide the 'Save & publish' button if we're on a brand new module.
        if ($record && $record->ID == 0) {
            $actions = $form->Actions();

            // Remove the publish button on the pre-module state
            $actions->removeByName('action_publish');

            // Remove the save action if there are no sub-classes to instantiate
            $classes = ClassInfo::subclassesFor(PageModule::class);
            unset($classes[PageModule::class]);

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

        // Create a navigator and point it at the parent page
        $navigator = new SilverStripeNavigator($this->owner->record->Page());

        $navField = new LiteralField('SilverStripeNavigator', $navigator->renderWith('SilverStripe\Admin\Includes\LeftAndMain_SilverStripeNavigator'));
        $navField->setAllowHTML(true);

        $fields = $form->Fields();

        $fields->push($navField);
    }
}
