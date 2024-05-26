<?php

/**
Fields Summary:
- selectRank [select]
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
     'childs' => 
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
         'forbiddenNames' => 
        array (
          0 => 'id',
          1 => 'key',
          2 => 'path',
          3 => 'type',
          4 => 'index',
          5 => 'classname',
          6 => 'creationdate',
          7 => 'userowner',
          8 => 'value',
          9 => 'class',
          10 => 'list',
          11 => 'fullpath',
          12 => 'childs',
          13 => 'values',
          14 => 'cachetag',
          15 => 'cachetags',
          16 => 'parent',
          17 => 'published',
          18 => 'valuefromparent',
          19 => 'userpermissions',
          20 => 'dependencies',
          21 => 'modificationdate',
          22 => 'usermodification',
          23 => 'byid',
          24 => 'bypath',
          25 => 'data',
          26 => 'versions',
          27 => 'properties',
          28 => 'permissions',
          29 => 'permissionsforuser',
          30 => 'childamount',
          31 => 'apipluginbroker',
          32 => 'resource',
          33 => 'parentClass',
          34 => 'definition',
          35 => 'locked',
          36 => 'language',
          37 => 'omitmandatorycheck',
          38 => 'idpath',
          39 => 'object',
          40 => 'fieldname',
          41 => 'property',
          42 => 'parentid',
          43 => 'children',
          44 => 'scheduledtasks',
        ),
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
