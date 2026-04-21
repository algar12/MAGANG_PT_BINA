import React, { useState, useEffect, useCallback } from 'react';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import LiveWeightCard from '../LiveWeightCard';
import StatsBar from '../StatsBar';
import BomTable from '../BomTable';

// ── Setup Echo (reuse instance yang sama) ─────────────────────────────────────
window.Pusher = Pusher;
const echo = new Echo({
    broadcaster:  'reverb',
    key:          import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:       import.meta.env.VITE_REVERB_HOST,
    wsPort:       import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort:      import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS:     (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});

/**
 * MulaiMenimbangPage.jsx
 *
 * Halaman penimbangan real-time.
 * Menggunakan WebSocket (Laravel Reverb) untuk menerima update BOM
 * saat ESP32 mengirim data berat — tanpa polling ke server.
 *
 * Alur data:
 *   ESP32 → POST /api/sensor/weight → DeviceController
 *        → broadcast(CostingUpdated) → WebSocket Channel production.{id}
 *        → React menerima event → update state costings
 */
export default function MulaiMenimbangPage({ defaultOrderId, deviceId, deviceName }) {
    const [orders, setOrders]     = useState([]);
    const [orderId, setOrderId]   = useState(defaultOrderId || '');
    const [costings, setCostings] = useState([]);
    const [lastUpdate, setLastUpdate] = useState(null);

    // Ambil daftar order aktif (sekali saja)
    useEffect(() => {
        fetch('/api/production-orders/active')
            .then(r => r.json())
            .then(json => {
                const list = json.data ?? [];
                setOrders(list);
                if (!orderId && list.length > 0) setOrderId(String(list[0].id));
            })
            .catch(() => {});
    }, []);

    // Ambil data costings awal via REST (sekali saat order dipilih)
    const loadInitialCostings = useCallback(async () => {
        if (!orderId) return;
        try {
            const res  = await fetch(`/api/costing-live/${orderId}`);
            const json = await res.json();
            setCostings(json.costings ?? []);
            setLastUpdate(new Date());
        } catch { /* ignore */ }
    }, [orderId]);

    // Subscribe ke WebSocket channel untuk update real-time
    useEffect(() => {
        if (!orderId) return;

        // Load data awal dulu (satu kali)
        setCostings([]);
        loadInitialCostings();

        // Subscribe ke channel production.{order_id}
        const channel = echo.channel(`production.${orderId}`);

        channel.listen('.costing.updated', (payload) => {
            // Ketika ada bahan yang ditimbang, update hanya baris yang berubah
            setCostings(prev =>
                prev.map(c => c.id === payload.costing.id
                    ? { ...c, ...payload.costing }   // merge field yang berubah
                    : c
                )
            );
            setLastUpdate(new Date());
        });

        // Cleanup saat order berubah atau unmount
        return () => echo.leaveChannel(`production.${orderId}`);
    }, [orderId, loadInitialCostings]);

    return (
        <div style={{ padding: '28px', maxWidth: 1050 }}>

            {/* Header halaman */}
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 20, fontWeight: 800, marginBottom: 6 }}>
                    Mulai Menimbang
                </h1>
                <p style={{ color: '#64748b', fontSize: 13 }}>
                    Data timbangan dikirim langsung via WebSocket — tanpa refresh halaman.
                </p>
            </div>

            {/* Pilih Production Order */}
            <OrderSelector orders={orders} value={orderId} onChange={setOrderId} />

            {/* Live Weight via WebSocket */}
            {deviceId && (
                <div style={{ marginBottom: 20 }}>
                    <LiveWeightCard deviceId={deviceId} deviceName={deviceName} />
                </div>
            )}

            {/* Stats ringkasan */}
            {costings.length > 0 && <StatsBar costings={costings} />}

            {/* BOM Table */}
            <div style={{
                background: 'rgba(255,255,255,0.03)',
                border: '1px solid rgba(255,255,255,0.07)',
                borderRadius: 16, overflow: 'hidden',
            }}>
                <div style={{
                    padding: '16px 22px',
                    borderBottom: '1px solid rgba(255,255,255,0.07)',
                    display: 'flex', alignItems: 'center', justifyContent: 'space-between',
                }}>
                    <div style={{ fontWeight: 700, fontSize: 14 }}>Daftar BOM Items</div>
                    <WsStatus lastUpdate={lastUpdate} orderId={orderId} />
                </div>
                <BomTable costings={costings} />
            </div>
        </div>
    );
}

/** Dropdown pilih production order */
function OrderSelector({ orders, value, onChange }) {
    return (
        <div style={{ marginBottom: 20 }}>
            <label style={{ display: 'block', fontSize: 12, color: '#64748b', marginBottom: 8, fontWeight: 600 }}>
                PRODUCTION ORDER
            </label>
            <select
                value={value}
                onChange={e => onChange(e.target.value)}
                style={{
                    width: '100%', maxWidth: 420, padding: '10px 14px',
                    background: 'rgba(255,255,255,0.06)',
                    border: '1px solid rgba(255,255,255,0.1)',
                    borderRadius: 10, color: '#e2e8f0',
                    fontSize: 14, fontFamily: 'inherit',
                    cursor: 'pointer', outline: 'none', appearance: 'none',
                }}
            >
                <option value="" style={{ background: '#1e293b' }}>— Pilih Order —</option>
                {orders.map(o => (
                    <option key={o.id} value={o.id} style={{ background: '#1e293b' }}>
                        #{o.order_number} — {o.formula?.formula_name ?? 'Formula'} ({o.status})
                    </option>
                ))}
            </select>
        </div>
    );
}

/** Badge status WebSocket + waktu update terakhir */
function WsStatus({ lastUpdate, orderId }) {
    if (!orderId) return null;
    return (
        <div style={{ display: 'flex', alignItems: 'center', gap: 6, fontSize: 11 }}>
            <span style={{
                width: 6, height: 6, borderRadius: '50%',
                background: '#22c55e', boxShadow: '0 0 6px #22c55e',
                display: 'inline-block', animation: 'pulse 2s infinite',
            }} />
            <span style={{ color: '#22c55e' }}>WebSocket</span>
            {lastUpdate && (
                <span style={{ color: '#475569' }}>
                    · Update {lastUpdate.toLocaleTimeString('id-ID')}
                </span>
            )}
        </div>
    );
}
