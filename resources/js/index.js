import * as L from "leaflet";
import "leaflet-fullscreen";
import "leaflet-control-geocoder";
import "leaflet-draw";
import "leaflet-editable";
// import { point, buffer, circle } from '@turf/turf';

// Assigning the imported functions to the turf object
// const turf = {
//     point: point,
//     buffer: buffer,
//     circle: circle
// };

window.mapPicker = ($wire, config) => {
    return {
        map: null,
        tile: null,
        marker: null,
        createMap: function (el) {
            const that = this;

            this.map = L.map(el, config.controls);
            L.Control.geocoder().addTo(this.map);


            var editableLayers = new L.FeatureGroup();
            this.map.addLayer(editableLayers);   
        
            var options = {
                position: 'topright',        
                edit: {
                    featureGroup: editableLayers,
                },
        
                draw: {
                    polygon: false,
                    polyline: false,
                    rectangle: false,
                    circle: true, // Enable circle drawing
                    circlemarker: false,
                    marker: false

                },
    
                // draw: {
                //     polygon: {
                //         allowIntersection: false,
                //         showArea: true
                //     }
                // }
        
            };
                            
        
            var drawControl = new L.Control.Draw(options);
            this.map.addControl(drawControl);
        
            this.map.on('draw:created', function (e) {
                var layer = e.layer;
                var type = e.layerType;
        
            if (type === 'circle') {
        
                var theCenterPt = layer.getLatLng();
        
                var center = [theCenterPt.lng,theCenterPt.lat]; 

                
                var theRadius = layer.getRadius();
                console.log(theRadius);
                editableLayers.addLayer(layer);
        
                // Turf Circle
                // var turfCircle;
                // var options = {steps: 64, units: 'meters'};  //Change steps to 4 to see what it does.
                // var turfCircle = turf.circle(center, theRadius, options);
                // var NewTurfCircle = new L.GeoJSON(turfCircle, {color:"black"}).addTo(this.map);
        
                // //Turf Buffer
                // var thepoint = turf.point(center);
                // var buffered = turf.buffer(thepoint, theRadius, {units: 'meters'});
                // var NewTurfBuffer = new L.GeoJSON(buffered, {color:"red"}).addTo(this.map);
            }
        });
        

            // this.map.on(L.Draw.Event.CREATED, function (e) {
            //     var type = e.layerType,
            //         layer = e.layer;
            
            //     if (type === 'marker') {
            //         layer.bindPopup('A popup!');
            //     }
            
            //     editableLayers.addLayer(layer);
            // });
        

            this.map.on("load", () => {
                setTimeout(() => this.map.invalidateSize(true), 0);
                if (config.showMarker === true) {
                    this.marker.setLatLng(this.map.getCenter());
                }
            });

            // Add click event listener to the map
            this.map.on("click", (e) => {
                this.marker.setLatLng(e.latlng);

                $wire.set(config.statePath, e.latlng, false);
                if (config.liveLocation) {
                    $wire.$refresh();
                }

                
                // Add a new marker at the clicked coordinates
                // this.marker = L.marker(e.latlng).addTo(this.map);
            });

            if (!config.draggable) {
                this.map.dragging.disable();
            }

            this.tile = L.tileLayer(config.tilesUrl, {
                attribution: config.attribution,
                minZoom: config.minZoom,
                maxZoom: config.maxZoom,
                tileSize: config.tileSize,
                zoomOffset: config.zoomOffset,
                detectRetina: config.detectRetina,
            }).addTo(this.map);

            if (config.showMarker === true) {
                const markerColor = config.markerColor || "#3b82f6";
                const svgIcon = L.divIcon({
                    html: `<svg xmlns="http://www.w3.org/2000/svg" class="map-icon" fill="${markerColor}" width="36" height="36" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg>`,
                    className: "",
                    iconSize: [36, 36],
                    iconAnchor: [18, 36],
                });
                this.marker = L.marker([0, 0], {
                    icon: svgIcon,
                    draggable: false,
                    autoPan: true,
                }).addTo(this.map);
                // this.map.on("move", () =>
                //     this.marker.setLatLng(this.map.getCenter())
                // );
            }

            this.map.on("moveend", () => {
                let coordinates = this.getCoordinates();
                if (
                    config.draggable &&
                    (coordinates.lng !== this.map.getCenter()["lng"] ||
                        coordinates.lat !== this.map.getCenter()["lat"])
                ) {
                    // $wire.set(config.statePath, this.map.getCenter(), false);
                    // if (config.liveLocation) {
                    //     $wire.$refresh();
                    // }
                }
            });

            this.map.on("locationfound", function () {
                that.map.setZoom(config.controls.zoom);
            });
            let location = this.getCoordinates();
            if (!location.lat && !location.lng) {
                this.map.locate({
                    setView: true,
                    maxZoom: config.controls.maxZoom,
                    enableHighAccuracy: true,
                    watch: false,
                });
            } else {
                this.map.setView(new L.LatLng(location.lat, location.lng));
            }
        },
        removeMap: function (el) {
            if (this.marker) {
                this.marker.remove();
                this.marker = null;
            }
            this.tile.remove();
            this.tile = null;
            this.map.off();
            this.map.remove();
            this.map = null;
        },
        getCoordinates: function () {
            let location = $wire.get(config.statePath);
            if (location === null || !location.hasOwnProperty("lat")) {
                location = { lat: 0, lng: 0 };
            }
            return location;
        },
        attach: function (el) {
            this.createMap(el);
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.intersectionRatio > 0) {
                            if (!this.map) this.createMap(el);
                        } else {
                            this.removeMap(el);
                        }
                    });
                },
                {
                    root: null, // set document viewport as root
                    rootMargin: "0px", // margin around root
                    threshold: 1.0, // 1.0 means that when 100% of the target is visible
                }
            );
            observer.observe(el);
        },
    };
};
window.dispatchEvent(new CustomEvent("map-script-loaded"));
