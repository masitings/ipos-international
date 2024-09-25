<?php

return [
    "User-Login-History" => [
        "name" => "User-Login-History",
        "sql" => "",
        "dataSourceConfig" => [
            [
                "sql" => "id,user_name,loginTime,logoutTime FROM users_login ORDER BY loginTime DESC",
                "from" => "",
                "where" => "",
                "groupby" => "",
                "type" => "sql"
            ]
        ],
        "columnConfiguration" => [
            [
                "name" => "id",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel235-1"
            ],
            [
                "name" => "user_name",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel235-2"
            ],
            [
                "name" => "loginTime",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel235-3"
            ],
            [
                "name" => "logoutTime",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel235-4"
            ]
        ],
        "niceName" => "Login-History",
        "group" => "",
        "groupIconClass" => "",
        "iconClass" => "pimcore_icon_workflow_action",
        "menuShortcut" => FALSE,
        "reportClass" => "",
        "chartType" => NULL,
        "pieColumn" => NULL,
        "pieLabelColumn" => NULL,
        "xAxis" => NULL,
        "yAxis" => [

        ],
        "modificationDate" => 1642669031,
        "creationDate" => 1642668941,
        "shareGlobally" => NULL,
        "sharedUserNames" => [

        ],
        "sharedRoleNames" => [

        ],
        "id" => "User-Login-History"
    ],
    "Contact-History" => [
        "name" => "Contact-History",
        "sql" => "",
        "dataSourceConfig" => [
            [
                "sql" => "firstName AS FirstName,lastName AS LastName,companyName AS Company,designationText AS Designation,companyUrl AS CompanyURL, industryText as Industry, phoneNumber AS Phone,email AS Email,receiveEmail AS ReceiveMarketingEmail,messageText AS Message,companyOverviewText as CompanyOverview, existingIaIpProfileText as ExistingIP, overseasExpansionText as OverseasExpansion, proprietaryTechnologyText as ProprietaryTechnology, source,sendTime FROM contact_history ORDER BY sendTime DESC",
                "from" => "",
                "where" => "",
                "groupby" => "",
                "type" => "sql"
            ]
        ],
        "columnConfiguration" => [
            [
                "name" => "sendTime",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel815-1"
            ],
            [
                "name" => "FirstName",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel815-2"
            ],
            [
                "name" => "LastName",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel815-3"
            ],
            [
                "name" => "Company",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel815-4"
            ],
            [
                "name" => "Designation",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel815-5"
            ],
            [
                "name" => "Phone",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel815-6"
            ],
            [
                "name" => "Email",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel815-7"
            ],
            [
                "name" => "ReceiveMarketingEmail",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel815-8"
            ],
            [
                "name" => "source",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel815-10"
            ],
            [
                "name" => "Message",
                "display" => TRUE,
                "export" => TRUE,
                "order" => TRUE,
                "width" => "",
                "label" => "",
                "id" => "extModel815-9"
            ]
        ],
        "niceName" => "Contact-History",
        "group" => "",
        "groupIconClass" => "",
        "iconClass" => "pimcore_icon_workflow_action",
        "menuShortcut" => FALSE,
        "reportClass" => "",
        "chartType" => NULL,
        "pieColumn" => NULL,
        "pieLabelColumn" => NULL,
        "xAxis" => NULL,
        "yAxis" => [

        ],
        "modificationDate" => 1679033196,
        "creationDate" => 1645780410,
        "shareGlobally" => FALSE,
        "sharedUserNames" => [

        ],
        "sharedRoleNames" => [

        ],
        "id" => "Contact-History"
    ]
];
