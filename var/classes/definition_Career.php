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
 * - coverImage [hotspotimage]
 * - authorIcon [hotspotimage]
 * - videoTime [input]
 * - detailVideo [video]
 * - moreContent [link]
 * - file [link]
 * - guideTitle [input]
 * - fullGuide [link]
 * - chineseGuide [link]
 * - interestedTitle [input]
 * - alsoList [manyToManyObjectRelation]
 * - IndustryTitle [input]
 * - checkIndustry [manyToManyObjectRelation]
 * - shares [manyToManyObjectRelation]
 * - tags [multiselect]
 */

return Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
   'id' => '23',
   'name' => 'Career',
   'description' => '',
   'creationDate' => 0,
   'modificationDate' => 1716911154,
   'userOwner' => 59,
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
         'title' => '',
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
                 'width' => 1200,
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
             'labelWidth' => 200,
             'labelAlign' => 'left',
          )),
          2 => 
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
                 'name' => 'moreContent',
                 'title' => 'Want More Great Content',
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
              )),
              1 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\Link::__set_state(array(
                 'fieldtype' => 'link',
                 'name' => 'file',
                 'title' => 'Preview of the Guide',
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
              )),
              2 => 
              Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                 'fieldtype' => 'panel',
                 'layout' => NULL,
                 'border' => false,
                 'name' => 'guideData',
                 'type' => NULL,
                 'region' => NULL,
                 'title' => '',
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
                     'name' => 'guideTitle',
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
                  Pimcore\Model\DataObject\ClassDefinition\Data\Link::__set_state(array(
                     'fieldtype' => 'link',
                     'name' => 'fullGuide',
                     'title' => 'English Version',
                     'tooltip' => 'For the orange top CTA

1. "Text" field: Insert full text such as "Download English Version"
2. "Path" field: Insert URL',
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
                  Pimcore\Model\DataObject\ClassDefinition\Data\Link::__set_state(array(
                     'fieldtype' => 'link',
                     'name' => 'chineseGuide',
                     'title' => 'Chinese Version',
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
             'icon' => '',
             'labelWidth' => 200,
             'labelAlign' => 'left',
          )),
          4 => 
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
                  Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation::__set_state(array(
                     'fieldtype' => 'manyToManyObjectRelation',
                     'width' => '',
                     'height' => '',
                     'maxItems' => NULL,
                     'relationType' => true,
                     'visibleFields' => 'id,title,classname',
                     'allowToCreateNewObject' => false,
                     'optimizedAdminLoading' => false,
                     'enableTextSelection' => false,
                     'visibleFieldDefinitions' => 
                    array (
                    ),
                     'classes' => 
                    array (
                      0 => 
                      array (
                        'classes' => 'Career',
                      ),
                    ),
                     'pathFormatterClass' => '',
                     'name' => 'alsoList',
                     'title' => 'Content',
                     'tooltip' => 'To select objects to be displayed at the right component "Also Worth Watching/Reading":

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
                 'labelWidth' => 200,
                 'labelAlign' => 'left',
              )),
              1 => 
              Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
                 'fieldtype' => 'panel',
                 'layout' => '',
                 'border' => false,
                 'name' => 'IndustryData',
                 'type' => NULL,
                 'region' => NULL,
                 'title' => 'Find Out More',
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
                     'width' => 800,
                     'defaultValue' => NULL,
                     'columnLength' => 190,
                     'regex' => '',
                     'regexFlags' => 
                    array (
                    ),
                     'unique' => false,
                     'showCharCount' => true,
                     'name' => 'IndustryTitle',
                     'title' => 'Description',
                     'tooltip' => 'To insert short description / summary of the page to feature in the Find Out More CTA.',
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
                     'visibleFields' => 'id,title,fullpath,classname',
                     'allowToCreateNewObject' => false,
                     'optimizedAdminLoading' => false,
                     'enableTextSelection' => false,
                     'visibleFieldDefinitions' => 
                    array (
                    ),
                     'classes' => 
                    array (
                      0 => 
                      array (
                        'classes' => 'CaseStudy',
                      ),
                      1 => 
                      array (
                        'classes' => 'Course',
                      ),
                      2 => 
                      array (
                        'classes' => 'Events',
                      ),
                      3 => 
                      array (
                        'classes' => 'News',
                      ),
                      4 => 
                      array (
                        'classes' => 'Articles',
                      ),
                      5 => 
                      array (
                        'classes' => 'Business',
                      ),
                      6 => 
                      array (
                        'classes' => 'PatentAnalytic',
                      ),
                      7 => 
                      array (
                        'classes' => 'WebinarRecordings',
                      ),
                      8 => 
                      array (
                        'classes' => 'Career',
                      ),
                    ),
                     'pathFormatterClass' => '',
                     'name' => 'checkIndustry',
                     'title' => 'Content',
                     'tooltip' => 'Page to feature in the Find Out More CTA.',
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
                 'labelWidth' => 200,
                 'labelAlign' => 'left',
              )),
              2 => 
              Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation::__set_state(array(
                 'fieldtype' => 'manyToManyObjectRelation',
                 'width' => 1200,
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
                  0 => 
                  array (
                    'classes' => 'Shares',
                  ),
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
             'labelWidth' => 200,
             'labelAlign' => 'left',
          )),
          5 => 
          Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'fieldtype' => 'panel',
             'layout' => NULL,
             'border' => false,
             'name' => 'TagList',
             'type' => NULL,
             'region' => NULL,
             'title' => 'Keywords',
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
              Pimcore\Model\DataObject\ClassDefinition\Data\Multiselect::__set_state(array(
                 'fieldtype' => 'multiselect',
                 'options' => 
                array (
                  0 => 
                  array (
                    'key' => 'Business Guide in Action',
                    'value' => 'Business Guide in Action',
                  ),
                  1 => 
                  array (
                    'key' => 'Business Guides',
                    'value' => 'Business Guides',
                  ),
                  2 => 
                  array (
                    'key' => 'Resources',
                    'value' => 'Resources',
                  ),
                  3 => 
                  array (
                    'key' => 'IP Basics',
                    'value' => 'IP Basics',
                  ),
                  4 => 
                  array (
                    'key' => 'Collaboration',
                    'value' => 'Collaboration',
                  ),
                  5 => 
                  array (
                    'key' => 'IP Management',
                    'value' => 'IP Management',
                  ),
                  6 => 
                  array (
                    'key' => 'IP Enforcement',
                    'value' => 'IP Enforcement',
                  ),
                  7 => 
                  array (
                    'key' => 'IP Audits',
                    'value' => 'IP Audits',
                  ),
                  8 => 
                  array (
                    'key' => 'IP Intelligence',
                    'value' => 'IP Intelligence',
                  ),
                  9 => 
                  array (
                    'key' => 'Research & Development',
                    'value' => 'Research & Development',
                  ),
                  10 => 
                  array (
                    'key' => 'International IP',
                    'value' => 'International IP',
                  ),
                  11 => 
                  array (
                    'key' => 'Business Guide in Action: Episode 1 - Safeguarding Your Competitive Edge (Moodware)',
                    'value' => 'Business Guide in Action: Episode 1 - Safeguarding Your Competitive Edge (Moodware)',
                  ),
                  12 => 
                  array (
                    'key' => 'Safeguarding Your Competitive Edge',
                    'value' => 'Safeguarding Your Competitive Edge',
                  ),
                  13 => 
                  array (
                    'key' => 'Business Guide in Action: Episode 2 - Creating Business Assets From Your Ideas (Starstruck)',
                    'value' => 'Business Guide in Action: Episode 2 - Creating Business Assets From Your Ideas (Starstruck)',
                  ),
                  14 => 
                  array (
                    'key' => 'Creating Business Assets From Your Ideas',
                    'value' => 'Creating Business Assets From Your Ideas',
                  ),
                  15 => 
                  array (
                    'key' => 'Partnering For Commercial Advantage',
                    'value' => 'Partnering For Commercial Advantage',
                  ),
                  16 => 
                  array (
                    'key' => 'Business Guide in Action: Episode 4 - Going Global (Moodware)',
                    'value' => 'Business Guide in Action: Episode 4 - Going Global (Moodware)',
                  ),
                  17 => 
                  array (
                    'key' => 'Going Global',
                    'value' => 'Going Global',
                  ),
                  18 => 
                  array (
                    'key' => 'Business Guide in Action: Episode 5 - Planning For Success (Mountaineering)',
                    'value' => 'Business Guide in Action: Episode 5 - Planning For Success (Mountaineering)',
                  ),
                  19 => 
                  array (
                    'key' => 'Planning For Success',
                    'value' => 'Planning For Success',
                  ),
                  20 => 
                  array (
                    'key' => 'Business Guide in Action: Episode 6 - Upholding Your IP Rights (BB Bottle)',
                    'value' => 'Business Guide in Action: Episode 6 - Upholding Your IP Rights (BB Bottle)',
                  ),
                  21 => 
                  array (
                    'key' => 'Upholding Your IP Rights',
                    'value' => 'Upholding Your IP Rights',
                  ),
                  22 => 
                  array (
                    'key' => 'Business Guide in Action: Episode 7 - Making Money From Your IP (Da Vinci)',
                    'value' => 'Business Guide in Action: Episode 7 - Making Money From Your IP (Da Vinci)',
                  ),
                  23 => 
                  array (
                    'key' => 'Making Money From Your IP',
                    'value' => 'Making Money From Your IP',
                  ),
                  24 => 
                  array (
                    'key' => 'Building A Strong Brand',
                    'value' => 'Building A Strong Brand',
                  ),
                  25 => 
                  array (
                    'key' => 'Branding',
                    'value' => 'Branding',
                  ),
                  26 => 
                  array (
                    'key' => 'Uncovering Your Hidden Value',
                    'value' => 'Uncovering Your Hidden Value',
                  ),
                  27 => 
                  array (
                    'key' => 'IP Financing',
                    'value' => 'IP Financing',
                  ),
                  28 => 
                  array (
                    'key' => 'IP Monetisation',
                    'value' => 'IP Monetisation',
                  ),
                  29 => 
                  array (
                    'key' => 'IP Taxation',
                    'value' => 'IP Taxation',
                  ),
                  30 => 
                  array (
                    'key' => 'Business Guide in Action: Episode 3 - Partnering For Commercial Advantage Doughmino',
                    'value' => 'Business Guide in Action: Episode 3 - Partnering For Commercial Advantage Doughmino',
                  ),
                  31 => 
                  array (
                    'key' => 'Keeping Your IP Out Of Trouble',
                    'value' => 'Keeping Your IP Out Of Trouble',
                  ),
                  32 => 
                  array (
                    'key' => 'Risk Management',
                    'value' => 'Risk Management',
                  ),
                  33 => 
                  array (
                    'key' => 'Knowing Your Competition',
                    'value' => 'Knowing Your Competition',
                  ),
                  34 => 
                  array (
                    'key' => 'Making Best Use Of All Your Valuable Assets',
                    'value' => 'Making Best Use Of All Your Valuable Assets',
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
             'labelWidth' => 100,
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
   'group' => 'About',
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
