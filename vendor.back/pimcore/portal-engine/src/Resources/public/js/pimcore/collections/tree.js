/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


Ext.define('collectionElementModel', {
    extend: 'Ext.data.TreeModel',
    idProperty: 'elementId',
    fields: ['id', 'elementId', 'text', 'key', 'path', 'leaf', 'expanded']
});

pimcore.registerNS('pimcore.plugin.PimcorePortalEngineBundle.collections.tree');
pimcore.plugin.PimcorePortalEngineBundle.collections.tree = Class.create(pimcore.object.tree, {

    treeDataUrl: Routing.generate('pimcore_portalengine_admin_collection_tree_data'),

    initialize: function(config) {
        this.perspectiveCfg = {
            position: 'right'
        };

        this.perspectiveCfg = new pimcore.perspective(this.perspectiveCfg);
        this.position = this.perspectiveCfg.position;

        var parentPanel = Ext.getCmp('pimcore_panel_tree_' + this.position);

        this.config = {
            rootId: 1,
            collectionId: config.collectionId,
            treeId: 'collection_' + config.collectionId,
            treeTitle: config.name,
            parentPanel: parentPanel
        };

        pimcore.layout.treepanelmanager.register(this.config.treeId);

        var rootNode = {
            id: this.config.rootId,
            type: 'folder',
            level: 0,
            permissions: {},
            allowDrop: false,
            allowDrag: false
        };

        this.init(rootNode);

        if(config.moveRight) {
            const doMove = pimcore.layout.treepanelmanager.toRight.bind(this);
            doMove();
        } else {
            const doMove = pimcore.layout.treepanelmanager.toLeft.bind(this);
            doMove();
        }
    },

    init: function (rootNodeConfig) {

        var itemsPerPage = pimcore.settings['object_tree_paging_limit'];

        rootNodeConfig.text = t('home');
        rootNodeConfig.id = '' +  rootNodeConfig.id;
        rootNodeConfig.allowDrag = true;
        rootNodeConfig.iconCls = 'pimcore_icon_home';
        rootNodeConfig.cls = 'pimcore_tree_node_root';
        rootNodeConfig.expanded = true;

        var store = Ext.create('pimcore.data.PagingTreeStore', {
            autoLoad: true,
            autoSync: false,
            model: 'collectionElementModel',
            proxy: {
                type: 'ajax',
                url: this.treeDataUrl,
                idParam: 'elementId',
                reader: {
                    type: 'json',
                    totalProperty : 'total',
                    rootProperty: 'nodes',
                    idProperty: 'elementId'
                },
                extraParams: {
                    limit: itemsPerPage,
                    collectionId: this.config.collectionId
                }
            },
            pageSize: itemsPerPage,
            root: rootNodeConfig
        });


        this.tree = Ext.create('pimcore.tree.Panel', {
            selModel : {
                mode : 'MULTI'
            },
            store: store,
            region: 'center',
            autoLoad: false,
            iconCls: 'pimcore_icon_material portal-engine_admin_collections',
            cls: 'pimcore_tree_no_root_node',
            id: this.config.treeId,
            title: this.config.treeTitle,
            autoScroll: true,
            animate: false,
            rootVisible: false,
            bufferedRenderer: false,
            border: false,
            listeners: this.getTreeNodeListeners(),
            scrollable: true,
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    appendOnly: true,
                    allowCopy: true,
                    ddGroup: 'element',
                    scrollable: true,
                    allowContainerDrops: false, //not possible to restrict drops if set to true
                    enableDrag: true
                },
                listeners: {
                    nodedragover: this.onTreeNodeOver.bind(this),
                    beforedrop: function(targetNode, data, overModel, dropPosition, dropHandlers) {
                        data.copy = true;
                    },
                    drop: this.onElementDrop.bind(this)
                },
                xtype: 'pimcoretreeview'
            },
            tools: [{
                type: 'close',
                cls: 'portal_engine_collection_tree_close',
                handler: function() {
                    const source = Ext.getCmp('pimcore_panel_tree_' + this.position);
                    source.remove(this.tree, false);
                    if(source.items.getCount() < 1) {
                        source.collapse();
                        source.hide();
                    }
                    pimcore.plugin.PimcorePortalEngineBundle.collections.forgetOpenTree(this.config.collectionId);
                    source.updateLayout();
                    pimcore.layout.refresh();
                }.bind(this)
            },{
                type: 'right',
                handler: pimcore.layout.treepanelmanager.toRight.bind(this),
                hidden: this.position === 'right'
            },{
                type: 'left',
                handler: pimcore.layout.treepanelmanager.toLeft.bind(this),
                hidden: this.position === 'left'
            }]
        });

        store.on('nodebeforeexpand', function (node) {
            this.showTreeNodeLoadingIndicator(node, this.tree);
        }.bind(this));

        store.on('nodeexpand', function (node, index, item, eOpts) {
            clearTimeout(pimcore.helpers.treeNodeLoadingIndicatorTimeouts['collection' + node.data.elementId]);

            var view = this.tree.getView();

            var nodeEl = Ext.fly(view.getNodeByRecord(node));
            if(nodeEl) {
                var icon = nodeEl.query('.x-tree-icon')[0];

                var iconEl = Ext.get(icon);

                iconEl.removeCls('pimcore_tree_node_loading_indicator');
                iconEl.up('.x-grid-cell').removeCls('pimcore_treenode_hide_plus_button');
            }

        }.bind(this));

        this.tree.on('afterrender', function () {
            this.tree.loadMask = new Ext.LoadMask(
                {
                    target: Ext.getCmp(this.config.treeId),
                    msg:t('please_wait')
                });
        }.bind(this));

        this.config.parentPanel.add(this.tree);
        this.config.parentPanel.updateLayout();


        if (!this.config.parentPanel.alreadyExpanded && this.perspectiveCfg.expanded) {
            this.config.parentPanel.alreadyExpanded = true;
            this.tree.expand();
        }

    },

    onTreeNodeOver: function (targetNode, position, dragData, e, eOpts ) {
        var node = dragData.records[0];

        if(targetNode.getOwnerTree().getId() !== node.getOwnerTree().getId()) {
            if(node.data.type !== 'folder') {
                if(node.data.elementType === targetNode.data.elementType && (node.data.className === undefined || node.data.className === targetNode.data.className)) {
                    return true;
                }
            }
        }

        return false;
    },

    onTreeNodeBeforeMove: function (node, oldParent, newParent, index, eOpts ) {
        return false;
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts ) {
        e.stopEvent();
        tree.select();

        var menu = new Ext.menu.Menu();

        if(tree.getSelectionModel().getSelected().length > 1) {
            var selectedIds = [];
            tree.getSelectionModel().getSelected().each(function (item) {
                selectedIds.push(item.parentNode.data.id + '_' + item.data.id);
            });

            if (record.data.permissions['remove']) {
                menu.add(new Ext.menu.Item({
                    text: t('portal-engine.collectiontree.remove'),
                    iconCls: 'pimcore_icon_delete',
                    handler: this.removeElement.bind(this, selectedIds.join(','))
                }));
            }
        } else {
            if (record.data.permissions['remove']) {
                menu.add(new Ext.menu.Item({
                    text: t('portal-engine.collectiontree.remove'),
                    iconCls: 'pimcore_icon_delete',
                    handler: this.removeElement.bind(this, record.parentNode.data.id + '_' + record.data.id)
                }));
            }

            menu.add({
                text: t('refresh'),
                iconCls: 'pimcore_icon_reload',
                handler: this.reloadNode.bind(this, tree, record)
            });
        }

        pimcore.plugin.broker.fireEvent('prepareObjectTreeContextMenu', menu, this, record);

        menu.showAt(e.pageX+1, e.pageY+1);
    },

    showTreeNodeLoadingIndicator: function(record, tree) {
        pimcore.helpers.treeNodeLoadingIndicatorTimeouts['collection' + record.data.elementId] = window.setTimeout(function (tree, record) {
            var view = tree.getView();
            var nodeEl = Ext.fly(view.getNodeByRecord(record));
            var icon = nodeEl.query('.x-tree-icon')[0];

            var iconEl = Ext.get(icon);
            iconEl.addCls('pimcore_tree_node_loading_indicator');
        }.bind(this, tree, record), 200);
    },

    reloadNode: function(tree, record) {
        this.showTreeNodeLoadingIndicator(record, this.tree);
        pimcore.elementservice.refreshNode(record);
    },

    onTreeNodeClick: function (tree, record, item, index, event, eOpts ) {
        if (event.ctrlKey === false && event.shiftKey === false && event.altKey === false) {
            try {
                if (record.data.permissions.view) {

                    if(record.data.elementType === 'object') {
                        pimcore.helpers.openObject(record.data.id, record.data.type);
                    }

                    if(record.data.elementType === 'asset') {
                        pimcore.helpers.openAsset(record.data.id, record.data.type);
                    }
                }
            } catch (e) {
                console.log(e);
            }
        }
    },

    onElementDrop: function(targetNode, data, overModel) {
        overModel.getOwnerTree().loadMask.show();

        let elementIds = [];
        data.records.forEach(element => elementIds.push(element.data.id));

        // get root node config
        Ext.Ajax.request({
            url: Routing.generate('pimcore_portalengine_admin_collection_add'),
            params: {
                collectionId: this.config.collectionId,
                targetId: overModel.data.elementId,
                elementIds: elementIds.join()
            },
        }).then(function(response) {
            var rdata = Ext.decode(response.responseText);

            if (rdata && !rdata.success) {
                pimcore.helpers.showNotification(t('error'), t('portal-engine.collectiontree.failed_add_new'), 'error', t(rdata.message));
            }
            pimcore.elementservice.refreshNode(overModel);
            overModel.getOwnerTree().loadMask.hide();
        });
    },

    removeElement: function(elementIds) {
        this.tree.loadMask.show();

        Ext.Ajax.request({
            url: Routing.generate('pimcore_portalengine_admin_collection_remove'),
            params: {
                collectionId: this.config.collectionId,
                elementIds: elementIds
            },
        }).then(function(response) {
            var rdata = Ext.decode(response.responseText);

            if (rdata) {
                if (!rdata.success) {
                    pimcore.helpers.showNotification(t('error'), t('portal-engine.collectiontree.failed_remove'), 'error', t(rdata.message));
                } else {
                    rdata.parentIds.forEach(function(parentId) {
                        const store = this.tree.getStore();
                        var node = store.getNodeById(parentId);
                        pimcore.elementservice.refreshNode(node);
                    }.bind(this));

                }
            }
            this.tree.loadMask.hide();
        }.bind(this));
    }
});

