/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {useEffect} from "react";
import {serializeParamTupleArray, sortParamTuples} from "~portal-engine/scripts/utils/utils";

export function useSearchParams({
    currentParams,
    paramNames,
    onSetup = noop,
    onURLChanged = noop,
}) {
    useEffect(() => {
        // Sync state with url params
        let urlSearchParams = Array.from(new URLSearchParams(location.search).entries());
        onSetup(urlSearchParams);

        window.addEventListener('popstate', () => {
            let urlSearchParams = Array.from(new URLSearchParams(location.search).entries());
            onURLChanged(urlSearchParams);
        });
    }, []);

    useEffect(() => {
        // Push history state
        let unknownParams = Array.from(new URLSearchParams(location.search))
            .filter(([key]) => !paramNames.includes(key));

        let params = [ ...unknownParams, ...currentParams].sort(sortParamTuples);

        let paramString = params.length
            ? `?${serializeParamTupleArray(params)}`
            : '';

        if (location.search !== paramString) {
            history.pushState(document.title, history.state, location.pathname + paramString)
        }
    }, [currentParams, paramNames]);
}
