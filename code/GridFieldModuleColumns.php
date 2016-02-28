<?php

class GridFieldModuleColumns implements GridField_HTMLProvider, GridField_ColumnProvider
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
        if (!in_array('Published', $columns)) {
            array_unshift($columns, 'Published');
        }

        if (!in_array('Icon', $columns)) {
            array_unshift($columns, 'Icon');
        }
    }

    public function getColumnsHandled($gridField)
    {
        return array('Icon', 'Published');
    }

    public function getColumnContent($gridField, $record, $columnName)
    {
        switch ($columnName) {
            case 'Icon':
                return '<img src="'.$record::$icon.'">';

            case 'Published':
                return '<strong>Yes</strong><br>27/02/2016 07:06pm';
        }
    }

    public function getColumnAttributes($gridField, $record, $columnName)
    {
        switch ($columnName) {
            case 'Icon':
                return array(
                    'class' => 'col-icon',
                    'width' => '64',
                );

            case 'Published':
                return array(
                    'class' => 'col-published',
                    'width' => '100',
                );
        }
    }

    public function getColumnMetadata($gridField, $columnName)
    {
        switch ($columnName) {
            case 'Icon':
                return array(
                    'title' => '',
                );

            case 'Published':
                return array(
                    'title' => 'Published',
                );
        }
    }
}
