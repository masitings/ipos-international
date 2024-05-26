/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {Fragment, useState} from 'react';
import {ReactComponent as SearchIcon} from "~portal-engine/icons/search";
import {Typeahead, Menu, MenuItem, Highlighter} from "react-bootstrap-typeahead";
import {useTranslation} from "~portal-engine/scripts/components/Trans";
import {getIconComponentByName} from "~portal-engine/scripts/utils/utils";
import {requestSearch} from "~portal-engine/scripts/features/search/search-actions";
import Media from "react-media";
import {MD_DOWN} from "~portal-engine/scripts/consts/mediaQueries";
import Overlay from "~portal-engine/scripts/components/Overlay";
import placeholderImageSrc from "~portal-engine/images/placeholder-img.svg";
import {gotoSearchResult} from "~portal-engine/scripts/features/search/search-api";

export function TypeaheadSearch(props) {
    const [isOpen, setIsOpen] = useState(false);

    const overlayTitle = useTranslation('search-overlay.title');

    return (
        <Media queries={{
            small: MD_DOWN,
        }}>
            {matches => (
                matches.small
                    ? <Fragment>
                        <button type="button"
                                className="btn btn-link p-0 main-navbar__top__item__icon"
                                onClick={() => setIsOpen(!isOpen)}>
                            <SearchIcon/>
                        </button>
                        <Overlay title={overlayTitle} isShown={isOpen} onClose={() => setIsOpen(false)}>
                            <SearchInput isMobile={true} autoFocus={true} {...props}/>
                        </Overlay>
                    </Fragment>
                    : <SearchInput {...props}/>
            )}
        </Media>
    );
}

export function SearchInput(props) {
    const [multiSelections, setMultiSelections] = useState([...new URLSearchParams(decodeURIComponent(document.location.search)).entries()].filter(([key]) => key.startsWith('q[')).map(([_, value]) => value) || []);
    const [isLoading, setIsLoading] = useState(false);
    const [options, setOptions] = useState({items: []});
    const [currentInput, setCurrentInput] = useState('');
    const placeHolderText = useTranslation('typeahead.search');
    const imgPlaceholderTitle = useTranslation('placeholder-image');

    const handleSearch = (query) => {
        setCurrentInput(query);
        setIsLoading(true);

        let params = multiSelections.map((selection) => ({name: 'q[]', value: selection}));
        params.push({name: 'q[]', value: query});

        requestSearch(params).then(result => {
            setIsLoading(false);
            setOptions(result);
        });
    };

    const handleSubmit = (evt) => {
        evt.preventDefault();
        gotoSearchResult([...multiSelections, currentInput]);
    };

    const handleChange = (currentSelections) => {
        setMultiSelections(currentSelections);

        let resultObject = currentSelections.find(result => typeof result !== "string" && result.url);
        if (resultObject) {
            // an item form the menu was selected; go to detail page
            window.location = resultObject.url;
        } else {
            gotoSearchResult(currentSelections);
        }
    };

    return (
        <Fragment>
            <form className={props.isMobile ? 'full-height-layout' : ''}
                  onSubmit={(evt) => handleSubmit(evt)}>
                <label htmlFor="basic-typeahead-multiple"
                       className="sr-only">{useTranslation('typeahead.search')}</label>
                <Typeahead
                    {...props}
                    id="basic-typeahead-search"
                    className="typeahead"
                    labelKey="label"
                    filterBy={() => true}
                    multiple={true}
                    isLoading={isLoading}
                    onInputChange={handleSearch}
                    onChange={handleChange}
                    options={[
                        ...(currentInput ? [{label: currentInput, isCurrentInput: true}] : []),
                        ...((options && options.items) ? options.items : [])]}
                    selectHintOnEnter={true}
                    allowNew={true}
                    renderMenu={(results, menuProps, state) => {
                        let index = 1;
                        const groups = results.reduce((r, obj) => {
                            if (obj.groupId) {
                                r[obj.groupId] = [...r[obj.groupId] || [], obj];
                            }
                            return r;
                        }, {});

                        let items = [
                            ...Object.keys(groups)
                                .sort()
                                .map((group, groupIndex) => {
                                    return (
                                        <Fragment key={groupIndex}>
                                            {options && options.groups && options.groups.find((x) => x.id === parseInt(group)) ? (
                                                <ResultHeader key={group.id}
                                                              group={options.groups.find((x) => x.id === parseInt(group))}/>
                                            ) : null}

                                            {groups[group].map((i) => {
                                                const item =
                                                    <MenuItem key={i.id} option={i} position={index}>
                                                        <div className="row align-items-center">
                                                            <div className="col-4 col-md-2">
                                                                <div className="embed-responsive embed-responsive-16by9">
                                                                    {i.img ? (
                                                                        <div className="embed-responsive-item blur-image text-center">
                                                                            <div className="blur-image__bg"
                                                                                 style={{backgroundImage: `url(${i.img})`}}/>
                                                                            <img src={i.img}
                                                                                 className="position-relative blur-image__image"/>
                                                                        </div>
                                                                    ) : (
                                                                        <img src={placeholderImageSrc}
                                                                             alt={imgPlaceholderTitle}
                                                                             className="embed-responsive-item"/>
                                                                    )}
                                                                </div>
                                                            </div>
                                                            <div className="col-8 col-md-10">
                                                                <Highlighter search={state.text}>
                                                                    {i.label}
                                                                </Highlighter>
                                                            </div>
                                                        </div>
                                                    </MenuItem>;

                                                index++;
                                                return item;
                                            })}
                                        </Fragment>
                                    );
                                })
                        ];

                        return (
                            results.length ? (
                                <Menu {...menuProps}>
                                    {results.filter(({isCurrentInput}) => isCurrentInput)
                                        .map((result, i) => (
                                            <MenuItem key={`current-input-${i}`} option={result.label} position={0}>
                                                {result.label}
                                            </MenuItem>
                                        ))[0]
                                    }

                                    {items}
                                </Menu>
                            ) : null
                        );
                    }}
                    placeholder={placeHolderText}
                    selected={multiSelections}
                >
                    <button type="submit" className="btn btn-link rbt__form-icon">
                        <SearchIcon height="22"/>
                    </button>
                </Typeahead>
            </form>
        </Fragment>
    );
}

export function ResultHeader({group}) {
    const showAllText = useTranslation('typeahead.show-all');

    return (
        <Menu.Header>
            <div className="row row-gutter--2 align-items-baseline">
                {(group.icon && renderGroupIcon(group.icon)) ? (
                    <div className="col-auto">
                        {renderGroupIcon(group.icon)}
                    </div>
                ) : null}
                <div className="col">
                    <div className="rbt-menu__category-title">{group.name}</div>
                </div>
                <div className="col-auto">
                    <a href={group.url} className="rbt-menu__category-link">
                        {showAllText} <span className="d-none d-md-inline-block">{group.name}</span>
                    </a>
                </div>
            </div>
        </Menu.Header>
    )
}

function renderGroupIcon(icon) {
    let Icon = getIconComponentByName(icon);
    if (Icon) {
        return <Icon height="16"/>
    } else {
        return null
    }
}


export default TypeaheadSearch;