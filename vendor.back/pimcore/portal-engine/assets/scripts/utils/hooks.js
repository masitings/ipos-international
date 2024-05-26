/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import {useEffect, useRef, useState} from "react";
import {FAILED, FETCHING, NOT_ASKED, SUCCESS} from "~portal-engine/scripts/consts/fetchingStates";

export const useFetch = (url) => {
    const cache = useRef({});
    const [status, setStatus] = useState(NOT_ASKED);
    const [payload, setPayload] = useState();
    const [error, setError] = useState();

    useEffect(() => {
        if (!url) return;
        const fetchData = async () => {
            try {
                setStatus(FETCHING);
                if (cache.current[url]) {
                    const data = cache.current[url];
                    setPayload(data);
                    console.log('set payload', data);
                    setStatus(SUCCESS);
                } else {
                    const response = await fetch(url);
                    const data = await response.json();
                    if (data.success) {
                        cache.current[url] = data; // set response in cache;
                        console.log('set payload', data);
                        setPayload(data);
                        setStatus(SUCCESS);
                    } else {
                        setStatus(FAILED);
                        setError(data.error);
                    }
                }
            } catch (e) {
                // todo catch
                setStatus(FAILED);
                setError(e)
            }
        };

        fetchData();
    }, [url]);

    return { status, payload, error };
};
