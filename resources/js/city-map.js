/* =============================================================
 *  İl-içi harita — ilçe polygonları
 *
 *  Blade: <div x-data="cityMap({
 *              provinceSlug: '...',
 *              districtsGeoJsonUrl: '/data/districts/...geojson'
 *          })"> ... <div x-ref="map"></div> ... </div>
 *
 *  Davranış:
 *  - GeoJSON'u indir → Leaflet polygon katmanı çiz
 *  - Hover'da polygon vurgulanır, tooltipte ilçe adı görünür
 *  - Click'te /internet-altyapi/{il}/{ilce} sayfasına yönlendirir
 *  - Altta hafif tile (light/dark temaya göre) coğrafi bağlam verir
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
    // DaisyUI tema root <html data-theme="..."> üzerinden okunur.
    const theme = document.documentElement.getAttribute('data-theme') || '';
    const dark = /dark/i.test(theme);
    // CartoDB — atıf gerekli, rate-limit yumuşak, ücretsiz.
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
        hoverDistrict: null,  // { name, slug } | null

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

        // ---- navigation -----------------------------------------
        goToDistrict(nameOrSlug) {
            const slug = slugify(nameOrSlug);
            if (!slug || !this.provinceSlug) return;
            window.location.href = `/internet-altyapi/${this.provinceSlug}/${slug}`;
        },

        // ---- map ------------------------------------------------
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
                // Kullanıcı dünyaya zoom-out edemesin; Türkiye + ilçe detayı aralığı
                minZoom: 4,
                maxZoom: 13,
                worldCopyJump: false,
                maxBoundsViscosity: 0.9,
                // Fractional zoom → fitBounds il sınırını kesin sığdırır,
                // integer zoom'un yarattığı "bir tık fazla yakın" sorunu biter.
                zoomSnap: 0.25,
                zoomDelta: 0.5,
                wheelPxPerZoomLevel: 80,
            });

            // Layer eklemeden ÖNCE center/zoom olmak zorunda,
            // yoksa Leaflet default (0,0 z0) kalır ve dünya görünür.
            this._map.setView([39.0, 35.3], 6);

            // Arka plan tile — çok silik, coğrafi bağlam için
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
                const res = await fetch(this.districtsGeoJsonUrl, { cache: 'force-cache' });
                if (!res.ok) throw new Error();
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
                        // GeoJSON'daki isimler lowercase tr; görsel etiket için Title Case yap
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

                // x-cloak / transition nedeniyle map container yükseklik 0
                // iken initMap çağrılmış olabilir. İki frame + setTimeout ile
                // gerçek ölçüyü aldıktan sonra fit et; ayrıca bounds'ı maxBounds
                // olarak da ata ki kullanıcı il sınırından dışarı kayamasın.
                const finalize = () => {
                    if (!this._map || !this._geoLayer) return;
                    try {
                        const bounds = this._geoLayer.getBounds();
                        if (!bounds.isValid()) return;
                        this._map.invalidateSize();

                        // Polygon'u ekranın ~%40'ına sıkıştırmak için:
                        //   - geniş padding
                        //   - fitZoom'dan 2.5 kademe kırp (zoomSnap: 0.25 → fractional OK)
                        const size = this._map.getSize();
                        const pad  = [
                            Math.max(64, Math.round(size.x * 0.38)),
                            Math.max(64, Math.round(size.y * 0.38)),
                        ];

                        const center     = bounds.getCenter();
                        const fitZoom    = this._map.getBoundsZoom(bounds, false, pad);
                        const targetZoom = Math.max(4, fitZoom - 3.5);

                        this._map.setView(center, targetZoom, { animate: false });

                        // Kullanıcı bir tık daha uzaklaştırabilsin, ama Türkiye'yi
                        // komple görecek kadar değil.
                        this._map.setMinZoom(Math.max(4, targetZoom - 0.5));
                        this._map.setMaxBounds(bounds.pad(1.8));
                    } catch {}
                };
                requestAnimationFrame(() =>
                    requestAnimationFrame(() => setTimeout(finalize, 30)),
                );
            } catch {
                this.error = 'Harita yüklenemedi — aşağıdaki listeden ilçe seçebilirsiniz.';
            } finally {
                this.loading = false;
            }
        },
    };
}
