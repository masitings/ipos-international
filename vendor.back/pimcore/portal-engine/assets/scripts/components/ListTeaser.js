/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from 'react';
import {mapObject} from "~portal-engine/scripts/utils/utils";
import Media from 'react-media';
import ActionBar from "~portal-engine/scripts/components/actions/ActionBar";
import {useTranslation} from "~portal-engine/scripts/components/Trans";
import placeholderImageSrc from "~portal-engine/images/placeholder-img.svg";
import ActionDropdown from "~portal-engine/scripts/components/actions/ActionDropdown";

export default function (props) {
    let {
        id,
        className = "",
        title,
        subTitle,
        type,
        image = {},
        children,
        href,
        additionalContent,
        ImageComponent,
        actionHandler = {}
    } = props;

    // call all action handler with id as parameter
    actionHandler = mapObject(actionHandler, (_, fnc) => (() => fnc(id)));

    return (
        <div className={`list-teaser ${className}`}>
            <div className="list-teaser__body">
                <Media query="(hover: none) and (pointer: coarse), screen and (max-width: 767px) " render={() => (
                    <ActionDropdown actionHandler={actionHandler} classNames={{wrapper: 'options-toggle'}}/>
                )}/>

                {type ? (
                    <div className="badge badge-primary teaser__badge">{type}</div>
                ) : null}


                <div className="row align-items-md-center">
                    <div className="col-4 col-md-2">
                        {/*the following block can be overwritten completely*/}
                        {ImageComponent ? (
                            <ImageComponent {...props} />
                        ) : (
                            <div className="embed-responsive embed-responsive-16by9">
                                {
                                    (image && image.src) ? (
                                        <a href={props.href}>
                                            <div className="embed-responsive-item blur-image text-center">
                                                <div className="blur-image__bg" style={{backgroundImage: `url(${image.src})`}}/>
                                                <img {...image} alt={image.alt || title} className="position-relative blur-image__image"/>
                                            </div>
                                        </a>
                                    ) : (
                                        <a href={props.href}><FallbackImage/></a>
                                    )
                                }
                            </div>
                        )}
                    </div>
                    <div className="col-8 col-md-10">
                        <div className="row row-gutter--5 vertical-gutter--2 align-items-center">
                            <div className="col-md-3 col-xl-4 vertical-gutter__item">
                                {title ? (
                                    <h3 className="list-teaser__title"><a href={href}>{title}</a></h3>
                                ) : null}

                                {subTitle ? (
                                    <div className="list-teaser__sub-title text-muted">{subTitle}</div>
                                ) : null}
                            </div>
                            <div className="col-md vertical-gutter__item list-teaser__col-divider">
                                {children}
                            </div>
                            <Media query="screen and (min-width: 768px) and (hover: hover)" render={() => (
                                <div className="col-auto vertical-gutter__item list-teaser__col-divider">
                                    <ActionBar actionHandler={actionHandler}/>
                                </div>
                            )}/>
                        </div>
                    </div>
                </div>
            </div>

            {additionalContent ? (
                <div className="list-teaser__additional-body">
                    {additionalContent}
                </div>
            ): null}
        </div>
    );
}

export function FallbackImage() {
    const placeholderTitle = useTranslation('placeholder-image');

    return (
        <img src={placeholderImageSrc} alt={placeholderTitle} className="embed-responsive-item"/>
    );
}