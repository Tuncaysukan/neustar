import './bootstrap';

import Alpine from 'alpinejs';
import SpeedTest from '@cloudflare/speedtest';
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
        status: 'idle',
        phase: 'Başlatmaya hazır',
        summary: {},
        liveDown: 0,
        liveUp: 0,
        error: null,
        finishedAt: null,
        packetLossSupported: false,
        _engine: null,

        phaseLabels: {
            latency: 'Gecikme ölçülüyor',
            download: 'İndirme hızı ölçülüyor',
            upload: 'Yükleme hızı ölçülüyor',
            packetLoss: 'Paket kaybı ölçülüyor',
        },

        format(bps, decimals = 1) {
            if (bps == null || !Number.isFinite(bps)) return '—';
            const mbps = bps / 1e6;
            if (mbps >= 100) return mbps.toFixed(0);
            if (mbps >= 10) return mbps.toFixed(1);
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
            this.status = 'running';
            this.phase = 'Hazırlanıyor…';

            // Try to get TURN creds so we can include packet-loss; fall back
            // to a measurement plan without it when the endpoint is unreachable.
            const turnCreds = await fetchTurnCreds();
            const config = { autoStart: false };
            if (turnCreds) {
                Object.assign(config, turnCreds);
                this.packetLossSupported = true;
            } else {
                config.measurements = MEASUREMENTS_NO_PACKET_LOSS;
                this.packetLossSupported = false;
            }

            let engine;
            try {
                engine = new SpeedTest(config);
            } catch (e) {
                this.error = 'Tarayıcınız bu ölçümü desteklemiyor olabilir.';
                this.status = 'error';
                return;
            }
            this._engine = engine;

            engine.onPhaseChange = ({ measurement }) => {
                const label = this.phaseLabels[measurement?.type];
                if (label) this.phase = label + '…';
            };

            engine.onResultsChange = () => {
                this.summary = { ...engine.results.getSummary() };
                const dl = engine.results.getDownloadBandwidth();
                const up = engine.results.getUploadBandwidth();
                if (typeof dl === 'number') this.liveDown = dl;
                if (typeof up === 'number') this.liveUp = up;
            };

            engine.onFinish = (results) => {
                this.summary = results.getSummary();
                this.phase = 'Ölçüm tamamlandı';
                this.status = 'done';
                this.finishedAt = new Date();
            };

            engine.onError = (err) => {
                const msg = String(err?.message || err || '');
                // Swallow packet-loss specific errors — they shouldn't kill the run.
                if (/packet\s*loss|turnServerUser/i.test(msg)) {
                    this.packetLossSupported = false;
                    return;
                }
                this.error = msg || 'Bilinmeyen bir hata oluştu.';
                this.status = 'error';
            };

            try {
                engine.play();
            } catch (e) {
                this.error = String(e?.message || e);
                this.status = 'error';
            }
        },

        restart() {
            if (this._engine) {
                try { this._engine.pause(); } catch {}
            }
            this.start();
        },

        _reset() {
            this.summary = {};
            this.liveDown = 0;
            this.liveUp = 0;
            this.phase = 'Başlatmaya hazır';
            this.error = null;
            this.finishedAt = null;
        },
    }));
});

Alpine.start();
