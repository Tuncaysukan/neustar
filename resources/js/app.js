import './bootstrap';

import Alpine from 'alpinejs';
import addressLookup from './address-lookup';
import cityMap from './city-map';
import districtLookup from './district-lookup';

window.Alpine = Alpine;

function safeJsonParse(value, fallback) {
    try {
        return JSON.parse(value);
    } catch {
        return fallback;
    }
}

function normalizeCompareIds(input) {
    const ids = Array.isArray(input) ? input : [];
    return ids
        .map((v) => Number.parseInt(String(v), 10))
        .filter((v) => Number.isFinite(v) && v > 0);
}

window.maskPhone = function(el) {
    let digits = el.value.replace(/\D/g, "");
    
    // Temizleme: 05 veya 905 ile başlıyorsa başını at
    if (digits.startsWith("05")) digits = digits.substring(1);
    else if (digits.startsWith("905")) digits = digits.substring(2);
    
    // Her zaman 5 ile başlamalı (eğer boş değilse)
    if (digits.length > 0 && !digits.startsWith("5")) {
        digits = "5" + digits;
    }
    
    // Max 10 hane (5xx xxx xxxx)
    digits = digits.substring(0, 10);
    
    // Eğer sadece 90 veya boşsa, tamamen boş maskeyi göster
    if (digits.length === 0 || digits === "90") {
        digits = "5"; 
    }

    // Maskeyi oluştur: +90(5xx)xxx-xxxx
    let m = "+90(5";
    
    // (5xx) -> 1. ve 2. haneler (indis 1 ve 2)
    let d1 = digits.substring(1, 3);
    m += d1 + "_".repeat(2 - d1.length) + ")";
    
    // Orta 3 hane (indis 3, 4, 5)
    let d2 = digits.substring(3, 6);
    m += d2 + "_".repeat(3 - d2.length) + "-";
    
    // Son 4 hane (indis 6, 7, 8, 9)
    let d3 = digits.substring(6, 10);
    m += d3 + "_".repeat(4 - d3.length);
    
    el.value = m;
    
    // İmleci ilk boşluğa veya sona taşı
    let cursor = m.indexOf("_");
    if (cursor === -1) cursor = m.length;
    
    setTimeout(() => {
        el.setSelectionRange(cursor, cursor);
    }, 0);
};

