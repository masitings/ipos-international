/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


(function () {
    function initSidebarButtons() {
        const buttons = document.getElementsByClassName("js-pe-open-sidebar");

        if (buttons.length) {
            for (let i = 0; i < buttons.length; i++) {
                let button = buttons[i];

                button.addEventListener("click", function (event) {
                    event.preventDefault();

                    const target = document.getElementById(button.dataset.href);

                    if (target) {
                        target.classList.toggle("open");
                    }
                });
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        initSidebarButtons();
    });
})();