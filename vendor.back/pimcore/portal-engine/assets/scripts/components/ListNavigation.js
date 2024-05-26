/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useCallback, useState} from 'react';
import Tab from "react-bootstrap/Tab";
import {useTranslation} from "~portal-engine/scripts/components/Trans";
import Nav from "react-bootstrap/Nav";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import Card from "react-bootstrap/Card";
import Accordion from "react-bootstrap/Accordion";
import {ReactComponent as AngleIcon} from "~portal-engine/icons/angle-down";
import {FOLDERS, TAGS} from "~portal-engine/scripts/consts/list-navigation-types";
import {noop} from "~portal-engine/scripts/utils/utils";
import FolderTree from "~portal-engine/scripts/features/folders/components/FolderTree";
import TagTree from "~portal-engine/scripts/features/tags/coponents/TagTree";
import {getConfig} from "~portal-engine/scripts/utils/general";
import TagTextList from "~portal-engine/scripts/features/tags/coponents/TagTextList";

export default function ({
    FolderTreeComponent = FolderTree,
    TagTreeComponent = TagTree,
    ...props
}) {
    let items = [{
        key: FOLDERS,
        label: useTranslation('listing.nav.folders'),
        isActive: getConfig('list.folders.active'),
        Component: FolderTreeComponent
    }, {
        key: TAGS,
        label: useTranslation('listing.nav.tags'),
        isActive: getConfig('list.tags.active'),
        Component: TagTreeComponent
    }].filter(({isActive}) => isActive);

    if (items.length < 0) {
        return;
    }

    return (
        <Media queries={{
            small: MD_DOWN,
        }}>
            {matches => (
                matches.small
                    ? <Accordions items={items} {...props}/>
                    : <Tabs items={items} {...props}/>
            )}
        </Media>
    );
}

export function Tabs({
    navigationType = FOLDERS,
    items = [],
    onSelect = noop
}) {
    if (items.length === 1) {
        let Component = items[0].Component;
        let title = items[0].label;

        return (
            <Fragment>
                <h4 className="mb-3">{title}</h4>
                <Component/>
            </Fragment>
        );
    }

    return (
        <Tab.Container activeKey={navigationType || FOLDERS} onSelect={onSelect}>
            <Nav variant="pills" className="nav-fill">
                {items.map(({key, label}) => (
                    <Nav.Item key={key}>
                        <Nav.Link eventKey={key}>{label}</Nav.Link>
                    </Nav.Item>
                ))}
            </Nav>

            <div className="scroll-area pl-3 py-3">
                <Tab.Content>
                    {items.map(({key, Component}) => (
                        <Tab.Pane key={key} eventKey={key}>
                            <Component/>
                        </Tab.Pane>
                    ))}
                </Tab.Content>
            </div>
        </Tab.Container>
    )
}

const rootPath = getConfig('list.folders.root.path') || '/';

export function Accordions({
    navigationType,
    selectedFolderPath = rootPath,
    selectedTagIds,
    items = [],
    onSelect = noop
}) {
    const [openAccordionId, setOpenAccordionId] = useState(null);
    const handleSelect = useCallback((newNavigationType) => {
        setOpenAccordionId(newNavigationType);
        if (newNavigationType) {
            onSelect(newNavigationType);
        }
    }, []);


    return (
        <Accordion activeKey={openAccordionId} onSelect={handleSelect}>
            {items.map(({label, key, Component}) => {
                const hasValue = navigationType === key && (
                    (key === TAGS && selectedTagIds.length)
                    || (key !== TAGS) && selectedFolderPath !== rootPath);

                const isOpen = openAccordionId === key;

                return (
                    <Card key={key} className="card--light">
                        <Accordion.Toggle className={`form-control form-control-sm form-control--card-header text-truncate ${isOpen ? 'is-open': ''}`}
                                          eventKey={key}>
                            <span className={hasValue || isOpen ? '' :'text-muted'}>
                                {label}
                            </span>

                            {hasValue ? ': ' : ''}

                            {(navigationType === key)
                                ? key === TAGS
                                    ? ( // tags
                                        <TagTextList ids={selectedTagIds} className="d-inline"/>
                                    )
                                    : ( // folders
                                        selectedFolderPath.slice(selectedFolderPath.lastIndexOf('/') + 1)
                                    )
                                : null}

                            <AngleIcon width="20" height="20" className="form-control--card-header__collapse-icon"/>
                        </Accordion.Toggle>

                        <Accordion.Collapse eventKey={key}>
                            <Card.Body className="scroll-area">
                                <Component/>
                            </Card.Body>
                        </Accordion.Collapse>
                    </Card>
                );
            })}
        </Accordion>
    )
}