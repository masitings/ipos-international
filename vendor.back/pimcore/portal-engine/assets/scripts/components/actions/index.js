/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


export const filterActionHandlerByPermissions = ({permissions, actionHandler = {}}) => {
    if (permissions) {
        if (permissions.download === false) {
            let {onDownload, onAddToCart, ...rest} = actionHandler;
            actionHandler = rest;
        }

        if (permissions.share === false) {
            let {onShare, ...rest} = actionHandler;
            actionHandler = rest;
        }

        if (permissions.delete === false || permissions.delete === undefined) {
            let {onDelete, ...rest} = actionHandler;
            actionHandler = rest;
        }

        if (permissions.edit === false || permissions.edit === undefined) {
            let {onEdit, ...rest} = actionHandler;
            actionHandler = rest;
        }

        if (permissions.update === false || permissions.update === undefined) {
            let {onUpdate, onReplace, ...rest} = actionHandler;
            actionHandler = rest;
        }

        if (permissions.collectionRemove === false) {
            let {onRemoveFromCollection, ...rest} = actionHandler;
            actionHandler = rest;
        }
    }

    return actionHandler;
};

export const mergePermissionList = permissions =>
    permissions.reduce((acc, cur) => {
        let newAcc = {...acc};
        Object.entries(cur).filter(([key]) => !acc[key]).forEach(([key, value]) => {
            newAcc[key] = value;
        });

        return newAcc;
    }, {});