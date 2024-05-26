/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {toggleClass, hasClass, addClass, removeClass, closest, find, findAll} from "~portal-engine/scripts/utils/dom-utils";

const isMobile = () => matchMedia('(max-width: 767px)').matches;

export function init () {
    let mainNav = findAll('.js-main-nav');
    mainNav.forEach(function (item) {
        let $nav = item,
            $toggle = find('.js-main-nav__toggle', $nav),
            $collapse = find('.js-main-nav__collapse', $nav);

        if ($toggle) {
            $toggle.addEventListener('click', () => {
                toggleClass('is-open', $toggle);
                toggleClass('is-open', $collapse);
            });
        }

        findAll('.js-main-nav__list').forEach(function (list) {
            let $listItems = findAll('.js-main-nav__list-item', list);
            let $itemToggles = findAll('.js-main-nav__list-toggle', list);

            $itemToggles.forEach(function (toggle) {
                toggle.addEventListener('click', function(evt) {
                    let $parentItem = closest('.js-main-nav__list-item', this);
                    if (hasClass('is-open', $parentItem)) {
                        removeClass('is-open', $parentItem);
                    } else {
                        if (isMobile()) {
                            addClass('is-open', $parentItem);
                        } else {
                            $listItems.forEach(function (item) {
                                removeClass('is-open', item)
                            });
                            addClass('is-open', $parentItem);
                        }
                    }
                })
            });
        });

        let $searchToggle = findAll('.js-toggle-search');
        $searchToggle.forEach(function ($this) {
            $this.addEventListener('click', function () {
                let search = document.getElementById($this.dataset.target);
                if (hasClass('is-open', search)) {
                    removeClass('is-open', search);
                } else {
                    addClass('is-open', search);
                }
            })
        })
    });
}
