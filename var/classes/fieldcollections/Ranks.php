<?php

/**
 * Fields Summary:
 * - selectRank [select]
 */

return Pimcore\Model\DataObject\Fieldcollection\Definition::__set_state(array(
   'dao' => NULL,
   'key' => 'Ranks',
   'parentClass' => '',
   'implementsInterfaces' => '',
   'title' => '',
   'group' => '',
   'layoutDefinitions' => 
  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
     'fieldtype' => 'panel',
     'layout' => NULL,
     'border' => false,
     'name' => NULL,
     'type' => NULL,
     'region' => NULL,
     'title' => NULL,
     'width' => 0,
     'height' => 0,
     'collapsible' => false,
     'collapsed' => false,
     'bodyStyle' => NULL,
     'datatype' => 'layout',
     'permissions' => NULL,
     'children' => 
    array (
      0 => 
      Pimcore\Model\DataObject\ClassDefinition\Data\Select::__set_state(array(
         'fieldtype' => 'select',
         'options' => 
        array (
          0 => 
          array (
            'key' => 'Senior Faculty',
            'value' => 'Senior Faculty',
          ),
          1 => 
          array (
            'key' => 'Faculty',
            'value' => 'Faculty',
          ),
          2 => 
          array (
            'key' => 'IP Strategist, Project Lead',
            'value' => 'IP Strategist, Project Lead',
          ),
          3 => 
          array (
            'key' => 'Head, Patent Analytics',
            'value' => 'Head, Patent Analytics',
          ),
          4 => 
          array (
            'key' => 'IP Strategist, Client Lead',
            'value' => 'IP Strategist, Client Lead',
          ),
          5 => 
          array (
            'key' => 'IP Strategist',
            'value' => 'IP Strategist',
          ),
          6 => 
          array (
            'key' => 'Associate IP Strategist',
            'value' => 'Associate IP Strategist',
          ),
          7 => 
          array (
            'key' => '',
            'value' => '',
          ),
        ),
         'width' => '',
         'defaultValue' => '',
         'optionsProviderClass' => '',
         'optionsProviderData' => '',
         'columnLength' => 190,
         'dynamicOptions' => false,
         'name' => 'selectRank',
         'title' => 'Position / Title',
         'tooltip' => '',
         'mandatory' => false,
         'noteditable' => false,
         'index' => false,
         'locked' => false,
         'style' => '',
         'permissions' => NULL,
         'datatype' => 'data',
         'relationType' => false,
         'invisible' => false,
         'visibleGridView' => false,
         'visibleSearch' => false,
         'blockedVarsForExport' => 
        array (
        ),
         'defaultValueGenerator' => '',
      )),
    ),
     'locked' => false,
     'blockedVarsForExport' => 
    array (
    ),
     'icon' => NULL,
     'labelWidth' => 100,
     'labelAlign' => 'left',
  )),
   'generateTypeDeclarations' => true,
   'blockedVarsForExport' => 
  array (
  ),
));
