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
            window.location.href = `/internet-altyapi/${slug}`;
        },

        submitSearch() {
            const first = this.filteredProvinces[0];
            if (first) this.goToProvince(first.slug);
        },

        // ---- map -------------------------------------------------
        _map: null,
        _geoLayer: null,

        async initMap() {
            const el = this.$refs.map;
            if (!el || this._map) return;

            this._map = L.map(el, {
                zoomControl: false,
                attributionControl: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                dragging: false,         // pure click-to-select; drag gerekmez
                keyboard: false,
                tap: true,
                minZoom: 4,
                maxZoom: 8,
            });
            this._map.setView([39.0, 35.3], 5);

            try {
                const res = await fetch('/data/tr-cities.json', { cache: 'force-cache' });
                if (!res.ok) throw new Error();
                const geojson = await res.json();

                this._geoLayer = L.geoJSON(geojson, {
                    style: () => ({
                        color: '#1bb6ad',
                        weight: 0.8,
                        fillColor: '#d7f1ef',
                        fillOpacity: 0.6,
                    }),
                    onEachFeature: (feature, layer) => {
                        const name = feature?.properties?.name || '';

                        layer.bindTooltip(name, {
                            direction: 'top',
                            sticky: true,
                            className: 'ns-map-tooltip',
                            opacity: 1,
                        });

                        layer.on('mouseover', () => {
                            this.hoverProvince = name;
                            layer.setStyle({
                                fillColor: '#1bb6ad',
                                fillOpacity: 0.85,
                                weight: 1.2,
                                color: '#0b6864',
                            });
                            if (layer.bringToFront) layer.bringToFront();
                        });
                        layer.on('mouseout', () => {
                            this.hoverProvince = null;
                            this._geoLayer.resetStyle(layer);
                        });
                        layer.on('click', () => this.goToProvince(name));
                    },
                }).addTo(this._map);

                requestAnimationFrame(() => {
                    if (!this._map || !this._geoLayer) return;
                    try {
                        this._map.invalidateSize(false);
                        this._map.fitBounds(this._geoLayer.getBounds(), {
                            padding: [12, 12],
                            animate: false,
                        });
                    } catch {}
                });
            } catch {
                this.error = this.error || 'Harita yüklenemedi, il listesinden seç.';
            }
        },
    };
}
