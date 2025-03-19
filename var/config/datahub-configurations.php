<?php

return [
    "folders" => [

    ],
    "list" => [
        "events" => [
            "general" => [
                "active" => TRUE,
                "type" => "graphql",
                "name" => "events",
                "description" => "",
                "sqlObjectCondition" => "",
                "modificationDate" => 1639475594,
                "path" => NULL
            ],
            "schema" => [
                "queryEntities" => [
                    "Events" => [
                        "id" => "Events",
                        "name" => "Events",
                        "columnConfig" => [
                            "columns" => [
                                [
                                    "attributes" => [
                                        "attribute" => "title",
                                        "label" => "title",
                                        "dataType" => "input",
                                        "layout" => [
                                            "fieldtype" => "input",
                                            "width" => "",
                                            "defaultValue" => NULL,
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "regex" => "",
                                            "unique" => FALSE,
                                            "showCharCount" => FALSE,
                                            "name" => "title",
                                            "title" => "title",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "eventType",
                                        "label" => "eventType",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "Webinar",
                                                    "value" => "Webinar"
                                                ],
                                                [
                                                    "key" => "Course",
                                                    "value" => "Course"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "eventType",
                                            "title" => "eventType",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "topic",
                                        "label" => "topic",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "Intellectual Property",
                                                    "value" => "Intellectual Property"
                                                ],
                                                [
                                                    "key" => " Public Agency",
                                                    "value" => " Public Agency"
                                                ],
                                                [
                                                    "key" => " Government Agency",
                                                    "value" => " Government Agency"
                                                ],
                                                [
                                                    "key" => " IP Management",
                                                    "value" => " IP Management"
                                                ],
                                                [
                                                    "key" => " Intangible Assets",
                                                    "value" => " Intangible Assets"
                                                ],
                                                [
                                                    "key" => " Commercialising Innovations",
                                                    "value" => " Commercialising Innovations"
                                                ],
                                                [
                                                    "key" => " Licensing",
                                                    "value" => " Licensing"
                                                ],
                                                [
                                                    "key" => "Business",
                                                    "value" => "Business"
                                                ],
                                                [
                                                    "key" => "Enterprise",
                                                    "value" => "Enterprise"
                                                ],
                                                [
                                                    "key" => "IP Fundamentals",
                                                    "value" => "IP Fundamentals"
                                                ],
                                                [
                                                    "key" => "Intellectual Property Law",
                                                    "value" => "Intellectual Property Law"
                                                ],
                                                [
                                                    "key" => "Monetisation",
                                                    "value" => "Monetisation"
                                                ],
                                                [
                                                    "key" => "Webinar",
                                                    "value" => "Webinar"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "topic",
                                            "title" => "topic",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "venue",
                                        "label" => "venue",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "Online Live Stream",
                                                    "value" => "Online Live Stream"
                                                ],
                                                [
                                                    "key" => "Online (Self Learning )",
                                                    "value" => "Online (Self Learning )"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "venue",
                                            "title" => "venue",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "cost",
                                        "label" => "cost",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "Free",
                                                    "value" => "Free"
                                                ],
                                                [
                                                    "key" => "Paid",
                                                    "value" => "Paid"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "cost",
                                            "title" => "cost",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "proficiency",
                                        "label" => "Proficiency",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "Beginner",
                                                    "value" => "Beginner"
                                                ],
                                                [
                                                    "key" => "Intermediate",
                                                    "value" => "Intermediate"
                                                ],
                                                [
                                                    "key" => "Professional",
                                                    "value" => "Professional"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "proficiency",
                                            "title" => "Proficiency",
                                            "tooltip" => "",
                                            "mandatory" => TRUE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "audience",
                                        "label" => "Audience",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "",
                                                    "value" => "All"
                                                ],
                                                [
                                                    "key" => "IP Professionals",
                                                    "value" => "IP Professionals"
                                                ],
                                                [
                                                    "key" => "Public Agencies/Officers",
                                                    "value" => "Public Agencies/Officers"
                                                ],
                                                [
                                                    "key" => "Enterprises/Individuals",
                                                    "value" => "Enterprises/Individuals"
                                                ],
                                                [
                                                    "key" => "Graduate Studies",
                                                    "value" => "Graduate Studies"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "audience",
                                            "title" => "Audience",
                                            "tooltip" => "",
                                            "mandatory" => TRUE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "planing",
                                        "label" => "planing",
                                        "dataType" => "fieldcollections",
                                        "layout" => [
                                            "fieldtype" => "fieldcollections",
                                            "allowedTypes" => [
                                                "ProgramPlanning"
                                            ],
                                            "lazyLoading" => TRUE,
                                            "maxItems" => "",
                                            "disallowAddRemove" => FALSE,
                                            "disallowReorder" => FALSE,
                                            "collapsed" => FALSE,
                                            "collapsible" => FALSE,
                                            "border" => FALSE,
                                            "name" => "planing",
                                            "title" => "planing",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "fullpath",
                                        "label" => "fullpath",
                                        "dataType" => "system",
                                        "layout" => [
                                            "title" => "fullpath",
                                            "name" => "fullpath",
                                            "datatype" => "data",
                                            "fieldtype" => "system"
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "coverImage",
                                        "label" => "coverImage",
                                        "dataType" => "image",
                                        "layout" => [
                                            "fieldtype" => "image",
                                            "queryColumnType" => "int(11)",
                                            "columnType" => "int(11)",
                                            "name" => "coverImage",
                                            "title" => "coverImage",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "width" => "",
                                            "height" => "",
                                            "uploadPath" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ]
                            ]
                        ]
                    ]
                ],
                "mutationEntities" => [

                ],
                "specialEntities" => [
                    "document" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "document_folder" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "asset" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "asset_folder" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "asset_listing" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "object_folder" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ]
                ]
            ],
            "security" => [
                "method" => "datahub_apikey",
                "apikey" => "af857163a5b2a05be63753beda3813a4",
                "skipPermissionCheck" => FALSE
            ],
            "workspaces" => [
                "asset" => [
                    [
                        "read" => TRUE,
                        "cpath" => "/Images/Events",
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE,
                        "id" => "extModel1394-1"
                    ],
                    [
                        "read" => TRUE,
                        "cpath" => "/Images/News",
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE,
                        "id" => "extModel1394-2"
                    ]
                ],
                "document" => [

                ],
                "object" => [
                    [
                        "read" => TRUE,
                        "cpath" => "/news/upcoming-events",
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE,
                        "id" => "extModel1273-1"
                    ]
                ]
            ]
        ],
        "test" => [
            "general" => [
                "active" => TRUE,
                "type" => "graphql",
                "name" => "test",
                "description" => "",
                "sqlObjectCondition" => "",
                "modificationDate" => 1633842198,
                "path" => NULL
            ],
            "schema" => [
                "queryEntities" => [
                    "Fctest" => [
                        "id" => "Fctest",
                        "name" => "Fctest",
                        "columnConfig" => [
                            "columns" => [
                                [
                                    "attributes" => [
                                        "attribute" => "startDate",
                                        "label" => "startDate",
                                        "dataType" => "date",
                                        "layout" => [
                                            "fieldtype" => "date",
                                            "queryColumnType" => "bigint(20)",
                                            "columnType" => "bigint(20)",
                                            "defaultValue" => NULL,
                                            "useCurrentDate" => FALSE,
                                            "name" => "startDate",
                                            "title" => "startDate",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ]
                            ]
                        ]
                    ]
                ],
                "mutationEntities" => [

                ],
                "specialEntities" => [
                    "document" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "document_folder" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "asset" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "asset_folder" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "asset_listing" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "object_folder" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ]
                ]
            ],
            "security" => [
                "method" => "datahub_apikey",
                "apikey" => "f17424d2a7c4b77bf06b9e14cadacab3",
                "skipPermissionCheck" => FALSE
            ],
            "workspaces" => [
                "asset" => [

                ],
                "document" => [

                ],
                "object" => [

                ]
            ]
        ],
        "academy" => [
            "general" => [
                "active" => TRUE,
                "type" => "graphql",
                "name" => "academy",
                "description" => "",
                "sqlObjectCondition" => "",
                "modificationDate" => 1644485006,
                "path" => NULL
            ],
            "schema" => [
                "queryEntities" => [
                    "Course" => [
                        "id" => "Course",
                        "name" => "Course",
                        "columnConfig" => [
                            "columns" => [
                                [
                                    "attributes" => [
                                        "attribute" => "id",
                                        "label" => "id",
                                        "dataType" => "system",
                                        "layout" => [
                                            "title" => "id",
                                            "name" => "id",
                                            "datatype" => "data",
                                            "fieldtype" => "system"
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "fullpath",
                                        "label" => "fullpath",
                                        "dataType" => "system",
                                        "layout" => [
                                            "title" => "fullpath",
                                            "name" => "fullpath",
                                            "datatype" => "data",
                                            "fieldtype" => "system"
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "title",
                                        "label" => "title",
                                        "dataType" => "input",
                                        "layout" => [
                                            "fieldtype" => "input",
                                            "width" => "",
                                            "defaultValue" => NULL,
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "regex" => "",
                                            "unique" => FALSE,
                                            "showCharCount" => FALSE,
                                            "name" => "title",
                                            "title" => "title",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "eventType",
                                        "label" => "eventType",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "Seminar",
                                                    "value" => "Seminar"
                                                ],
                                                [
                                                    "key" => "Courses",
                                                    "value" => "Courses"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "eventType",
                                            "title" => "eventType",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "level",
                                        "label" => "level",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "Beginner",
                                                    "value" => "Beginner"
                                                ],
                                                [
                                                    "key" => "Intermediate",
                                                    "value" => "Intermediate"
                                                ],
                                                [
                                                    "key" => "Professional",
                                                    "value" => "Professional"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "level",
                                            "title" => "level",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "topic",
                                        "label" => "topic",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "Brand",
                                                    "value" => "Brand"
                                                ],
                                                [
                                                    "key" => "Bussiness",
                                                    "value" => "Bussiness"
                                                ],
                                                [
                                                    "key" => "Certification",
                                                    "value" => "Certification"
                                                ],
                                                [
                                                    "key" => "Class",
                                                    "value" => "Class"
                                                ],
                                                [
                                                    "key" => "Copyright",
                                                    "value" => "Copyright"
                                                ],
                                                [
                                                    "key" => "Counterfeit",
                                                    "value" => "Counterfeit"
                                                ],
                                                [
                                                    "key" => "Creative",
                                                    "value" => "Creative"
                                                ],
                                                [
                                                    "key" => "Education",
                                                    "value" => "Education"
                                                ],
                                                [
                                                    "key" => "Enterprise",
                                                    "value" => "Enterprise"
                                                ],
                                                [
                                                    "key" => "Franchising",
                                                    "value" => "Franchising"
                                                ],
                                                [
                                                    "key" => "ICT",
                                                    "value" => "ICT"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "topic",
                                            "title" => "topic",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "academyType",
                                        "label" => "academyType",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "IP Professionals",
                                                    "value" => "IP Professionals"
                                                ],
                                                [
                                                    "key" => "Public Agencies / Officers",
                                                    "value" => "Public Agencies / Officers"
                                                ],
                                                [
                                                    "key" => "Enterprises / Individuals",
                                                    "value" => "Enterprises / Individuals"
                                                ],
                                                [
                                                    "key" => "Graduate Studies",
                                                    "value" => "Graduate Studies"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "academyType",
                                            "title" => "academyType",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "venue",
                                        "label" => "venue",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "Online Live Stream",
                                                    "value" => "Online Live Stream"
                                                ],
                                                [
                                                    "key" => "Online (Self Learning )",
                                                    "value" => "Online (Self Learning )"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "venue",
                                            "title" => "venue",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "learningType",
                                        "label" => "Class Type",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "In Person",
                                                    "value" => "In Person"
                                                ],
                                                [
                                                    "key" => "Online Self Learning",
                                                    "value" => "Online Self Learning"
                                                ],
                                                [
                                                    "key" => "Online Live-Stream",
                                                    "value" => "Online Live-Stream"
                                                ],
                                                [
                                                    "key" => "Online Self Learning + Online Live-Stream",
                                                    "value" => "Online Self Learning + Online Live-Stream"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "learningType",
                                            "title" => "Class Type",
                                            "tooltip" => "Select 1 class type only.",
                                            "mandatory" => TRUE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => TRUE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "fee",
                                        "label" => "Fee",
                                        "dataType" => "select",
                                        "layout" => [
                                            "fieldtype" => "select",
                                            "options" => [
                                                [
                                                    "key" => "Free",
                                                    "value" => "Free"
                                                ],
                                                [
                                                    "key" => "Paid",
                                                    "value" => "Paid"
                                                ]
                                            ],
                                            "width" => "",
                                            "defaultValue" => "",
                                            "optionsProviderClass" => "",
                                            "optionsProviderData" => "",
                                            "queryColumnType" => "varchar",
                                            "columnType" => "varchar",
                                            "columnLength" => 190,
                                            "dynamicOptions" => FALSE,
                                            "name" => "fee",
                                            "title" => "Fee",
                                            "tooltip" => "",
                                            "mandatory" => TRUE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "defaultValueGenerator" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "planing",
                                        "label" => "planing",
                                        "dataType" => "fieldcollections",
                                        "layout" => [
                                            "fieldtype" => "fieldcollections",
                                            "allowedTypes" => [

                                            ],
                                            "lazyLoading" => TRUE,
                                            "maxItems" => "",
                                            "disallowAddRemove" => FALSE,
                                            "disallowReorder" => FALSE,
                                            "collapsed" => FALSE,
                                            "collapsible" => FALSE,
                                            "border" => FALSE,
                                            "name" => "planing",
                                            "title" => "planing",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ],
                                [
                                    "attributes" => [
                                        "attribute" => "coverImage",
                                        "label" => "Landing Page Cover Image",
                                        "dataType" => "image",
                                        "layout" => [
                                            "fieldtype" => "image",
                                            "queryColumnType" => "int(11)",
                                            "columnType" => "int(11)",
                                            "name" => "coverImage",
                                            "title" => "Landing Page Cover Image",
                                            "tooltip" => "",
                                            "mandatory" => FALSE,
                                            "noteditable" => FALSE,
                                            "index" => FALSE,
                                            "locked" => FALSE,
                                            "style" => "",
                                            "permissions" => NULL,
                                            "datatype" => "data",
                                            "relationType" => FALSE,
                                            "invisible" => FALSE,
                                            "visibleGridView" => FALSE,
                                            "visibleSearch" => FALSE,
                                            "width" => "",
                                            "height" => "",
                                            "uploadPath" => ""
                                        ]
                                    ],
                                    "isOperator" => FALSE
                                ]
                            ]
                        ]
                    ]
                ],
                "mutationEntities" => [

                ],
                "specialEntities" => [
                    "document" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "document_folder" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "asset" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "asset_folder" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "asset_listing" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ],
                    "object_folder" => [
                        "read" => FALSE,
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE
                    ]
                ]
            ],
            "security" => [
                "method" => "datahub_apikey",
                "apikey" => "079eb73af99d72172ab6f349bbad36bb",
                "skipPermissionCheck" => FALSE
            ],
            "workspaces" => [
                "asset" => [
                    [
                        "read" => TRUE,
                        "cpath" => "/Images/Academy",
                        "create" => FALSE,
                        "update" => FALSE,
                        "delete" => FALSE,
                        "id" => "extModel6275-1"
                    ]
                ],
                "document" => [

                ],
                "object" => [
                    [
                        "read" => TRUE,
                        "cpath" => "/academy",
                        "create" => FALSE,
                        "update" => TRUE,
                        "delete" => FALSE,
                        "id" => "extModel18256-1"
                    ]
                ]
            ]
        ]
    ]
];
