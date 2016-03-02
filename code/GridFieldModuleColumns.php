<?php

/**
 * Class GridFieldModuleColumns
 */
class GridFieldModuleColumns extends GridFieldDataColumns implements GridField_HTMLProvider
{
    /*
     * GridField_HTMLProvider
     */
    public function getHTMLFragments($gridField)
    {
        Requirements::css(MODULATOR_PATH.'/css/GridFieldModuleColumns.css');
    }

    /*
     * GridField_ColumnProvider
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('Icon', $columns)) {
            array_unshift($columns, 'Icon');
        }
    }

    /**
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return array('Icon', 'Summary');
    }

    /**
     * @return string
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        switch ($columnName) {
            case 'Icon':
                return '<img src="'.$record::$icon.'">';

            case 'Summary':
                return '<strong>'.$record->Title.'</strong><br>'.$record->getSummaryContent();
        }
    }

    /**
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        switch ($columnName) {
            case 'Icon':
                return array(
                    'class' => 'col-icon',
                    'width' => '64',
                );

            default:
                return array();
        }
    }

    /**
     * @return array
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        switch ($columnName) {
            case 'Icon':
                return array(
                    'title' => 'Type',
                );

            default:
                return array();
        }
    }
}
