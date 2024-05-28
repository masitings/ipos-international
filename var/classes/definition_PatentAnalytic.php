<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - localizedfields [localizedfields]
 * -- title [input]
 * -- author [input]
 * - content [wysiwyg]
 * - releaseDate [datetime]
 * - coverView [checkbox]
 * - latest [checkbox]
 * - resourceType [select]
 * - coverImage [hotspotimage]
 * - authorIcon [hotspotimage]
 * - interestedTitle [input]
 * - interestedList [manyToManyObjectRelation]
 * - shares [manyToManyObjectRelation]
 * - file [link]
 * - seoTitle [input]
 * - seoDescription [textarea]
 * - tags [multiselect]
 * - videoTime [input]
 * - detailVideo [video]
 */

return Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
   'id' => '18',
   'name' => 'PatentAnalytic',
   'description' => '',
   'creationDate' => 0,
   'modificationDate' => 1716911154,
   'userOwner' => 2,
   'userModification' => 59,
   'parentClass' => '',
   'implementsInterfaces' => '',
   'listingParentClass' => '',
   'useTraits' => '',
   'listingUseTraits' => '',
   'encryption' => false,
   'encryptedTables' => 
  array (
  ),
   'allowInherit' => false,
   'allowVariants' => false,
   'showVariants' => false,
   'fieldDefinitions' => 
  array (
  ),
   'layoutDefinitions' => 
  Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
     'fieldtype' => 'panel',
     'layout' => NULL,
     'border' => false,
     'name' => 'pimcore_root',
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
      Pimcore\Model\DataObject\ClassDefinition\Layout\Tabpanel::__set_state(array(
         'fieldtype' => 'tabpanel',
         'border' => false,
         'tabPosition' => NULL,
         'name' => 'Layout',
         'type' => NULL,
         'region' => NULL,
         'title' => 'Layout',
         'width' => '',
         'height' => '',
         'collapsible' => false,
         'collapsed' => false,
         'bodyStyle' => '',
         'datatype' => 'layout',
         'permissions' => NULL,
         'children' => 
        array (
          0 => 
          Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'fieldtype' => 'panel',
             'layout' => NULL,
             'border' => false,
             'name' => 'BaseData',
             'type' => NULL,
             'region' => NULL,
             'title' => 'Base Data',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'permissions' => NULL,
             'children' => 
            array (
              0 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields::__set_state(array(
                 'fieldtype' => 'localizedfields',
                 'children' => 
                array (
                  0 => 
                  Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                     'fieldtype' => 'input',
                     'width' => 600,
                     'defaultValue' => NULL,
                     'columnLength' => 190,
                     'regex' => '',
                     'regexFlags' => 
                    array (
                    ),
                     'unique' => false,
                     'showCharCount' => false,
                     'name' => 'title',
                     'title' => 'Title',
                     'tooltip' => '',
                     'mandatory' => true,
                     'noteditable' => false,
                     'index' => false,
                     'locked' => false,
                     'style' => '',
                     'permissions' => NULL,
                     'datatype' => 'data',
                     'relationType' => false,
                     'invisible' => false,
                     'visibleGridView' => false,
                     'visibleSearch' => true,
                     'blockedVarsForExport' => 
                    array (
                    ),
                     'defaultValueGenerator' => '',
                  )),
                  1 => 
                  Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                     'fieldtype' => 'input',
                     'width' => 400,
                     'defaultValue' => NULL,
                     'columnLength' => 190,
                     'regex' => '',
                     'regexFlags' => 
                    array (
                    ),
                     'unique' => false,
                     'showCharCount' => false,
                     'name' => 'author',
                     'title' => 'Author',
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
                     'visibleSearch' => true,
                     'blockedVarsForExport' => 
                    array (
                    ),
                     'defaultValueGenerator' => '',
                  )),
                ),
                 'name' => 'localizedfields',
                 'region' => NULL,
                 'layout' => NULL,
                 'title' => '',
                 'width' => '',
                 'height' => '',
                 'maxTabs' => NULL,
                 'border' => false,
                 'provideSplitView' => false,
                 'tabPosition' => NULL,
                 'hideLabelsWhenTabsReached' => NULL,
                 'referencedFields' => 
                array (
                ),
                 'fieldDefinitionsCache' => NULL,
                 'permissionView' => NULL,
                 'permissionEdit' => NULL,
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
                 'visibleGridView' => true,
                 'visibleSearch' => true,
                 'blockedVarsForExport' => 
                array (
                ),
                 'labelWidth' => 0,
                 'labelAlign' => 'left',
              )),
              1 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Wysiwyg::__set_state(array(
                 'fieldtype' => 'wysiwyg',
                 'width' => '',
                 'height' => '',
                 'toolbarConfig' => '',
                 'excludeFromSearchIndex' => false,
                 'maxCharacters' => '',
                 'name' => 'content',
                 'title' => 'Content',
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
              )),
              2 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Datetime::__set_state(array(
                 'fieldtype' => 'datetime',
                 'queryColumnType' => 'bigint(20)',
                 'columnType' => 'bigint(20)',
                 'defaultValue' => NULL,
                 'useCurrentDate' => false,
                 'name' => 'releaseDate',
                 'title' => 'Published Date',
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
              3 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Checkbox::__set_state(array(
                 'fieldtype' => 'checkbox',
                 'defaultValue' => 0,
                 'name' => 'coverView',
                 'title' => 'Featured Item',
                 'tooltip' => 'To feature at Resources Overview landing page > Get Insights Component > Patent Analytics Reports Tab',
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
              4 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Checkbox::__set_state(array(
                 'fieldtype' => 'checkbox',
                 'defaultValue' => 0,
                 'name' => 'latest',
                 'title' => 'Publish as Latest',
                 'tooltip' => 'To appear in "Latest Patent Analytics Reports" component at the Patent Analytics Reports landing page.',
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
              5 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Select::__set_state(array(
                 'fieldtype' => 'select',
                 'options' => 
                array (
                  0 => 
                  array (
                    'key' => 'Patent Analytics Reports',
                    'value' => 'Patent Analytics Reports',
                  ),
                ),
                 'width' => '',
                 'defaultValue' => '',
                 'optionsProviderClass' => '',
                 'optionsProviderData' => '',
                 'columnLength' => 190,
                 'dynamicOptions' => false,
                 'name' => 'resourceType',
                 'title' => 'Category',
                 'tooltip' => '',
                 'mandatory' => true,
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
             'icon' => '',
             'labelWidth' => 150,
             'labelAlign' => 'left',
          )),
          1 => 
          Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'fieldtype' => 'panel',
             'layout' => NULL,
             'border' => false,
             'name' => 'Media',
             'type' => NULL,
             'region' => NULL,
             'title' => 'Image',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'permissions' => NULL,
             'children' => 
            array (
              0 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Hotspotimage::__set_state(array(
                 'fieldtype' => 'hotspotimage',
                 'queryColumnType' => 
                array (
                  'image' => 'int(11)',
                  'hotspots' => 'text',
                ),
                 'columnType' => 
                array (
                  'image' => 'int(11)',
                  'hotspots' => 'text',
                ),
                 'ratioX' => NULL,
                 'ratioY' => NULL,
                 'predefinedDataTemplates' => '',
                 'name' => 'coverImage',
                 'title' => 'Cover',
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
                 'width' => 600,
                 'height' => '',
                 'uploadPath' => '',
              )),
              1 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Hotspotimage::__set_state(array(
                 'fieldtype' => 'hotspotimage',
                 'queryColumnType' => 
                array (
                  'image' => 'int(11)',
                  'hotspots' => 'text',
                ),
                 'columnType' => 
                array (
                  'image' => 'int(11)',
                  'hotspots' => 'text',
                ),
                 'ratioX' => NULL,
                 'ratioY' => NULL,
                 'predefinedDataTemplates' => '',
                 'name' => 'authorIcon',
                 'title' => 'Author',
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
                 'width' => 600,
                 'height' => '',
                 'uploadPath' => '',
              )),
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'icon' => '',
             'labelWidth' => 0,
             'labelAlign' => 'left',
          )),
          2 => 
          Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'fieldtype' => 'panel',
             'layout' => NULL,
             'border' => false,
             'name' => 'AlsoWorthWatching',
             'type' => NULL,
             'region' => NULL,
             'title' => 'Related Content',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'permissions' => NULL,
             'children' => 
            array (
              0 => 
              Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                 'fieldtype' => 'panel',
                 'layout' => NULL,
                 'border' => false,
                 'name' => 'interestedData',
                 'type' => NULL,
                 'region' => NULL,
                 'title' => 'Related Content',
                 'width' => 1200,
                 'height' => 270,
                 'collapsible' => false,
                 'collapsed' => false,
                 'bodyStyle' => '',
                 'datatype' => 'layout',
                 'permissions' => NULL,
                 'children' => 
                array (
                  0 => 
                  Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                     'fieldtype' => 'input',
                     'width' => 400,
                     'defaultValue' => NULL,
                     'columnLength' => 190,
                     'regex' => '',
                     'regexFlags' => 
                    array (
                    ),
                     'unique' => false,
                     'showCharCount' => false,
                     'name' => 'interestedTitle',
                     'title' => 'Title',
                     'tooltip' => 'If field is left empty, page will display the default title "Also Worth Watching/Reading"',
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
                  1 => 
                  Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation::__set_state(array(
                     'fieldtype' => 'manyToManyObjectRelation',
                     'width' => '',
                     'height' => '',
                     'maxItems' => NULL,
                     'relationType' => true,
                     'visibleFields' => 
                    array (
                    ),
                     'allowToCreateNewObject' => false,
                     'optimizedAdminLoading' => false,
                     'enableTextSelection' => false,
                     'visibleFieldDefinitions' => 
                    array (
                    ),
                     'classes' => 
                    array (
                    ),
                     'pathFormatterClass' => '',
                     'name' => 'interestedList',
                     'title' => 'Content',
                     'tooltip' => 'To select objects to be displayed at the right component "Also Worth Watching":

1. Click the Search icon and key in the page title or
2. Pull any objects under "Data Objects"',
                     'mandatory' => false,
                     'noteditable' => false,
                     'index' => false,
                     'locked' => false,
                     'style' => '',
                     'permissions' => NULL,
                     'datatype' => 'data',
                     'invisible' => false,
                     'visibleGridView' => false,
                     'visibleSearch' => true,
                     'blockedVarsForExport' => 
                    array (
                    ),
                  )),
                ),
                 'locked' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'icon' => '',
                 'labelWidth' => 100,
                 'labelAlign' => 'left',
              )),
              1 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation::__set_state(array(
                 'fieldtype' => 'manyToManyObjectRelation',
                 'width' => '',
                 'height' => '',
                 'maxItems' => NULL,
                 'relationType' => true,
                 'visibleFields' => 
                array (
                ),
                 'allowToCreateNewObject' => false,
                 'optimizedAdminLoading' => false,
                 'enableTextSelection' => false,
                 'visibleFieldDefinitions' => 
                array (
                ),
                 'classes' => 
                array (
                ),
                 'pathFormatterClass' => '',
                 'name' => 'shares',
                 'title' => 'Social Share',
                 'tooltip' => 'To display social share buttons on the page:

Go to Data Objects > CTABookmark > SocialShareButtons and drag all four objects (email, facebook, linkedin, twitter)',
                 'mandatory' => false,
                 'noteditable' => false,
                 'index' => false,
                 'locked' => false,
                 'style' => '',
                 'permissions' => NULL,
                 'datatype' => 'data',
                 'invisible' => false,
                 'visibleGridView' => false,
                 'visibleSearch' => false,
                 'blockedVarsForExport' => 
                array (
                ),
              )),
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'icon' => '',
             'labelWidth' => 0,
             'labelAlign' => 'left',
          )),
          3 => 
          Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'fieldtype' => 'panel',
             'layout' => NULL,
             'border' => false,
             'name' => 'Resources',
             'type' => NULL,
             'region' => NULL,
             'title' => 'CTA',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'permissions' => NULL,
             'children' => 
            array (
              0 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Link::__set_state(array(
                 'fieldtype' => 'link',
                 'name' => 'file',
                 'title' => 'Download',
                 'tooltip' => 'For the purple bottom CTA

1. Insert CTA Title at the "Text" field
2. Insert URL at the "Path" field',
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
              )),
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'icon' => '',
             'labelWidth' => 0,
             'labelAlign' => 'left',
          )),
          4 => 
          Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'fieldtype' => 'panel',
             'layout' => NULL,
             'border' => false,
             'name' => 'SEO & Setting',
             'type' => NULL,
             'region' => NULL,
             'title' => 'SEO & Setting',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'permissions' => NULL,
             'children' => 
            array (
              0 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                 'fieldtype' => 'input',
                 'width' => '',
                 'defaultValue' => NULL,
                 'columnLength' => 190,
                 'regex' => '',
                 'regexFlags' => 
                array (
                ),
                 'unique' => false,
                 'showCharCount' => false,
                 'name' => 'seoTitle',
                 'title' => 'Title',
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
              1 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Textarea::__set_state(array(
                 'fieldtype' => 'textarea',
                 'width' => '',
                 'height' => '',
                 'maxLength' => NULL,
                 'showCharCount' => false,
                 'excludeFromSearchIndex' => false,
                 'name' => 'seoDescription',
                 'title' => 'Description',
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
              )),
              2 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Multiselect::__set_state(array(
                 'fieldtype' => 'multiselect',
                 'options' => 
                array (
                  0 => 
                  array (
                    'key' => 'Sensor Technology',
                    'value' => 'Sensor Technology',
                  ),
                  1 => 
                  array (
                    'key' => 'Resources',
                    'value' => 'Resources',
                  ),
                  2 => 
                  array (
                    'key' => 'Patent Analytics Reports',
                    'value' => 'Patent Analytics Reports',
                  ),
                  3 => 
                  array (
                    'key' => 'Patent Analytics',
                    'value' => 'Patent Analytics',
                  ),
                  4 => 
                  array (
                    'key' => 'Human and Machine Collaboration',
                    'value' => 'Human and Machine Collaboration',
                  ),
                  5 => 
                  array (
                    'key' => 'Healthcare Artificial Intelligence',
                    'value' => 'Healthcare Artificial Intelligence',
                  ),
                  6 => 
                  array (
                    'key' => 'Healthcare',
                    'value' => 'Healthcare',
                  ),
                  7 => 
                  array (
                    'key' => 'Graphene Technologies',
                    'value' => 'Graphene Technologies',
                  ),
                  8 => 
                  array (
                    'key' => 'Energy-Efficient Desalination Technologies',
                    'value' => 'Energy-Efficient Desalination Technologies',
                  ),
                  9 => 
                  array (
                    'key' => 'Energy',
                    'value' => 'Energy',
                  ),
                  10 => 
                  array (
                    'key' => 'Cloud Technology',
                    'value' => 'Cloud Technology',
                  ),
                  11 => 
                  array (
                    'key' => 'Cloud Security',
                    'value' => 'Cloud Security',
                  ),
                  12 => 
                  array (
                    'key' => 'Carbon',
                    'value' => 'Carbon',
                  ),
                  13 => 
                  array (
                    'key' => 'Autonomous Vehicles',
                    'value' => 'Autonomous Vehicles',
                  ),
                  14 => 
                  array (
                    'key' => 'Artificial Intelligence',
                    'value' => 'Artificial Intelligence',
                  ),
                  15 => 
                  array (
                    'key' => '3D Printing in Building and Construction',
                    'value' => '3D Printing in Building and Construction',
                  ),
                  16 => 
                  array (
                    'key' => '3D Printing',
                    'value' => '3D Printing',
                  ),
                  17 => 
                  array (
                    'key' => 'Neurotechnology',
                    'value' => 'Neurotechnology',
                  ),
                  18 => 
                  array (
                    'key' => 'Neuroscience',
                    'value' => 'Neuroscience',
                  ),
                  19 => 
                  array (
                    'key' => 'Technologies',
                    'value' => 'Technologies',
                  ),
                ),
                 'width' => 1000,
                 'height' => 200,
                 'maxItems' => NULL,
                 'renderType' => 'tags',
                 'optionsProviderClass' => '',
                 'optionsProviderData' => '',
                 'dynamicOptions' => false,
                 'name' => 'tags',
                 'title' => 'Keywords',
                 'tooltip' => 'Select one or more keywords from the drop-down list.',
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
                 'visibleSearch' => true,
                 'blockedVarsForExport' => 
                array (
                ),
              )),
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'icon' => '',
             'labelWidth' => 0,
             'labelAlign' => 'left',
          )),
          5 => 
          Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'fieldtype' => 'panel',
             'layout' => NULL,
             'border' => false,
             'name' => 'Video',
             'type' => NULL,
             'region' => NULL,
             'title' => 'Video',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'permissions' => NULL,
             'children' => 
            array (
              0 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                 'fieldtype' => 'input',
                 'width' => '',
                 'defaultValue' => NULL,
                 'columnLength' => 190,
                 'regex' => '',
                 'regexFlags' => 
                array (
                ),
                 'unique' => false,
                 'showCharCount' => false,
                 'name' => 'videoTime',
                 'title' => 'Duration',
                 'tooltip' => 'Manually key in the video duration to display at the bottom right of the video.

Format: 00h 00m 00s',
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
              1 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Video::__set_state(array(
                 'fieldtype' => 'video',
                 'width' => 800,
                 'height' => 600,
                 'allowedTypes' => NULL,
                 'supportedTypes' => 
                array (
                  0 => 'asset',
                  1 => 'youtube',
                  2 => 'vimeo',
                  3 => 'dailymotion',
                ),
                 'name' => 'detailVideo',
                 'title' => 'URL',
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
              )),
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'icon' => '',
             'labelWidth' => 200,
             'labelAlign' => 'left',
          )),
        ),
         'locked' => false,
         'blockedVarsForExport' => 
        array (
        ),
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
   'icon' => '',
   'previewUrl' => '',
   'group' => 'Resources',
   'showAppLoggerTab' => false,
   'linkGeneratorReference' => '',
   'previewGeneratorReference' => '',
   'compositeIndices' => 
  array (
  ),
   'generateTypeDeclarations' => true,
   'showFieldLookup' => false,
   'propertyVisibility' => 
  array (
    'grid' => 
    array (
      'id' => true,
      'key' => false,
      'path' => true,
      'published' => true,
      'modificationDate' => true,
      'creationDate' => true,
    ),
    'search' => 
    array (
      'id' => true,
      'key' => false,
      'path' => true,
      'published' => true,
      'modificationDate' => true,
      'creationDate' => true,
    ),
  ),
   'enableGridLocking' => false,
   'deletedDataComponents' => 
  array (
  ),
   'dao' => NULL,
   'blockedVarsForExport' => 
  array (
  ),
   'activeDispatchingEvents' => 
  array (
  ),
));
