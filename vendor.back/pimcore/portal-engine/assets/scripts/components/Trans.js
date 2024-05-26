/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React, {useState, useEffect} from "react";
import {trans} from "~portal-engine/scripts/utils/intl"
import {makePromiseAbortable} from "~portal-engine/scripts/utils/general"

export default function Trans(props) {
    const {
        t,
        domain,
        params
    } = props;

    if (!t) {
        return null;
    }

    return useTranslation(t, domain, params);
}

export function useTranslation(key, domain, params) {
    const [translation, setTranslation] = useState("\u00a0");

    useEffect(() => {
        const {promise, abort} = makePromiseAbortable(trans(key, domain, params));

        promise
            .then((translation) => {
                setTranslation(translation);
            }).catch((...args) => {
                // nothing to do here, was aborted
            });

        return () => {
            abort();
        }
    }, [key, domain, params]);

    return translation;
}