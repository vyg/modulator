<?php

/**
 * Class PageModuleExtension.
 */
class PageModuleExtension extends DataExtension
{
    /**
     * SortableGridField doesn't play nice with versioned data objects.
     * Altering the order will only update the draft stage.
     * This extension hooks into the reorder event and pushes the changes through to Live.
     *
     * @param HastManyList $list The list of modules
     */
    public function onAfterReorderItems($list)
    {
        foreach ($list as $module) {
            $ancestry = Classinfo::ancestry($module->ClassName);

            // Only apply this action to PageModule objects, not all SortableGridField items
            if (in_array('PageModule', $ancestry) && $module->isPublished()) {
                $order = (int) $module->Order;

                $origStage = Versioned::current_stage();
                Versioned::reading_stage('Live');

                $module->Order = $order;
                $module->write();

                Versioned::reading_stage($origStage);
            }
        }
    }
}
