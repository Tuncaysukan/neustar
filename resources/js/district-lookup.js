/* =============================================================
 *  İlçe sayfası — DSMART (telkotürk backend) drill-down
 *
 *  Akış: Mahalle → Sokak → Bina → Daire → altyapı sorgusu
 *  Tüm veriler Laravel proxy üzerinden gelir:
 *    /api/adres/mahalleler/{citySlug}/{districtSlug}
 *    /api/adres/sokaklar/{mahalleKod}
 *    /api/adres/binalar/{csbmKod}
 *    /api/adres/daireler/{binaKodu}
 * ============================================================= */

export default function districtLookup(config = {}) {
    return {
        // ---- Konfigürasyon ----
        provinceName: String(config.provinceName || ''),
        districtName: String(config.districtName || ''),
        provinceSlug: String(config.provinceSlug || ''),
        districtSlug: String(config.districtSlug || ''),

        // ---- Wizard state ----
        step: 'neighborhood', // neighborhood | street | building | door | result

        // Her adımın listesi + yükleniyor + arama
        nItems: [],  nLoading: false,  nError: null,  nSearch: '',
        sItems: [],  sLoading: false,  sError: null,  sSearch: '',
        bItems: [],  bLoading: false,  bError: null,  bSearch: '',
        dItems: [],  dLoading: false,  dError: null,  dSearch: '',

        // Seçimler — { id, name, bbkCode? }
        selection: {
            neighborhood: null,
            street: null,
            building: null,
            door: null,
        },

        // Altyapı sorgusu
        submitting: false,
        result: null,
        submitError: null,

        // Lead başvuru
        leadSubmitting: false,
        leadError: null,
        leadDone: false,
        leadDoneMessage: null,
        leadForm: { fullName: '', phone: '+90(5__)___-____', hp: '' },

        // =========================================================
        //  init
        // =========================================================

        async init() {
            await this.fetchNeighborhoods();
        },

        _csrf() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        },

        async _fetchJSON(url) {
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!res.ok) throw new Error(`status-${res.status}`);
            return res.json();
        },

        _filter(items, q) {
            const key = String(q || '').trim().toLocaleLowerCase('tr');
            if (!key) return items;
            return items.filter((x) =>
                (x.name || '').toLocaleLowerCase('tr').includes(key),
            );
        },

        // =========================================================
        //  Step 1 — Mahalle
        // =========================================================

        async fetchNeighborhoods() {
            this.nLoading = true; this.nError = null; this.nItems = [];
            try {
                const payload = await this._fetchJSON(
                    `/api/adres/mahalleler/${encodeURIComponent(this.provinceSlug)}/${encodeURIComponent(this.districtSlug)}`,
                );
                if (!payload?.ok) {
                    this.nError = payload?.message || 'Mahalleler yüklenemedi.';
                    return;
                }
                this.nItems = Array.isArray(payload.items) ? payload.items : [];
                if (this.nItems.length === 0) this.nError = 'Bu ilçe için mahalle listesi bulunamadı.';
            } catch {
                this.nError = 'Adres servisine ulaşılamadı.';
            } finally {
                this.nLoading = false;
            }
        },
        get filteredNeighborhoods() { return this._filter(this.nItems, this.nSearch); },

        async selectNeighborhood(item) {
            this.selection.neighborhood = { id: item.id, name: item.name };
            this.step = 'street';
            this._scrollTop();
            await this.fetchStreets(item.id);
        },

        // =========================================================
        //  Step 2 — Sokak
        // =========================================================

        async fetchStreets(mahalleKod) {
            this.sLoading = true; this.sError = null; this.sItems = []; this.sSearch = '';
            try {
                const payload = await this._fetchJSON(`/api/adres/sokaklar/${encodeURIComponent(mahalleKod)}`);
                this.sItems = Array.isArray(payload?.items) ? payload.items : [];
                if (this.sItems.length === 0) this.sError = 'Bu mahalle için sokak bilgisi bulunamadı.';
            } catch {
                this.sError = 'Sokak listesi alınamadı.';
            } finally {
                this.sLoading = false;
            }
        },
        get filteredStreets() { return this._filter(this.sItems, this.sSearch); },

        async selectStreet(item) {
            this.selection.street = { id: item.id, name: item.name };
            this.step = 'building';
            this._scrollTop();
            await this.fetchBuildings(item.id);
        },

        // =========================================================
        //  Step 3 — Bina
        // =========================================================

        async fetchBuildings(csbmKod) {
            this.bLoading = true; this.bError = null; this.bItems = []; this.bSearch = '';
            try {
                const payload = await this._fetchJSON(`/api/adres/binalar/${encodeURIComponent(csbmKod)}`);
                this.bItems = Array.isArray(payload?.items) ? payload.items : [];
                if (this.bItems.length === 0) this.bError = 'Bu sokak için bina bilgisi bulunamadı.';
            } catch {
                this.bError = 'Bina listesi alınamadı.';
            } finally {
                this.bLoading = false;
            }
        },
        get filteredBuildings() { return this._filter(this.bItems, this.bSearch); },

        async selectBuilding(item) {
            this.selection.building = {
                id: item.id,
                name: item.name,
                bbkCode: item.bbkCode || item.id,
            };
            this.step = 'door';
            this._scrollTop();
            await this.fetchDoors(item.id);
        },

        // =========================================================
        //  Step 4 — Daire
        // =========================================================

        async fetchDoors(binaKodu) {
            this.dLoading = true; this.dError = null; this.dItems = []; this.dSearch = '';
            try {
                const payload = await this._fetchJSON(`/api/adres/daireler/${encodeURIComponent(binaKodu)}`);
                this.dItems = Array.isArray(payload?.items) ? payload.items : [];
                // Daire opsiyonel — boşsa direkt sorgu yapılabilsin
            } catch {
                this.dError = 'Daire listesi alınamadı.';
            } finally {
                this.dLoading = false;
            }
        },
        get filteredDoors() { return this._filter(this.dItems, this.dSearch); },

        async selectDoor(item) {
            this.selection.door = {
                id: item.id,
                name: item.name,
                bbkCode: item.bbkCode || item.id,
            };
            await this.runLookup();
        },

        async skipDoor() {
            this.selection.door = null;
            await this.runLookup();
        },

        // =========================================================
        //  Altyapı sorgusu
        // =========================================================

        async runLookup() {
            if (this.submitting) return;
            this.submitting = true;
            this.submitError = null;
            this.result = null;

            try {
                const res = await fetch('/altyapi-sorgu', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this._csrf(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        city:         this.provinceName,
                        district:     this.districtName,
                        neighborhood: this.selection.neighborhood?.name || '',
                        street:       this.selection.street?.name || '',
                        no:           this.selection.building?.name || '',
                    }),
                });
                if (!res.ok) throw new Error(`status-${res.status}`);
                const payload = await res.json();
                if (payload?.ok === false) {
                    this.submitError = payload.message || 'Sorgu tamamlanamadı.';
                    return;
                }
                this.result = payload;
                this.step = 'result';
                this.$nextTick(() => this._scrollTop());
            } catch {
                this.submitError = 'Servise ulaşılamadı, lütfen tekrar dene.';
            } finally {
                this.submitting = false;
            }
        },

        // =========================================================
        //  Breadcrumb / reset
        // =========================================================

        goToStep(target) {
            const order = ['neighborhood', 'street', 'building', 'door', 'result'];
            const from = order.indexOf(this.step);
            const to   = order.indexOf(target);
            if (to < 0 || to > from) return;

            if (to <= 0) { this.selection.neighborhood = null; this.sItems = []; }
            if (to <= 1) { this.selection.street = null;       this.bItems = []; }
            if (to <= 2) { this.selection.building = null;     this.dItems = []; }
            if (to <= 3) {
                this.selection.door = null;
                this.result = null;
                this.leadDone = false;
                this.leadError = null;
            }
            this.step = target;
            this._scrollTop();
        },

        resetAll() {
            this.selection = { neighborhood: null, street: null, building: null, door: null };
            this.sItems = []; this.bItems = []; this.dItems = [];
            this.result = null;
            this.submitError = null;
            this.leadDone = false;
            this.leadDoneMessage = null;
            this.leadError = null;
            this.step = 'neighborhood';
            this._scrollTop();
        },

        _scrollTop() {
            this.$nextTick(() => {
                const node = document.getElementById('ns-wizard');
                if (node) node.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        },

        // =========================================================
        //  Lead formu
        // =========================================================

        get canSubmitLead() {
            const name  = (this.leadForm.fullName || '').trim();
            const phone = (this.leadForm.phone || '').replace(/\s+/g, '');
            return name.length >= 3 && name.includes(' ') && /^[0-9+()\-]{10,}$/.test(phone);
        },

        async submitLead() {
            if (!this.canSubmitLead || this.leadSubmitting) return;
            this.leadSubmitting = true;
            this.leadError = null;

            try {
                const res = await fetch('/altyapi-basvuru', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this._csrf(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        full_name:    this.leadForm.fullName.trim(),
                        phone:        this.leadForm.phone.trim(),
                        city:         this.provinceName,
                        district:     this.districtName,
                        neighborhood: this.selection.neighborhood?.name || '',
                        street:       this.selection.street?.name || '',
                        building_no:  this.selection.building?.name || '',
                        lookup:       {
                            ...this.result,
                            bbk: this.selection.door?.bbkCode || this.selection.building?.bbkCode || null,
                            door: this.selection.door?.name || null,
                        },
                        hp: this.leadForm.hp,
                    }),
                });
                if (res.status === 422) {
                    const errJson = await res.json().catch(() => null);
                    const first = errJson?.errors ? Object.values(errJson.errors)[0]?.[0] : null;
                    this.leadError = first || 'Lütfen bilgilerini kontrol et.';
                    return;
                }
                if (!res.ok) throw new Error(`status-${res.status}`);
                const payload = await res.json();
                this.leadDone = true;
                this.leadDoneMessage = payload?.message || 'Başvurun alındı, en kısa sürede arayacağız.';
            } catch {
                this.leadError = 'Servise ulaşılamadı, lütfen tekrar dene.';
            } finally {
                this.leadSubmitting = false;
            }
        },

        // =========================================================
        //  UI helpers
        // =========================================================

        statusLabel(key) {
            return ({
                available: 'Hizmet var',
                partial:   'Kısmi — port kontrol',
                limited:   'Sınırlı',
                unavailable: 'Yok',
            })[key] || key;
        },
        statusBadge(key) {
            return ({
                available: 'badge-success',
                partial:   'badge-warning',
                limited:   'badge-ghost',
                unavailable: 'badge-error',
            })[key] || 'badge-ghost';
        },
    };
}
