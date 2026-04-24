/* =============================================================
 *  İl-içi harita — ilçe polygonları
 *
 *  Blade: <div x-data="cityMap({
 *              provinceSlug: '...',
 *              districtsGeoJsonUrl: '/data/districts/...geojson'
 *          })"> ... <div x-ref="map"></div> ... </div>
 * ============================================================= */

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

function slugify(str) {
    return String(str)
        .toLocaleLowerCase('tr')
        .replace(/ı/g, 'i')
        .replace(/ğ/g, 'g')
        .replace(/ü/g, 'u')
        .replace(/ş/g, 's')
        .replace(/ö/g, 'o')
        .replace(/ç/g, 'c')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

function tileUrlForCurrentTheme() {
    const theme = document.documentElement.getAttribute('data-theme') || '';
    const dark = /dark/i.test(theme);
    return dark
        ? 'https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png'
        : 'https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png';
}

export default function cityMap(config = {}) {
    const provinceSlug = String(config.provinceSlug || '');
    const districtsGeoJsonUrl = config.districtsGeoJsonUrl || null;

    return {
        provinceSlug,
        districtsGeoJsonUrl,

        loading: true,
        error: null,
        hoverDistrict: null,

        _map: null,
        _geoLayer: null,
        _tileLayer: null,
        _themeObserver: null,

        async init() {
            if (!this.districtsGeoJsonUrl) {
                this.loading = false;
                this.error = 'Bu il için harita verisi bulunmuyor.';
                return;
            }
            await this.$nextTick();
            await this.initMap();
        },

        destroy() {
            try { this._themeObserver?.disconnect(); } catch {}
            try { this._map?.remove(); } catch {}
            this._map = null;
            this._geoLayer = null;
            this._tileLayer = null;
        },

        goToDistrict(nameOrSlug) {
            const slug = slugify(nameOrSlug);
            if (!slug || !this.provinceSlug) return;
            // Direkt tarife sayfasına yönlendir
            window.location.href = `/internet-tarifeleri/${this.provinceSlug}/ucuz-${slug}-ev-interneti-fiyatlari`;
        },

        async initMap() {
            const el = this.$refs.map;
            if (!el || this._map) return;

            this._map = L.map(el, {
                zoomControl: true,
                attributionControl: true,
                scrollWheelZoom: false,
                doubleClickZoom: true,
                dragging: true,
                keyboard: true,
                tap: true,
                minZoom: 4,
                maxZoom: 13,
                zoomSnap: 0.25,
                zoomDelta: 0.5,
                wheelPxPerZoomLevel: 80,
            });

            // Türkiye merkezi ile başlat
            this._map.setView([39.5, 32.5], 6);
            // Harita arka planını beyaz yap
            el.style.backgroundColor = '#ffffff';

            // Arka plan tile
            this._tileLayer = L.tileLayer(tileUrlForCurrentTheme(), {
                maxZoom: 18,
                subdomains: 'abcd',
                attribution: '© OpenStreetMap · © CARTO',
                opacity: 0.35,
            }).addTo(this._map);

            // Tema değişince tile'ı yenile
            this._themeObserver = new MutationObserver(() => {
                if (!this._tileLayer) return;
                this._tileLayer.setUrl(tileUrlForCurrentTheme());
            });
            this._themeObserver.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme'],
            });

            try {
                // cache: 'no-cache' — her zaman güncel veri al
                const res = await fetch(this.districtsGeoJsonUrl, { cache: 'no-cache' });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const geojson = await res.json();

                this._geoLayer = L.geoJSON(geojson, {
                    style: () => ({
                        color: '#0b6864',
                        weight: 0.9,
                        fillColor: '#1bb6ad',
                        fillOpacity: 0.28,
                    }),
                    onEachFeature: (feature, layer) => {
                        const raw = feature?.properties?.name || '';
                        const display = raw
                            .split(/\s+/)
                            .map((w) => w.charAt(0).toLocaleUpperCase('tr') + w.slice(1))
                            .join(' ');
                        const slug = slugify(raw);

                        layer.bindTooltip(display, {
                            direction: 'top',
                            sticky: true,
                            className: 'ns-map-tooltip',
                            opacity: 1,
                        });

                        layer.on('mouseover', () => {
                            this.hoverDistrict = { name: display, slug };
                            layer.setStyle({
                                fillColor: '#1bb6ad',
                                fillOpacity: 0.7,
                                weight: 1.4,
                                color: '#083a38',
                            });
                            if (layer.bringToFront) layer.bringToFront();
                        });
                        layer.on('mouseout', () => {
                            this.hoverDistrict = null;
                            this._geoLayer.resetStyle(layer);
                        });
                        layer.on('click', () => this.goToDistrict(slug));
                    },
                }).addTo(this._map);

                // fitBounds — birden fazla zamanlama ile
                const bounds = this._geoLayer.getBounds();
                if (bounds.isValid()) {
                    const doFit = () => {
                        if (!this._map) return;
                        this._map.invalidateSize();
                        this._map.fitBounds(bounds, {
                            padding: [20, 20],
                            animate: false,
                            maxZoom: 13,
                        });
                    };

                    doFit();
                    setTimeout(doFit, 150);
                    setTimeout(() => {
                        doFit();
                        if (this._map) {
                            const z = this._map.getZoom();
                            this._map.setMinZoom(Math.max(4, z - 1));
                            this._map.setMaxBounds(bounds.pad(1.8));
                        }
                    }, 500);
                }
            } catch (err) {
                console.error('[cityMap] Harita yüklenemedi:', err);
                this.error = 'Harita yüklenemedi — aşağıdaki listeden ilçe seçebilirsiniz.';
            } finally {
                this.loading = false;
            }
        },
    };
}
