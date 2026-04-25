/**
 * Ana sayfa Türkiye haritası — sadece mobil iyileştirmeleri.
 * Adres çubuğu / visualViewport / orientation / BFCache sonrası Leaflet
 * boyutu yanlış kalmasın; fit tekrar çağrılır.
 */

/**
 * @param {() => void} fitFn — invalidateSize + fitBounds yapan fonksiyon
 * @param {HTMLElement} mapEl — Leaflet kök elementi
 */
export function scheduleTurkeyMapMobileRefits(fitFn, mapEl) {
    if (typeof window === 'undefined' || !mapEl) return;

    const run = () => {
        try {
            fitFn();
        } catch {
            /* ignore */
        }
    };

    const delays = [0, 80, 200, 450, 800, 1400];
    delays.forEach((ms) => {
        window.setTimeout(() => {
            if (!mapEl.isConnected) return;
            run();
        }, ms);
    });
}

/**
 * @param {() => void} fitFn
 * @param {HTMLElement} mapEl
 * @returns {() => void} cleanup
 */
export function bindTurkeyMapMobileLifecycle(fitFn, mapEl) {
    if (typeof window === 'undefined' || !mapEl) {
        return () => {};
    }

    let t = null;
    const debounced = () => {
        window.clearTimeout(t);
        t = window.setTimeout(() => {
            try {
                fitFn();
            } catch {
                /* ignore */
            }
        }, 60);
    };

    const vv = window.visualViewport;
    if (vv) {
        vv.addEventListener('resize', debounced, { passive: true });
        vv.addEventListener('scroll', debounced, { passive: true });
    }

    const onOrientation = () => {
        window.setTimeout(() => {
            try {
                fitFn();
            } catch {
                /* ignore */
            }
        }, 280);
    };

    const onPageShow = (e) => {
        if (e.persisted) {
            window.setTimeout(() => {
                try {
                    fitFn();
                } catch {
                    /* ignore */
                }
            }, 100);
        }
    };

    window.addEventListener('orientationchange', onOrientation);
    window.addEventListener('pageshow', onPageShow);

    return () => {
        window.clearTimeout(t);
        window.removeEventListener('orientationchange', onOrientation);
        window.removeEventListener('pageshow', onPageShow);
        if (vv) {
            vv.removeEventListener('resize', debounced);
            vv.removeEventListener('scroll', debounced);
        }
    };
}
