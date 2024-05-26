/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from "react";
import ListTeaser from "~portal-engine/scripts/components/ListTeaser";
import {getDownloadListItemById} from "~portal-engine/scripts/features/selectors";
import {connect} from "react-redux";
import {ReactComponent as CheckIcon} from "~portal-engine/icons/check-circle";
import {ReactComponent as WarningIcon} from "~portal-engine/icons/exclamation-circle";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import {editCartItemClicked, removeFormCart} from "~portal-engine/scripts/features/download/download-actions";
import {useConfirmModal} from "~portal-engine/scripts/components/modals/ConfirmModal";

export const mapStateToProps = (state, {id}) => {
    let item = getDownloadListItemById(state, id);

    if (!item) {
        return {};
    }

    return ({
        title: item.name,
        subTitle: (<Trans t={item.dataPoolName} domain="data-pool" />),
        href: item.detailLink,
        configs: item.configs,
        messages: item.messages,
        image: {
            src: item.thumbnail,
            alt: item.name,
        }
    });
};

export function DownloadCartListItem ({configs, messages = [], ...props}) {
    const warningText = useTranslation('download.warning');

    const additionalContent = (messages && messages.length ? (
            <ul className="list-unstyled vertical-gutter vertical-gutter--2">
                {messages.map(message => (
                    <li key={message} className="font-weight-bold text-muted small vertical-gutter__item">
                        <div className="row row-gutter--1">
                            <div className="col-auto">
                                <WarningIcon className="icon-in-text mr-1"
                                             height="1rem"
                                             title={warningText}
                                             aria-label={warningText}/>
                            </div>
                            <div className="col">
                                <Trans t={message} domain="download-cart-message"/>
                            </div>
                        </div>
                    </li>
                ))}
            </ul>
        ) : null
    );

    return (
        <ListTeaser {...props} additionalContent={additionalContent}>
            <CartItemConfig configs={configs}/>
        </ListTeaser>
    )
}

export const mapDispatchToProps = (dispatch, {id}) => {
    return {
        actionHandler: {
            onEdit: (id) => dispatch(editCartItemClicked({id})),
            onDelete: (id) => dispatch(removeFormCart({id}))
        }
    };
};

export function CartItemConfig({configs}) {
    const selectedLabel = useTranslation('selected');

    return (
        <div className="row vertical-gutter--2">
            {configs.map(({attribute, type, label, formatLabel}) => (
                <div className="col-md-4 vertical-gutter__item" key={`${attribute}-${type}`}>
                    <div className="key-value-item text-break">
                        <div className="key-value-item__label"><Trans t={label} domain="download-type"/>:</div>
                        <div className="key-value-item__value">{formatLabel ? <Trans t={formatLabel} domain="download-format"/> :  <CheckIcon label={selectedLabel} title={selectedLabel} height="1rem"/>}</div>
                    </div>
                </div>
            ))}
        </div>
    )
}

export default connect(mapStateToProps, mapDispatchToProps)(function (props) {
    const {confirm, confirmModal} = useConfirmModal(props.actionHandler.onDelete, {
        title: <Trans t="download.delete-confirm.title"/>,
        message: <Trans t="download.delete-confirm.text"/>,
        cancelText: <Trans t="download.delete-confirm.cancel"/>,
        confirmText: <Trans t="download.delete-confirm.confirm"/>,
        confirmStyle: "danger",
    });

    let transformedProps = {
        ...props,
        actionHandler: {
            ...props.actionHandler,
            onDelete: confirm
        }
    };

    return <Fragment>
        <DownloadCartListItem {...transformedProps}/>

        {confirmModal}
    </Fragment>
})