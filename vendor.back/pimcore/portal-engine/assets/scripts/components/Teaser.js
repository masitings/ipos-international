/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment} from 'react';
import {mapObject, noop} from "~portal-engine/scripts/utils/utils";
import Media from 'react-media';
import ActionBar from "~portal-engine/scripts/components/actions/ActionBar";
import ActionDropdown from "~portal-engine/scripts/components/actions/ActionDropdown";
import Trans, {useTranslation} from "~portal-engine/scripts/components/Trans";
import placeholderImageSrc from "~portal-engine/images/placeholder-img.svg";

export const defaultProps = {
    readonly: false,
    className: "",
    actionHandler: {},
    image: {},
    isSelected : false,
    onSelectedToggle : noop,
    ImageComponent: Image,
    BodyComponent: Body,
    DetailActionComponent: DetailAction,
};

export default function (props) {
    props = {
        ...defaultProps,
        ...props
    };

    // call all action handler with id as parameter
    props.actionHandler = mapObject(props.actionHandler, (_, fnc) => (() => fnc(id)));

    let {
        id,
        className,
        type,
        href,
        actionHandler,
        ImageComponent,
        BodyComponent
    } = props;

    return (
        <div className={`teaser d-flex flex-column ${className}`}>
            <ImageComponent {...props} />

            <Media query="(hover: none) and (pointer: coarse), screen and (max-width: 767px) " render={() => (
                <ActionDropdown classNames={{toggle: 'text-white', wrapper: 'options-toggle'}} actionHandler={actionHandler} actionUrls={{onDetail: href}}/>
            )}/>

            {type ? (
                <div className="badge badge-primary teaser__badge">{type}</div>
            ) : null}

            <BodyComponent {...props}/>
        </div>
    );
}

export function Image(props) {
    props = {
        ...defaultProps,
        ...props
    };

    const {
        title,
        image,
        isSelected,
        onSelectedToggle,
        DetailActionComponent,
    } = props;

    const placeholderTitle = useTranslation('placeholder-image');

    return (
        <div className="embed-responsive embed-responsive-16by9 teaser__img">
            {image.src ? (
                <div className="embed-responsive-item blur-image text-center">
                    <div className="blur-image__bg" style={{backgroundImage: `url(${image.src})`}}/>
                    <img {...image} alt={image.alt || title} className="position-relative blur-image__image"/>
                </div>
            ) : (
                <img src={placeholderImageSrc} alt={placeholderTitle} className="embed-responsive-item"/>
            )}

            <DetailActionComponent onSelectedToggle={onSelectedToggle} isSelected={isSelected} {...props}/>
        </div>
    )
}

export function Body(props) {
    props = {
        ...defaultProps,
        ...props
    };

    const {
        title,
        children,
        href,
        readonly,
        actionHandler,
    } = props;


    return (
        <div className="teaser__body d-flex flex-column flex-grow-1 position-relative">
            {!readonly ? (
                <Media query="screen and (min-width: 768px) and (hover: hover)" render={() => (
                    <ActionBar className="action-bar--absolute" actionHandler={actionHandler}/>
                )}/>
            ) : null}

            {title ? (
                <h3 className="teaser__title"><a href={href}>{title}</a></h3>
            ) : null}

            <div className="mt-auto">
                {children}
            </div>
        </div>
    )
}

export function DetailAction(props) {
    props = {
        ...defaultProps,
        ...props
    };

    const {
        href,
        readonly,
        isSelected,
        size,
        onSelectedToggle,
    } = props;

    return (
        <Fragment>
            {!readonly ? (
                <button type="button" className={`teaser__select selection-indicator ${isSelected ? 'is-selected': ''}`} onClick={() => onSelectedToggle(!isSelected)}/>
            ) : null}

            <div className="teaser__options d-flex flex-column align-items-center justify-content-center">
                {href ? (
                    <div>
                        <a href={href} className={`btn btn-outline-light ${size === 'sm' ? 'btn-sm': ''}`}>
                            <Trans t="teaser.open"/>
                        </a>
                    </div>
                ) : null}
            </div>
        </Fragment>
    )
}




