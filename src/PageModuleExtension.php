<?php

namespace Voyage\Modulator;

use DataExtension;
use Classinfo;
use DB;
use Voyage\Modulator\PageModule;



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
            if (in_array(PageModule::class, $ancestry) && $module->isPublished()) {

                // Note: this code previously used Versioned's stage system to publish the changes, but this wasn't always reliable.
                // Manually updating the published stage isn't ideal...
                DB::query(sprintf('UPDATE PageModule_Live SET `Order` = %s WHERE ID = %s LIMIT 1', $module->Order, $module->ID));
            }
        }
    }
}
