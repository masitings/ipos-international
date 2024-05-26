/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useEffect} from "react";
import {CSSTransition} from 'react-transition-group';
import {noop} from "~portal-engine/scripts/utils/utils";
import {ReactComponent as CloseIcon} from "~portal-engine/icons/close";
import * as ReactDOM from "react-dom";
import {useTranslation} from "~portal-engine/scripts/components/Trans";

const portalDomNode = document.querySelector('#overlay-portal');
if (!portalDomNode) {
    console.error('Could not find overlay portal node with id "overlay-portal".');
}

export default function (props) {
    return portalDomNode
        ? ReactDOM.createPortal(<Overlay {...props}/>, portalDomNode)
        : <Overlay {...props}/>
}

function Overlay({
    className = '',
    isShown = false,
    title = '',
    children,
    onClose = noop
}) {
    let closeText = useTranslation('overlay.close');

    useEffect(() => {
        if (isShown) {
            document.body.style.top = `-${window.scrollY}px`;
            document.body.style.position = 'fixed';
        }

    }, [isShown]);

    const handleClose = () => {
        onClose();

        const scrollY = document.body.style.top;
        document.body.style.position = '';
        document.body.style.top = '';
        window.scrollTo(0, parseInt(scrollY || '0') * -1);
    };


    return (
        <CSSTransition
            classNames="overlay-"
            in={isShown}
            unmountOnExit={true}
            timeout={120}>
            {/*todo aria attribues*/}

            <section className={`overlay ${className}`} tabIndex="-1" role="dialog">
                <div className="overlay__head">
                    <div className="overlay__head-item overlay__head-item--main">
                        {title ? (
                            <h4 className="overlay__title">{title}</h4>
                        ) : null}
                    </div>
                    <div className="overlay__head-item">
                        <button type="button"
                                className="btn btn-link"
                                onClick={() => handleClose()}
                                title={closeText}
                                aria-label={closeText}>
                            <CloseIcon width={20} height={20}/>
                        </button>
                    </div>
                </div>
                <div className="overlay__body full-height-layout">
                    <div className="container full-height-layout full-height-layout__fill">
                        {children}
                    </div>
                </div>
            </section>
        </CSSTransition>
    )
}