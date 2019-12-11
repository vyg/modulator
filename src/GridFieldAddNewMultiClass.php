<?php

namespace Voyage\Modulator;

use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DropdownField;
use Symbiote\GridFieldExtensions\GridFieldExtensions;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass as SymbioteGridFieldAddNewMultiClass;

class GridFieldAddNewMultiClass extends SymbioteGridFieldAddNewMultiClass
{
    /**
     * {@inheritDoc}
     */
    public function getHTMLFragments($grid)
    {
        $classes = $this->getClasses($grid);

        if (!count($classes)) {
            return array();
        }

        GridFieldExtensions::include_requirements();

        Requirements::css('touchcast/modulator:css/GridFieldAddNewMultiClass.css');

        $newClasses = [];
        foreach ($classes as $className => $title) {
            $class = $this->unsanitiseClassName($className);
            $newClasses[$className] = $title . ' - '. $class::$description;
        }

        $field = new DropdownField(sprintf('%s[%s]', __CLASS__, $grid->getName()), '', $newClasses);
        if (Config::inst()->get(__CLASS__, 'showEmptyString')) {
            $field->setEmptyString(_t('GridFieldExtensions.SELECTTYPETOCREATE', '(Select type to create)'));
        }

        $field->addExtraClass('no-change-track');

        $data = new ArrayData(array(
            'Title'      => $this->getTitle(),
            'Link'       => Controller::join_links($grid->Link(), 'add-multi-class', '{class}'),
            'ClassField' => $field
        ));

        return array(
            $this->getFragment() => $data->renderWith(SymbioteGridFieldAddNewMultiClass::class)
        );
    }
}
