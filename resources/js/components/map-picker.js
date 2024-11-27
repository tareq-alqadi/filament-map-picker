import * as L from "leaflet";
import "leaflet-fullscreen";
import "leaflet-control-geocoder";
// import "leaflet-draw";
// import "leaflet-editable";

export default function mapPicker({
    id,
    state,
    config,
    hasMarkerCircle,
    toolbarButtons,
}) {
    return {
        id,

        state,

        config,

        hasMarkerCircle,

        toolbarButtons,

        map: null,

        tile: null,

        marker: null,

        markerCircle: null,

        init: function () {

            this.state = {
                lat: this.state.lat ?? 0,
                lng: this.state.lng ?? 0,
                radius: this.state.radius ?? 0,
            };

            setTimeout(() => {
                this.createMap(this.$refs[`map-${this.id}`]);
            }, 500);

            this.$watch("state", () => {
                this.setLocation(this.state.lat, this.state.lng);
            });
        },

        setLocation: function (lat, lng) {
            const latLng = new L.LatLng(lat, lng);
            this.marker.setLatLng(latLng);
            this.map.setView(latLng);
            this.drawCircleAroundMarker();
        },

        createMap: function (el) {
            this.initializeMap(el);

            if (this.toolbarButtons.includes('search')) {
                this.addGeocoderControl();
            }

            if (this.toolbarButtons.includes('fullScreen')) {
                this.addFullScreenControl();
            }

            if (this.toolbarButtons.includes('zoomControl')) {
                this.addZoomControl();
            }

            // this.addEditableLayers();
            // this.addDrawControl();

            this.setupEventListeners();
            this.addTileLayer();
            this.addMarker();
            this.handleInitialLocation();
        },

        initializeMap: function (el) {
            const bounds = L.latLngBounds(L.latLng(-90, -180), L.latLng(90, 180));

            this.map = L.map(el, {
                maxBounds: bounds,
                maxBoundsViscosity: 1.0,
                zoomControl: false,
                ...this.config.controls,
            });

        },

        createSvgIcon: function (fillColor = "#3b82f6", width = 36, height = 36) {
            fillColor = this.config.markerColor || fillColor;

            return L.divIcon({
                html: `<svg xmlns="http://www.w3.org/2000/svg" class="map-icon" fill="${fillColor}" width="${width}" height="${height}" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg>`,
                className: "",
                iconSize: [width, height],
                iconAnchor: [width / 2, height],
            });
        },

        addGeocoderControl: function () {
            L.Control.geocoder({
                defaultMarkGeocode: false
            })
                .on('markgeocode', (e) => {

                    const latlng = e.geocode.center;
                    this.marker.setLatLng(latlng);

                    this.map.fitBounds(e.geocode.bbox, { maxZoom: 17 });
                    this.map.setView(new L.LatLng(latlng.lat, latlng.lng));

                    this.setState({
                        lat: latlng.lat,
                        lng: latlng.lng,
                    });

                })
                .addTo(this.map);
        },

        addEditableLayers: function () {
            this.editableLayers = new L.FeatureGroup();
            this.map.addLayer(this.editableLayers);
        },

        addDrawControl: function () {
            const options = {
                position: 'topright',
                edit: {
                    featureGroup: this.editableLayers,
                },
                draw: {
                    polygon: false,
                    polyline: false,
                    rectangle: false,
                    circle: true,
                    circlemarker: false,
                    marker: false
                },
            };

            const drawControl = new L.Control.Draw(options);
            this.map.addControl(drawControl);

            this.map.on('draw:created', (e) => {
                const layer = e.layer;
                const type = e.layerType;

                if (type === 'circle') {
                    const theCenterPt = layer.getLatLng();
                    const center = [theCenterPt.lng, theCenterPt.lat];
                    const theRadius = layer.getRadius();
                    this.editableLayers.addLayer(layer);
                }
            });
        },

        setupEventListeners: function () {

            this.map.whenReady(() => {
                if (this.config.showMarker) {
                    this.marker.setLatLng(this.map.getCenter());
                }

                setTimeout(() => {
                    this.map.invalidateSize();
                }, 1000);
            });

            this.map.on('drag', () => {
                const bounds = L.latLngBounds(L.latLng(-90, -180), L.latLng(90, 180));

                this.map.panInsideBounds(bounds, { animate: false });
            });

            if (this.config.interactiveMarker) {
                this.map.on("click", (e) => {
                    this.marker.setLatLng(e.latlng);
                    this.drawCircleAroundMarker();

                    if (this.config.followMarker) {
                        this.map.setView(new L.LatLng(e.latlng.lat, e.latlng.lng));
                    }

                    this.setState({
                        lat: e.latlng.lat,
                        lng: e.latlng.lng,
                    });
                });
            }
        },

        addTileLayer: function () {

            this.tile = L.tileLayer(this.config.tilesUrl, {
                attribution: this.config.attribution,
                minZoom: this.config.minZoom,
                maxZoom: this.config.maxZoom,
                zoomOffset: this.config.zoomOffset,
                detectRetina: this.config.detectRetina,
            }).addTo(this.map);
        },

        addMarker: function () {
            if (this.config.showMarker) {
                const svgIcon = this.createSvgIcon(this.config.markerColor);

                this.marker = L.marker([0, 0], {
                    icon: svgIcon,
                    draggable: this.config.draggableMarker,
                    interactive: this.config.interactiveMarker,
                    autoPan: true,
                })
                    .addTo(this.map)
                    .on('drag', (e) => {
                        this.drawCircleAroundMarker();
                    })
                    .on('moveend', (e) => {

                        this.setState({
                            lat: e.target._latlng.lat,
                            lng: e.target._latlng.lng,
                        });

                    });

            }

        },

        addFullScreenControl: function () {
            this.map.addControl(new L.Control.Fullscreen());
        },

        addZoomControl: function () {
            L.control.zoom().addTo(this.map);
        },

        handleInitialLocation: function () {
            const location = this.getCoordinates();

            if (!location.lat && !location.lng) {
                this.map.locate({
                    setView: true,
                    maxZoom: this.config.controls.maxZoom,
                    enableHighAccuracy: true,
                    watch: false,
                });
            } else {
                this.map.setView(new L.LatLng(location.lat, location.lng));
                this.drawCircleAroundMarker();
            }
        },


        setState: function ({ lat, lng }) {
            this.state = {
                lat: lat.toFixed(9),
                lng: lng.toFixed(9),
                radius: this.state.radius
            };
        },

        drawCircleAroundMarker: function (radius) {
            if (this.marker && this.hasMarkerCircle) {

                this.removeCircleAroundMarker();

                this.markerCircle = L.circle(this.marker.getLatLng(), {
                    color: this.config.markerCircleColor || 'green',
                    fillColor: this.config.markerCircleColor || 'green',
                    fillOpacity: 0.5,
                    radius: radius || this.state.radius
                }).addTo(this.map);
            }

        },

        removeCircleAroundMarker: function () {
            if (this.markerCircle) {
                this.markerCircle.remove();
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
            let location = this.state || {};
            return {
                lat: location.lat || 0,
                lng: location.lng || 0
            };
        },

    };
}
