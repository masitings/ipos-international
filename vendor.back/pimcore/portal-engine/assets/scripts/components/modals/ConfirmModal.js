/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useCallback, useState} from "react";
import {Modal} from "react-bootstrap";
import {noop} from "~portal-engine/scripts/utils/utils";
import {createPortal} from "react-dom";
import Button from "react-bootstrap/Button";
import Trans from "~portal-engine/scripts/components/Trans";

export default function ConfirmModal({
    title,
    children,
    cancelStyle = "outline-secondary",
    confirmStyle = "primary",
    cancelText = <Trans t={'confirm-modal.cancel'}/>,
    confirmText = <Trans t={'confirm-modal.confirm'}/>,
    isOpen = true,
    onCancel = noop,
    onConfirm,
}) {
    return <Modal show={isOpen} onHide={onCancel} backdrop="static" centered>
        {title ? (
            <Modal.Header>
                <Modal.Title>{title}</Modal.Title>
            </Modal.Header>
        ) : null}

        <form onSubmit={onConfirm}>
            {children ? (
                <Modal.Body>
                    {children}
                </Modal.Body>
            ) : null}

            <Modal.Footer className="justify-content-center">
                <Button variant={cancelStyle} onClick={onCancel} className="btn-rounded" type="button">
                    {cancelText}
                </Button>


                {onConfirm ? (
                    <Button variant={confirmStyle} onClick={onConfirm} className="btn-rounded" type="button">
                        {confirmText}
                    </Button>
                ) : null}

            </Modal.Footer>
        </form>
    </Modal>;
}

const portalDomNode = document.querySelector('#overlay-portal');
const PortaledConfirmModal = (props) => {

    return createPortal(
        props.isOpen
            ? (
                <ConfirmModal {...props}>
                    {props.message}
                </ConfirmModal>)
            : null,
        portalDomNode
    );
};

export const useConfirmModal = (callback = noop, props
) => {
    const [value, setValue] = useState('init');

    const [isOpen, setIsOpen] = useState(false);
    const [params, setParams] = useState([]); /* sed to forward passed function parameters from the open function to the callback*/

    const open = (...currentParams) => {
        setParams(currentParams);
        setIsOpen(true)
    };

    const onCancel = useCallback(() => setIsOpen(false));

    const onConfirm = useCallback(() => {
        setIsOpen(false);
        callback(...params);
    });

    const confirmModal = <PortaledConfirmModal
        onConfirm={onConfirm}
        onCancel={onCancel}
        isOpen={isOpen}
        value={value}
        setValue={setValue}
        {...props}
    />;

    return {
        isOpen,
        open,
        confirm: open,
        confirmModal,
    }
};