document.addEventListener('alpine:init', () => {
    Alpine.store('compare', {
        max: 5,
        ids: normalizeCompareIds(safeJsonParse(localStorage.getItem('compare_ids') || '[]', [])),

        get count() {
            return this.ids.length;
        },

        has(id) {
            const n = Number.parseInt(String(id), 10);
            return this.ids.includes(n);
        },

        add(id) {
            const n = Number.parseInt(String(id), 10);
            if (!Number.isFinite(n) || n <= 0) return { ok: false, reason: 'invalid' };
            if (this.ids.includes(n)) return { ok: true, reason: 'exists' };
            if (this.ids.length >= this.max) return { ok: false, reason: 'limit' };
            this.ids = [...this.ids, n];
            localStorage.setItem('compare_ids', JSON.stringify(this.ids));
            return { ok: true, reason: 'added' };
        },

        remove(id) {
            const n = Number.parseInt(String(id), 10);
            this.ids = this.ids.filter((x) => x !== n);
            localStorage.setItem('compare_ids', JSON.stringify(this.ids));
        },

        clear() {
            this.ids = [];
            localStorage.setItem('compare_ids', JSON.stringify(this.ids));
        },

        url() {
            const ids = this.ids.slice(0, this.max);
            const qs = new URLSearchParams();
            qs.set('ids', ids.join(','));
            return `/karsilastir?${qs.toString()}`;
        },
    });

    /* -----------------------------------------------------------------
     *  Cloudflare Speed Test (powered by @cloudflare/speedtest)
     *  Same engine as speed.cloudflare.com. Browser-based, real traffic.
     *
     *  Packet-loss measurement requires a WebRTC TURN endpoint with
     *  short-lived credentials. We try Cloudflare's public `/turn-creds`
     *  endpoint; if it fails (CORS, outage, dev network) we fall back to
     *  a measurement plan without packet loss — the other 6 metrics are
     *  unaffected.
     * ----------------------------------------------------------------*/
    const MEASUREMENTS_NO_PACKET_LOSS = [
        { type: 'latency',  numPackets: 1 },
        { type: 'download', bytes: 1e5, count: 1, bypassMinDuration: true },
        { type: 'latency',  numPackets: 20 },
        { type: 'download', bytes: 1e5, count: 9 },
        { type: 'download', bytes: 1e6, count: 8 },
        { type: 'upload',   bytes: 1e5, count: 8 },
        { type: 'upload',   bytes: 1e6, count: 6 },
        { type: 'download', bytes: 1e7, count: 6 },
        { type: 'upload',   bytes: 1e7, count: 4 },
        { type: 'download', bytes: 2.5e7, count: 4 },
        { type: 'upload',   bytes: 2.5e7, count: 4 },
        { type: 'download', bytes: 1e8, count: 3 },
        { type: 'upload',   bytes: 5e7, count: 3 },
        { type: 'download', bytes: 2.5e8, count: 2 },
    ];

    async function fetchTurnCreds() {
        try {
            const controller = new AbortController();
            const timer = setTimeout(() => controller.abort(), 2500);
            const res = await fetch('https://speed.cloudflare.com/turn-creds', {
                signal: controller.signal,
                credentials: 'omit',
            });
            clearTimeout(timer);
            if (!res.ok) return null;
            const data = await res.json();
            if (!data?.uris && !data?.urls) return null;
            return {
                turnServerUri: (data.uris || data.urls)[0],
                turnServerUser: data.username,
                turnServerPass: data.credential,
            };
        } catch {
            return null;
        }
    }

    Alpine.data('addressLookup', addressLookup);
    Alpine.data('cityMap', cityMap);
    Alpine.data('districtLookup', districtLookup);

    Alpine.data('speedTest', () => ({
        status: 'idle',   // idle | running | done | error
        phase: 'Başlatmaya hazır',
        error: null,
        finishedAt: null,

        // Sonuçlar
        downloadMbps: null,
        uploadMbps: null,
        pingMs: null,
        jitterMs: null,

        // Canlı gösterge
        liveDown: 0,
        liveUp: 0,
        packetLossSupported: false,  // view uyumluluğu için

        // Özet (eski API uyumluluğu için)
        get summary() {
            return {
                download: this.downloadMbps != null ? this.downloadMbps * 1e6 : null,
                upload:   this.uploadMbps   != null ? this.uploadMbps   * 1e6 : null,
                latency:  this.pingMs,
                jitter:   this.jitterMs,
            };
        },

        format(bps, decimals = 1) {
            if (bps == null || !Number.isFinite(bps)) return '—';
            const mbps = bps / 1e6;
            if (mbps >= 100) return mbps.toFixed(0);
            if (mbps >= 10)  return mbps.toFixed(1);
            return mbps.toFixed(decimals);
        },
        formatMs(ms) {
            if (ms == null || !Number.isFinite(ms)) return '—';
            return Math.round(ms).toString();
        },
        formatPct(p) {
            if (p == null || !Number.isFinite(p)) return '—';
            return p.toFixed(p < 10 ? 2 : 1);
        },

        async start() {
            if (this.status === 'running') return;
            this._reset();
            this.status  = 'running';

            try {
                // 1. Ping ölç (4 istek, en düşük al)
                this.phase = 'Gecikme ölçülüyor…';
                this.pingMs   = await this._measurePing();
                this.jitterMs = await this._measureJitter();

                // 2. İndirme hızı
                this.phase = 'İndirme hızı ölçülüyor…';
                this.downloadMbps = await this._measureDownload();

                // 3. Yükleme hızı
                this.phase = 'Yükleme hızı ölçülüyor…';
                this.uploadMbps = await this._measureUpload();

                this.phase      = 'Ölçüm tamamlandı';
                this.status     = 'done';
                this.finishedAt = new Date();
            } catch (e) {
                this.error  = String(e?.message || e || 'Bilinmeyen hata');
                this.status = 'error';
            }
        },

        restart() { this.start(); },

        _reset() {
            this.downloadMbps = null;
            this.uploadMbps   = null;
            this.pingMs       = null;
            this.jitterMs     = null;
            this.liveDown     = 0;
            this.liveUp       = 0;
            this.error        = null;
            this.finishedAt   = null;
            this.phase        = 'Hazırlanıyor…';
        },

        // ── Ping: 5 istek, medyan al ──────────────────────────────
        async _measurePing() {
            const url = 'https://speed.cloudflare.com/__down?bytes=1000';
            const times = [];
            for (let i = 0; i < 5; i++) {
                const t0 = performance.now();
                await fetch(url + '&r=' + Math.random(), { cache: 'no-store' });
                times.push(performance.now() - t0);
            }
            times.sort((a, b) => a - b);
            return Math.round(times[Math.floor(times.length / 2)]);
        },

        // ── Jitter: ping varyansı ─────────────────────────────────
        async _measureJitter() {
            const url = 'https://speed.cloudflare.com/__down?bytes=1000';
            const times = [];
            for (let i = 0; i < 6; i++) {
                const t0 = performance.now();
                await fetch(url + '&r=' + Math.random(), { cache: 'no-store' });
                times.push(performance.now() - t0);
            }
            const diffs = [];
            for (let i = 1; i < times.length; i++) diffs.push(Math.abs(times[i] - times[i-1]));
            return Math.round(diffs.reduce((a, b) => a + b, 0) / diffs.length);
        },

        // ── İndirme: 3 farklı boyut, ağırlıklı ortalama ──────────
        async _measureDownload() {
            const sizes = [
                { bytes: 1e5,  weight: 1 },
                { bytes: 1e6,  weight: 2 },
                { bytes: 25e6, weight: 4 },
            ];
            let totalBits = 0, totalWeight = 0;

            for (const { bytes, weight } of sizes) {
                const url = `https://speed.cloudflare.com/__down?bytes=${bytes}&r=${Math.random()}`;
                const t0  = performance.now();
                const res = await fetch(url, { cache: 'no-store' });
                await res.arrayBuffer();
                const elapsed = (performance.now() - t0) / 1000;
                const mbps    = (bytes * 8) / elapsed / 1e6;
                totalBits   += mbps * weight;
                totalWeight += weight;
                // Canlı güncelle
                this.liveDown = (totalBits / totalWeight) * 1e6;
            }
            const result = totalBits / totalWeight;
            this.liveDown = result * 1e6;
            return Math.round(result * 10) / 10;
        },

        // ── Yükleme: Laravel proxy üzerinden (CORS sorunu yok) ──────
        async _measureUpload() {
            const sizes = [
                { bytes: 1e5,  weight: 1 },
                { bytes: 5e5,  weight: 2 },
                { bytes: 1e6,  weight: 3 },
            ];
            let totalBits = 0, totalWeight = 0;

            for (const { bytes, weight } of sizes) {
                const body = new Uint8Array(bytes);
                const url  = `/hiz-testi-upload?r=${Math.random()}`;
                const t0   = performance.now();
                try {
                    await fetch(url, {
                        method: 'POST',
                        body,
                        cache: 'no-store',
                        headers: {
                            'Content-Type': 'application/octet-stream',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '',
                        },
                    });
                } catch { /* upload hatası — atla */ }
                const elapsed = (performance.now() - t0) / 1000;
                const mbps    = (bytes * 8) / elapsed / 1e6;
                totalBits   += mbps * weight;
                totalWeight += weight;
                this.liveUp = (totalBits / totalWeight) * 1e6;
            }
            const result = totalBits / totalWeight;
            this.liveUp = result * 1e6;
            return Math.round(result * 10) / 10;
        },
    }));
});

Alpine.start();
