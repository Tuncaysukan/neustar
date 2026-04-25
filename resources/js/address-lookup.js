/* =============================================================
 *  Türkiye haritası — il seçicisi
 *
 *  Davranış: Kullanıcı haritada bir ile tıklar veya üstteki arama
 *  kutusundan seçer; o ilin sayfasına (`/internet-altyapi/{slug}`)
 *  yönlendirilir. Form alanları bu ekranda yok — detay (ilçe,
 *  mahalle, sokak) il/ilçe sayfalarında toplanıyor.
 *
 *  Veri kaynakları
 *  - /data/tr-provinces.json : 81 il + ilçe katalog snapshot'ı
 *  - /data/tr-cities.json    : il polygon GeoJSON
 * ============================================================= */

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { bindTurkeyMapMobileLifecycle, scheduleTurkeyMapMobileRefits } from './turkey-map-mobile';

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

export default function addressLookup() {
    return {
        // ---- state -----------------------------------------------
        loading: true,
        error: null,
        provinces: [],        // [{ id, name, slug }]
        query: '',            // arama kutusu
        hoverProvince: null,  // hover rozeti için il adı

        // ---- derived ---------------------------------------------
        get filteredProvinces() {
            const q = this.query.trim().toLocaleLowerCase('tr');
            if (!q) return this.provinces;
            return this.provinces.filter((p) =>
                p.name.toLocaleLowerCase('tr').startsWith(q),
            );
        },

        // ---- lifecycle -------------------------------------------
        async init() {
            try {
                const res = await fetch('/data/tr-provinces.json', { cache: 'force-cache' });
                if (!res.ok) throw new Error();
                const payload = await res.json();
                this.provinces = (payload?.data || [])
                    .map((p) => ({ id: p.id, name: p.name, slug: slugify(p.name) }))
                    .sort((a, b) => a.name.localeCompare(b.name, 'tr'));
            } catch {
                this.error = 'İl listesi yüklenemedi, sayfayı yenileyip deneyin.';
            } finally {
                this.loading = false;
            }

            await this.$nextTick();
            this.initMap();
        },

        // ---- navigation -------------------------------------------
        goToProvince(slugOrName) {
            let slug = slugOrName;
            // Eğer isim gelirse slug'a çevirelim
            const match = this.provinces.find(
                (p) =>
                    p.slug === slugOrName ||
                    p.name.toLocaleLowerCase('tr') === String(slugOrName).toLocaleLowerCase('tr'),
            );
            if (match) slug = match.slug;
            if (!slug) return;
            // Direkt tarife sayfasına yönlendir
            window.location.href = `/internet-tarifeleri/ucuz-${slug}-ev-interneti-fiyatlari`;
        },

        submitSearch() {
            const first = this.filteredProvinces[0];
            if (first) this.goToProvince(first.slug);
        },

        // ---- map -------------------------------------------------
        _map: null,
        _geoLayer: null,
        _resizeTimer: null,
        _resizeHandler: null,
        _mapResizeObserver: null,
        _mobileLifecycleOff: null,

        _isNarrowMap() {
            return typeof window !== 'undefined' && window.matchMedia('(max-width: 639.98px)').matches;
        },

        _fitProvinceLayer() {
            if (!this._map || !this._geoLayer) return;
            const el = this.$refs.map;
            if (!el) return;
            this._map.invalidateSize(false);
            const rect = el.getBoundingClientRect();
            let w = Math.max(1, Math.round(rect.width));
            let h = Math.max(1, Math.round(rect.height));
            if (w < 4 || h < 4) {
                w = el.clientWidth;
                h = el.clientHeight;
            }
            if (w < 4 || h < 4) return;

            const narrow = this._isNarrowMap();
            /* Mobil: masaüstüyle aynı “alanı doldur” mantığı; padding mobilde de sıkı */
            const ratio = narrow ? 0.0035 : 0.003;
            const pad = Math.max(2, Math.round(Math.min(w, h) * ratio));
            const bounds = this._geoLayer.getBounds();
            const center = bounds.getCenter();

            this._map.fitBounds(bounds, {
                padding: [pad, pad],
                maxZoom: 9,
                animate: false,
            });
            this._map.panTo(center, { animate: false, noMoveStart: true });

            try {
                const maxB = typeof bounds.pad === 'function' ? bounds.pad(0.12) : bounds;
                this._map.setMaxBounds(maxB);
                // Haritanın sağ/sol kaymasını tamamen engelle
                this._map.options.maxBoundsViscosity = 1.0;
            } catch {}

            this._map.dragging?.disable();
        },

        async initMap() {
            const el = this.$refs.map;
            if (!el || this._map) return;

            const narrow = this._isNarrowMap();

            this._map = L.map(el, {
                zoomControl: false,
                attributionControl: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                dragging: false,
                keyboard: false,
                tap: true,
                touchZoom: false,
                boxZoom: false,
                minZoom: 4,
                maxZoom: 9,
                zoomSnap: 0.25,
                zoomDelta: 0.5,
                maxBoundsViscosity: 1.0,
            });
            this._map.setView([39.0, 35.3], 5);
            el.style.backgroundColor = 'rgb(248 248 248)';

            try {
                const res = await fetch('/data/tr-cities.json', { cache: 'force-cache' });
                if (!res.ok) throw new Error();
                const geojson = await res.json();

                const provinceStyle = {
                    base: {
                        color: '#323a4f',
                        weight: 0.85,
                        fillColor: '#3f475f',
                        fillOpacity: 0.82,
                    },
                    hover: {
                        color: '#1e2433',
                        weight: 1.15,
                        fillColor: '#2a3144',
                        fillOpacity: 0.95,
                    },
                };

                this._geoLayer = L.geoJSON(geojson, {
                    style: () => ({ ...provinceStyle.base }),
                    onEachFeature: (feature, layer) => {
                        const name = feature?.properties?.name || '';

                        layer.bindTooltip(name, {
                            direction: 'top',
                            sticky: true,
                            className: 'ns-map-tooltip',
                            opacity: 1,
                        });

                        const onOver = () => {
                            this.hoverProvince = name;
                            layer.setStyle(provinceStyle.hover);
                            if (layer.bringToFront) layer.bringToFront();
                        };
                        const onOut = () => {
                            this.hoverProvince = null;
                            this._geoLayer.resetStyle(layer);
                        };
                        layer.on('mouseover', onOver);
                        layer.on('mouseout', onOut);
                        if (typeof window !== 'undefined' && window.matchMedia('(hover: none)').matches) {
                            layer.on('touchstart', onOver, { passive: true });
                            layer.on('touchend', onOut);
                            layer.on('touchcancel', onOut);
                        }
                        layer.on('click', () => this.goToProvince(name));
                    },
                }).addTo(this._map);

                const runFit = () => {
                    try {
                        this._fitProvinceLayer();
                    } catch {}
                };
                requestAnimationFrame(() => {
                    runFit();
                    requestAnimationFrame(runFit);
                });
                setTimeout(runFit, 200);
                setTimeout(runFit, 500);

                if (this._isNarrowMap()) {
                    scheduleTurkeyMapMobileRefits(runFit, el);
                    this._mobileLifecycleOff = bindTurkeyMapMobileLifecycle(runFit, el);
                }

                this._resizeHandler = () => {
                    clearTimeout(this._resizeTimer);
                    this._resizeTimer = setTimeout(() => {
                        try {
                            this._fitProvinceLayer();
                        } catch {}
                    }, 120);
                };
                window.addEventListener('resize', this._resizeHandler);

                if (typeof ResizeObserver !== 'undefined') {
                    this._mapResizeObserver = new ResizeObserver(() => {
                        clearTimeout(this._resizeTimer);
                        this._resizeTimer = setTimeout(() => {
                            try {
                                this._fitProvinceLayer();
                            } catch {}
                        }, 80);
                    });
                    this._mapResizeObserver.observe(el);
                }
            } catch {
                this.error = this.error || 'Harita yüklenemedi, il listesinden seç.';
            }
        },
    };
}
