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
import {mapObject, noop} from "~portal-engine/scripts/utils/utils";
import ActionDropdown from "~portal-engine/scripts/components/actions/ActionDropdown";
import {useTranslation} from "~portal-engine/scripts/components/Trans";
import placeholderImageSrc from "~portal-engine/images/placeholder-img.svg";

export default function DataTableRow(props) {
    let {
        id,
        href,
        image,
        listViewAttributes,
        detailListViewAttributes,
        isSelected = false,
        actionHandler,
        onSelectedToggle = noop,
    } = props;

    // call all action handler with id as parameter
    actionHandler = mapObject(actionHandler, (_, fnc) => (() => fnc(id)));

    const placeholderTitle = useTranslation('placeholder-image');

    return (
        <tr className={`data-table__row`} key={id}>
            <th className="position-relative data-table__settings">
                <div className="d-flex">
                    <ActionDropdown actionHandler={actionHandler} actionUrls={{onDetail: href}}/>
                    <button type="button" className={`ml-3 selection-indicator data-table__select ${isSelected ? 'is-selected': ''}`} onClick={() => onSelectedToggle(!isSelected, id)}/>
                </div>
            </th>
            <td className="data-table__img">
                <div className={`embed-responsive embed-responsive-2by1 ${href ? 'clickable' : ''}`}>
                    {image.src ? (
                        <div className="embed-responsive-item blur-image text-center" onClick={() => href ? window.location.href = href : false}>
                            <div className="blur-image__bg" style={{backgroundImage: `url(${image.src})`}}/>
                            <img src={image.src} alt={image.alt} className="position-relative blur-image__image"/>
                        </div>
                    ) : (
                        <img src={placeholderImageSrc} alt={placeholderTitle} onClick={() => href ? window.location.href = href : false} className="embed-responsive-item"/>
                    )}
                </div>
            </td>
            {listViewAttributes.map((attribute, index) => (
                <td key={id + '-' + index}>
                    <div dangerouslySetInnerHTML={{__html: detailListViewAttributes.find(obj => obj.key === attribute.key).value}}/>
                </td>
            ))}
        </tr>
    )
}