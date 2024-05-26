/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


pimcore.registerNS("pimcore.plugin.asset_metadata_class_definitions.bundle.configurationItem");
pimcore.plugin.asset_metadata_class_definitions.bundle.configurationItem = Class.create({

    allowedInType: 'asset',

    initialize: function (data, parentPanel, reopen, editorPrefix) {
        this.parentPanel = parentPanel;
        this.data = data;
        this.editorPrefix = editorPrefix;
        this.reopen = reopen;

        this.addTree();
        this.initLayoutFields();
        this.addLayout();
    },


    getRootPanel: function () {
        var iconStore = new Ext.data.ArrayStore({
            proxy: {
                url: '/admin/class/get-icons',
                type: 'ajax',
                reader: {
                    type: 'json'
                },
                extraParams: {
                    classId: this.getId()
                }
            },
            fields: ["text", "value"]
        });


        var iconFieldId = Ext.id();
        var iconField = new Ext.form.field.Text({
            id: iconFieldId,
            name: "icon",
            width: 396,
            value: this.data.icon,
            listeners: {
                "afterrender": function (el) {
                    el.inputEl.applyStyles("background:url(" + el.getValue() + ") right center no-repeat;");
                }
            }
        });

        this.rootPanel = new Ext.form.FormPanel({
            title: '<b>' + t("general_settings") + '</b>',
            bodyStyle: 'padding: 10px; border-top: 1px solid #606060 !important;',
            defaults: {
                labelWidth: 200
            },
            items: [
                {
                    xtype: "textfield",
                    readOnly: true,
                    fieldLabel: t("name"),
                    name: "name",
                    width: 500,
                    value: this.data.name
                },
                {
                    xtype: "textfield",
                    width: 600,
                    name: "prefix",
                    fieldLabel: t("prefix"),
                    value: this.data.prefix
                },
                {
                    xtype: "textfield",
                    width: 600,
                    name: "title",
                    fieldLabel: t("title"),
                    value: this.data.title
                },
                {
                    xtype: "fieldcontainer",
                    layout: "hbox",
                    fieldLabel: t("icon"),
                    defaults: {
                        labelWidth: 200
                    },
                    items: [
                        iconField,
                        {
                            xtype: "combobox",
                            store: iconStore,
                            width: 50,
                            valueField: 'value',
                            displayField: 'text',
                            listeners: {
                                select: function (ele, rec, idx) {
                                    var icon = ele.container.down("#" + iconFieldId);
                                    var newValue = rec.data.value;
                                    icon.component.setValue(newValue);
                                    icon.component.inputEl.applyStyles("background:url(" + newValue + ") right center no-repeat;");
                                    return newValue;
                                }.bind(this)
                            }
                        },
                        {
                            iconCls: "pimcore_icon_refresh",
                            xtype: "button",
                            tooltip: t("refresh"),
                            handler: function(iconField) {
                                iconField.inputEl.applyStyles("background:url(" + iconField.getValue() + ") right center no-repeat;");
                            }.bind(this, iconField)
                        },
                        {
                            xtype: "button",
                            iconCls: "pimcore_icon_icons",
                            text: t('icon_library'),
                            handler: function () {
                                pimcore.helpers.openGenericIframeWindow("icon-library", "/admin/misc/icon-list", "pimcore_icon_icons", t("icon_library"));
                            }
                        }
                    ]
                },
            ]
        });

        return this.rootPanel;
    },


    saveOnComplete: function (response) {
        try {
            var res = Ext.decode(response.responseText);
            if (res.success) {
                pimcore.helpers.showNotification(t("success"), t("saved_successfully"), "success");
            } else {
                if (res.message) {
                    pimcore.helpers.showNotification(t("error"), res.message, "error");
                } else {
                    throw "save was not successful, see log files in /var/logs";
                }
            }
        } catch (e) {
            this.saveOnError();
        }
    },

    addTree: function () {
        this.tree = Ext.create('Ext.tree.Panel', {
            region: "west",
            width: 300,
            split: true,
            enableDD: true,
            autoScroll: true,
            root: {
                id: "0",
                root: true,
                text: t("general_settings"),
                leaf: true,
                iconCls: "pimcore_icon_class",
                isTarget: true
            },
            listeners: this.getTreeNodeListeners(),
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    ddGroup: "element"
                }
            }
        });
    },

    addLayout: function () {

        this.editpanel = new Ext.Panel({
            region: "center",
            bodyStyle: "padding: 10px;",
            autoScroll: true
        });

        var panelButtons = [];

        panelButtons.push({
            text: t('reload_definition'),
            handler: this.onRefresh.bind(this),
            iconCls: "pimcore_icon_reload"
        });


        panelButtons.push({
            text: t("save"),
            iconCls: "pimcore_icon_apply",
            handler: this.save.bind(this)
        });


        this.panel = new Ext.Panel({
            border: false,
            layout: "border",
            closable: true,
            title: this.data.name + " ( ID: " + this.data.name + ")",
            id: this.editorPrefix + this.getId(),
            items: [
                this.tree,
                this.editpanel
            ],
            buttons: panelButtons
        });


        this.parentPanel.getEditPanel().add(this.panel);

        this.editpanel.add(this.getRootPanel());
        this.setCurrentNode("root");
        this.parentPanel.getEditPanel().setActiveTab(this.panel);

        pimcore.layout.refresh();
    },


    getId: function () {
        return this.data.name;
    },


    initLayoutFields: function () {

        if (this.data.layoutDefinitions) {
            if (this.data.layoutDefinitions.childs) {
                for (var i = 0; i < this.data.layoutDefinitions.childs.length; i++) {
                    this.tree.getRootNode().appendChild(this.recursiveAddNode(this.data.layoutDefinitions.childs[i],
                        this.tree.getRootNode()));
                }
                this.tree.getRootNode().expand();
            }
        }
    },

    recursiveAddNode: function (con, scope) {

        var fn = null;
        var newNode = null;

        if (con.datatype == "layout") {
            fn = this.addLayoutChild.bind(scope, con.fieldtype, con, this.context);
        } else if (con.datatype == "data") {
            fn = this.addDataChild.bind(scope, con.fieldtype, con, this.context);
        }

        newNode = fn();

        if (con.childs) {
            for (var i = 0; i < con.childs.length; i++) {
                this.recursiveAddNode(con.childs[i], newNode);
            }
        }

        return newNode;
    },


    getTreeNodeListeners: function () {

        var listeners = {
            "itemclick": this.onTreeNodeClick.bind(this),
            "itemcontextmenu": this.onTreeNodeContextmenu.bind(this)
        };
        return listeners;
    },


    onTreeNodeClick: function (tree, record, item, index, e, eOpts) {

        try {
            this.saveCurrentNode();
        } catch (e) {
            console.log(e);
        }


        try {
            this.editpanel.removeAll();

            if (record.data.editor) {
                this.editpanel.add(record.data.editor.getLayout());
                this.setCurrentNode(record.data.editor);
            }

            if (record.data.root) {
                this.editpanel.add(this.getRootPanel());
                this.setCurrentNode("root");
            }

            this.editpanel.updateLayout();
        } catch (e) {
            console.log(e);
        }
    },

    getDataMenu: function (tree, record, allowedTypes, parentType, editMode) {
        // get available data types
        var dataMenu = [];
        var dataComps = Object.keys(pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data);

        var parentRestrictions;
        var groups = [];

        var groupNames = ["text_and_number","date","select","relation","structured","other"];

        for (var i = 0; i < dataComps.length; i++) {
            var dataCompName = dataComps[i];
            var dataComp = pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[dataCompName];

            // check for disallowed types
            var allowed = false;

            if('object' !== typeof dataComp) {
                if (dataComp.prototype.allowIn[this.allowedInType]) {
                    allowed = true;
                }
            }

            if (!allowed) {
                continue;
            }


            if (dataComps[i] != "data") { // class data is an abstract class => disallow
                if (in_array("data", allowedTypes[parentType]) || in_array(dataComps[i], allowedTypes[parentType]) ) {

                    // check for restrictions from a parent field (eg. localized fields)
                    if(in_array("data", allowedTypes[parentType])) {
                        parentRestrictions = this.getRestrictionsFromParent(record);
                        if(parentRestrictions != null) {
                            if(!in_array(dataComps[i], allowedTypes[parentRestrictions])) {
                                continue;
                            }
                        }
                    }

                    var group = pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[dataComps[i]].prototype.getGroup();
                    if (group) {
                        if (!groups[group]) {
                            if (!in_array(group, groupNames)) {
                                groupNames.push(group);
                            }
                            groups[group] = [];
                        }
                    }
                    var handler;
                    if (editMode) {
                        handler = this.changeDataType.bind(this, tree, record, dataComps[i], true, this.context);
                    } else {
                        handler = this.addNewDataChild.bind(this, record, dataComps[i], this.context);
                    }

                    if (group) {
                        groups[group].push({
                            text: pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[dataComps[i]].prototype.getTypeName(),
                            iconCls: pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[dataComps[i]].prototype.getIconClass(),
                            handler: handler
                        });
                    } else {
                        dataMenu.push({
                            text: pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[dataComps[i]].prototype.getTypeName(),
                            iconCls: pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[dataComps[i]].prototype.getIconClass(),
                            handler: handler
                        });
                    }

                }
            }
        }

        for (i = 0; i < groupNames.length; i++) {
            if (groups[groupNames[i]] && groups[groupNames[i]].length > 0) {
                dataMenu.push(new Ext.menu.Item({
                    text: t(groupNames[i]),
                    iconCls: "pimcore_icon_data_group_" + groupNames[i],
                    hideOnClick: false,
                    menu: groups[groupNames[i]]
                }));
            }
        }
        return dataMenu;
    },


    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts) {
        e.stopEvent();
        tree.select();

        var menu = new Ext.menu.Menu();

        var allowedTypes = pimcore.object.helpers.layout.getAllowedTypes(this);

        var dataComps = Object.keys(pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data);

        for (let i = 0; i < dataComps.length; i++) {
            var dataCompName = dataComps[i];
            if ('object' === typeof pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[dataCompName]) {
                continue;
            }
            var component = pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[dataCompName];
            if (component.prototype.allowIn['localizedfield']) {
                allowedTypes.localizedfields.push(dataCompName);
            }
        }


        // the child-type "data" is a placehoder for all data components

        var parentType = "root";

        if (record.data.editor) {
            parentType = record.data.editor.type;
        }

        var changeTypeAllowed = false;
        if (record.data.type == "data") {
            changeTypeAllowed = true;
        }

        var childsAllowed = false;
        if (allowedTypes[parentType] && allowedTypes[parentType].length > 0) {
            childsAllowed = true;
        }

        if (childsAllowed || changeTypeAllowed) {
            // get available layouts
            var layoutMenu = [];
            var layouts = Object.keys(pimcore.plugin.asset_metadata_class_definitions.bundle.classes.layout);

            for (var i = 0; i < layouts.length; i++) {
                if (layouts[i] != "layout") {
                    if (in_array(layouts[i], allowedTypes[parentType])) {
                        layoutMenu.push({
                            text: pimcore.plugin.asset_metadata_class_definitions.bundle.classes.layout[layouts[i]].prototype.getTypeName(),
                            iconCls: pimcore.plugin.asset_metadata_class_definitions.bundle.classes.layout[layouts[i]].prototype.getIconClass(),
                            handler: function (record, type, context) {
                                var newNode = this.addLayoutChild.bind(record, type, null, context)();
                                newNode.getOwnerTree().getSelectionModel().select(newNode);
                                this.onTreeNodeClick(null, newNode);
                            }.bind(this, record, layouts[i], this.context)
                        });
                    }
                }
            }

            var getDataMenu = this.getDataMenu.bind(this, tree, record);
            var addDataMenu = getDataMenu(allowedTypes, parentType, false);

            if (layoutMenu.length > 0) {
                menu.add(new Ext.menu.Item({
                    text: t('add_layout_component'),
                    iconCls: "pimcore_icon_add",
                    hideOnClick: false,
                    menu: layoutMenu
                }));
            }

            if (addDataMenu.length > 0) {
                menu.add(new Ext.menu.Item({
                    text: t('add_data_component'),
                    iconCls: "pimcore_icon_add",
                    hideOnClick: false,
                    menu: addDataMenu
                }));
            }

            if (changeTypeAllowed) {
                var changeDataMenu = getDataMenu(allowedTypes, record.parentNode.data.editor.type, true);
                menu.add(new Ext.menu.Item({
                    text: t('convert_to'),
                    iconCls: "pimcore_icon_convert",
                    hideOnClick: false,
                    menu: changeDataMenu
                }));
            }

            if (record.data.type == "data") {
                menu.add(new Ext.menu.Item({
                    text: t('clone'),
                    iconCls: "pimcore_icon_clone",
                    hideOnClick: true,
                    handler: this.changeDataType.bind(this, tree, record, record.data.editor.type, false, this.context)
                }));
            }

            menu.add(new Ext.menu.Item({
                text: t('copy'),
                iconCls: "pimcore_icon_copy",
                hideOnClick: true,
                handler: this.copyNode.bind(this, tree, record)
            }));

            if (childsAllowed) {
                if (pimcore && pimcore.classEditor && pimcore.classEditor.clipboard) {
                    menu.add(new Ext.menu.Item({
                        text: t('paste'),
                        iconCls: "pimcore_icon_paste",
                        hideOnClick: true,
                        handler: this.dropNode.bind(this, tree, record)
                    }));
                }
            }
        }

        if (this.id != 0) {
            menu.add(new Ext.menu.Item({
                text: t('delete'),
                iconCls: "pimcore_icon_delete",
                handler: this.removeChild.bind(this, tree, record)
            }));
        }

        menu.showAt(e.pageX, e.pageY);
    },

    cloneNode: function (tree, node) {
        var theReference = this;
        var nodeLabel = node.data.text;
        var nodeType = node.data.type;

        var config = {
            text: nodeLabel,
            type: nodeType,
            leaf: node.data.leaf,
            expanded: node.data.expanded
        };


        config.listeners = theReference.getTreeNodeListeners();

        if (node.data.editor) {
            config.iconCls = node.data.editor.getIconClass();
        }

        var newNode = node.createNode(config);

        var theData = {};

        if (node.data.editor) {
            theData = Ext.apply(theData, node.data.editor.datax);
        }

        if (node.data.editor) {
            var definitions = newNode.data.editor = pimcore.plugin.asset_metadata_class_definitions.bundle[nodeType];
            var editorType = node.data.editor.type;
            var editor = definitions[editorType];

            newNode.data.editor = new editor(newNode, theData);
        }

        if (nodeType == "data") {
            var availableFields = newNode.data.editor.availableSettingsFields;
            for (var i = 0; i < availableFields.length; i++) {
                var field = availableFields[i];
                if (node.data.editor.datax[field]) {
                    if (field != "name") {
                        newNode.data.editor.datax[field] = node.data.editor.datax[field];
                    }
                }
            }

            newNode.data.editor.applySpecialData(node.data.editor);
        }


        var len = node.childNodes ? node.childNodes.length : 0;

        var i = 0;

        // Move child nodes across to the copy if required
        for (i = 0; i < len; i++) {
            var childNode = node.childNodes[i];
            var clonedChildNode = this.cloneNode(tree, childNode);

            newNode.appendChild(clonedChildNode);
        }
        return newNode;
    },


    copyNode: function (tree, record) {
        if (!pimcore.classEditor) {
            pimcore.classEditor = {};
        }

        var newNode = this.cloneNode(tree, record);
        pimcore.classEditor.clipboard = newNode;

    },

    dropNode: function (tree, record) {
        var node = pimcore.classEditor.clipboard;
        var newNode = this.cloneNode(tree, node);

        record.appendChild(newNode);
        tree.updateLayout();
    },


    setCurrentNode: function (cn) {
        this.currentNode = cn;
    },

    saveCurrentNode: function () {
        if (this.currentNode) {
            if (this.currentNode != "root") {
                this.currentNode.applyData();
            } else {
                // save root node data
                var items = this.rootPanel.queryBy(function (item) {
                    return true;
                });

                for (let i = 0; i < items.length; i++) {
                    let item = items[i];
                    if (typeof item.getValue == "function") {
                        this.data[item.name] = item.getValue();
                    }
                }
            }
        }
    },

    addLayoutChild: function (type, initData, context) {

        var nodeLabel = t(type);

        if (initData) {
            if (initData.name) {
                nodeLabel = initData.name;
            }
        }

        var newNode = {
            text: nodeLabel,
            type: "layout",
            iconCls: pimcore.plugin.asset_metadata_class_definitions.bundle.classes.layout[type].prototype.getIconClass(),
            leaf: false,
            expandable: false,
            expanded: true
        };
        newNode = this.appendChild(newNode);

        //to hide or show the expanding icon depending if childs are available or not
        newNode.addListener('remove', function (node, removedNode, isMove) {
            if (!node.hasChildNodes()) {
                node.set('expandable', false);
            }
        });
        newNode.addListener('append', function (node) {
            node.set('expandable', true);
        });


        var editor = new pimcore.plugin.asset_metadata_class_definitions.bundle.classes.layout[type](newNode, initData);
        newNode.set("editor", editor);

        this.expand();

        return newNode;
    },

    addNewDataChild: function (record, type, context) {
        var node = this.addDataChild.bind(record, type, {}, context)();
        node.getOwnerTree().getSelectionModel().select(node);
        this.onTreeNodeClick(null, node);

        var result = this.editpanel.query('field[name=name]');
        if (result.length && typeof result[0]['focus'] == 'function') {
            result[0].focus();
        }
    },

    addDataChild: function (type, initData, context) {

        var nodeLabel = '';

        initData = initData || {};

        if (type == "localizedfields") {
            initData.name = "localizedfields";
        }

        if (initData.name) {
            nodeLabel = initData.name;
        }


        var newNode = {
            text: nodeLabel,
            type: "data",
            leaf: true,
            iconCls: pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[type].prototype.getIconClass()
        };

        if (type == "localizedfields") {
            newNode.leaf = false;
            newNode.expanded = true;
            newNode.expandable = false;
            newNode.text = "localizedfields";
        }

        newNode = this.appendChild(newNode);

        var editor = new pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[type](newNode, initData);
        newNode.set("editor", editor);

        this.expand();

        return newNode;
    },

    changeDataType: function (tree, record, type, removeExisting, context) {
        try {
            this.saveCurrentNode();

            var nodeLabel = record.data.text;

            var theData = {};

            theData.name = nodeLabel;
            theData.datatype = "data";
            theData.fieldtype = type;

            if (!removeExisting) {
                var matches = nodeLabel.match(/\d+$/);

                if (matches) {
                    var number = matches[0];

                    var numberLength = number.length;
                    number = parseInt(number);
                    number = number + 1;

                    var l = nodeLabel.length;

                    nodeLabel = nodeLabel.substring(0, l - numberLength);
                } else {
                    number = 1;
                }
                nodeLabel = nodeLabel + number;
            }


            var parentNode = record.parentNode;

            var newNode = {
                text: nodeLabel,
                type: "data",
                leaf: true,
                iconCls: pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[type].prototype.getIconClass()
            };

            newNode = parentNode.createNode(newNode);

            if (!removeExisting) {
                theData.name = nodeLabel;
            }

            var editor = new pimcore.plugin.asset_metadata_class_definitions.bundle.classes.data[type](newNode, theData);
            newNode = record.parentNode.insertBefore(newNode, record);

            var availableFields = editor.availableSettingsFields;
            for (var i = 0; i < availableFields.length; i++) {
                var field = availableFields[i];
                if (record.data.editor.datax[field]) {
                    if (field != "name") {
                        editor.datax[field] = record.data.editor.datax[field];
                    }
                }
            }

            newNode.data.editor = editor;
            newNode.data.editor.applySpecialData(record.data.editor);


            if (removeExisting) {
                parentNode.removeChild(record);

            } else {
                parentNode.insertBefore(record, newNode);
            }

            //newNode.select();
            var f = this.onTreeNodeClick.bind(this, newNode.getOwnerTree(), newNode);
            f();

            var ownerTree = newNode.getOwnerTree();
            var selModel = ownerTree.getSelectionModel();
            selModel.select(newNode);


            return newNode;
        } catch (e) {
            console.log(e);
        }
    },


    removeChild: function (tree, record) {
        if (this.id != 0) {
            if (this.currentNode == record.data.editor) {
                this.currentNode = null;
                var rootNode = this.tree.getRootNode();
                var f = this.onTreeNodeClick.bind(this, this.tree, rootNode);
                f();
            }
            record.remove();
        }
    },

    getNodeData: function (node) {

        var data = {};

        if (node.data.editor) {
            if (typeof node.data.editor.getData == "function") {
                data = node.data.editor.getData();

                data.name = trim(data.name);

                // field specific validation
                var fieldValidation = true;
                if (typeof node.data.editor.isValid == "function") {
                    fieldValidation = node.data.editor.isValid();
                }

                var view = this.tree.getView();
                // check if the name is unique, localizedfields can be used more than once
                var nodeEl = Ext.fly(view.getNodeByRecord(node));

                var containerAwareDataName = data.name;

                if ((fieldValidation && in_arrayi(containerAwareDataName, this.usedFieldNames) == false)
                    || data.name == "localizedfields" && data.fieldtype == "localizedfields") {

                    if (data.datatype == "data") {
                        this.usedFieldNames.push(containerAwareDataName);
                    }

                    if (nodeEl) {
                        nodeEl.removeCls("tree_node_error");
                    }
                } else {
                    if (nodeEl) {
                        nodeEl.addCls("tree_node_error");
                    }

                    var invalidFieldsText = t("class_field_name_error") + ": '" + data.name + "'";

                    if (node.data.editor.invalidFieldNames) {
                        invalidFieldsText = t("reserved_field_names_error")
                            + (implode(',', node.data.editor.forbiddenNames));
                    }

                    pimcore.helpers.showNotification(t("error"), t("some_fields_cannot_be_saved"), "error",
                        invalidFieldsText);

                    this.getDataSuccess = false;
                    return false;
                }
            }
        }

        data.childs = null;
        if (node.childNodes.length > 0) {
            data.childs = [];

            for (var i = 0; i < node.childNodes.length; i++) {
                data.childs.push(this.getNodeData(node.childNodes[i]));
            }
        }

        return data;
    },

    getData: function () {

        this.getDataSuccess = true;

        this.usedFieldNames = [];

        var rootNode = this.tree.getRootNode();
        var nodeData = this.getNodeData(rootNode);

        return nodeData;
    },

    save: function () {

        this.saveCurrentNode();

        delete this.data.layoutDefinitions;

        var m = Ext.encode(this.getData());
        var n = Ext.encode(this.data);

        if (this.getDataSuccess) {
            Ext.Ajax.request({
                url: "/admin/asset-metadata-classdefinitions-bundle/backend/configuration-update",
                method: "PUT",
                params: {
                    configuration: m,
                    values: n,
                    name: this.data.name
                },
                success: this.saveOnComplete.bind(this),
                failure: this.saveOnError.bind(this)
            });
        }
    },

    saveOnError: function () {
        pimcore.helpers.showNotification(t("error"), t("saving_failed"), "error");
    },

    onRefresh: function () {
        this.parentPanel.getEditPanel().remove(this.panel);
        this.reopen();
    },

    getRestrictionsFromParent: function (node) {
        if (node.data.editor.type == "localizedfields") {
            return "localizedfields";
        } else {
            if (node.parentNode && node.parentNode.getDepth() > 0) {
                var parentType = this.getRestrictionsFromParent(node.parentNode);
                if (parentType != null) {
                    return parentType;
                }
            }
        }

        return null;
    }

});
