/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import {Modal} from "react-bootstrap";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import {ReactComponent as DownloadICon} from "~portal-engine/icons/arrow-alt-circle-down";
import {useTranslation} from "~portal-engine/scripts/components/Trans";
import {noop} from "~portal-engine/scripts/utils/utils";
import Checkbox from "./Checkbox";

export default function (props){
    const {
        title = '',
        children,
        download = true,
        isOpen,
        filters = [],
        componentsByType = {},
        onHide = noop,
        onDownload = noop,
        onClose = noop,
        classNames = {
            container: '',
            body: ''
        },
    } = props;

    let closeText = useTranslation('modal.close');
    let downloadText = useTranslation('modal.download');

    return (
        <Modal
            show={isOpen}
            onHide={onHide}
            backdrop="static"
            centered
        >
            <Modal.Header>
                <Modal.Title>{title}</Modal.Title>
                <button type="button" className="close" data-dismiss="modal" aria-label={closeText} onClick={() => onClose(!isOpen)}>
                    <span aria-hidden="true"><CloseIcon width="22" height="22" /></span>
                </button>
            </Modal.Header>
            <Modal.Body className={classNames.body}>
                {children}
                {/*    todo */}

                <ul className="nav nav-tabs nav-tabs--bg nav-tabs--lg">
                    <li className="nav-item">
                        <a className="nav-link active" href="#">Images</a>
                    </li>
                    <li className="nav-item">
                        <a className="nav-link" href="#">Cars</a>
                    </li>
                </ul>
                <div className="tab-content">
                    <div className="tab-pane active">
                        <div >
                            {filters.map((filter) => {
                                const Component = componentsByType[filter.type].Component;

                                if (!Component) {
                                    console.error(`Missing Component function for type ${filter.type}`);
                                    return;
                                }

                                return (
                                    <div className="row align-items-center" key={filter.name}>
                                        <div className="col-6">
                                            <div className="vertical-gutter__item"><Checkbox label="Add Structure Data" id="checkbox-1" name="checkbox-1"/></div>
                                        </div>
                                        <div className="col-6">
                                            <Component
                                                onChange={selectedOption => handleChange(filter, selectedOption)}
                                                {...filter} />
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </Modal.Body>
            <Modal.Footer className="justify-content-center">
                {download ? (
                    <button type="button" className="btn btn-primary btn-lg btn-rounded btn-with-addon" onClick={() => onDownload()}>
                    <span className="btn__addon">
                        <DownloadICon width="17" height="17"/>
                    </span>
                        {downloadText}
                    </button>
                ) : null}
            </Modal.Footer>
        </Modal>
    )
}