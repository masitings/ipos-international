/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */


import React from "react";
import L from "leaflet";
import {getConfig} from "~portal-engine/scripts/utils/general";
import {TileLayer as LeafletTileLayer, Marker as LeafletMarker, Map} from "react-leaflet";
import MarkerIcon from "~portal-engine/icons/map-marker-alt";

export function enabled() {
    return getConfig("geo.tileLayerUrl");
}

export const markerIcon = L.divIcon({
    html: MarkerIcon()
});

export function TileLayer() {
    return (
        <LeafletTileLayer url={getConfig("geo.tileLayerUrl")} attribution={getConfig("geo.copyright")}/>
    );
}

export function Marker(props = {}) {
    props = {...props, icon: markerIcon};

    return (
        <LeafletMarker {...props}/>
    );
}

export function MapFromLayout({layout, style, ...props}) {
    props = {
        ...props,
        zoom: 15,
        style: {
            ...style,
            height: layout.height ? layout.height + "px" : null
        }
    }

    return (
        <Map {...props}/>
    );
}

export function calculatePolyInformationFromLayoutData(latLngs) {
    const bounds = L.latLngBounds();
    const positions = [];

    latLngs.forEach((latLng) => {
        bounds.extend(new L.latLng(latLng.latitude, latLng.longitude));
        positions.push([latLng.latitude, latLng.longitude]);
    });

    const center = [bounds.getCenter().lat, bounds.getCenter().lng];

    return {
        positions: positions,
        center: center,
        bounds: bounds
    }
}

export function renderMapFromLayout(layout, center, bounds, poly) {
    return (
        <MapFromLayout center={center} bounds={bounds} layout={layout}>
            <TileLayer/>
            {poly}
        </MapFromLayout>
    );
}

export function basicPolyOptions() {
    return {
        stroke: true,
        opacity: 0.5,
        fillOpacity: 0.2,
        weight: 2
    }
}