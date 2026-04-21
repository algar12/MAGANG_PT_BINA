import React, { useState, useEffect } from 'react';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { formatTime } from './utils/formatters';

// ── Setup Laravel Echo (hanya sekali, di module level) ────────────────────────
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
 * LiveWeightCard.jsx
 *
 * Menampilkan berat timbangan secara real-time via WebSocket (Laravel Reverb).
 * Subscribe ke channel: scale.{device_id}
 * Event: weight.received
 *
 * Tidak ada polling — data di-push dari server saat ESP32 mengirim berat.
 * Sebagai fallback awal, satu kali fetch dari REST API untuk mendapat nilai terakhir.
 *
 * @param {{ deviceId: string, deviceName: string }} props
 */
export default function LiveWeightCard({ deviceId, deviceName }) {
    const [data, setData]         = useState(null);
    const [connected, setConnected] = useState(false);

    useEffect(() => {
        if (!deviceId) return;

        // ── Fallback: ambil nilai terakhir dari cache via REST (sekali saja) ──
        fetch(`/api/weight-live/${deviceId}`)
            .then(r => r.json())
            .then(json => { if (json.weight !== null) setData(json); })
            .catch(() => {});

        // ── Subscribe ke WebSocket channel ────────────────────────────────────
        const channel = echo.channel(`scale.${deviceId}`);

        channel
            .subscribed(() => setConnected(true))
            .listen('.weight.received', (payload) => {
                setData(payload);   // update state langsung dari push server
            });

        // Cleanup: unsubscribe saat komponen unmount atau device berubah
        return () => {
            echo.leaveChannel(`scale.${deviceId}`);
            setConnected(false);
        };
    }, [deviceId]);

    const hasWeight = data?.weight !== null && data?.weight !== undefined;

    return (
        <div style={{
            background: 'rgba(14,165,233,0.06)',
            border: `1px solid ${connected ? 'rgba(14,165,233,0.35)' : 'rgba(100,116,139,0.25)'}`,
            borderRadius: 16,
            padding: '24px 28px',
            display: 'flex',
            alignItems: 'center',
            gap: 20,
            transition: 'border-color 0.3s',
        }}>
            {/* Icon timbangan */}
            <div style={{
                width: 56, height: 56, borderRadius: 14, flexShrink: 0,
                background: 'rgba(14,165,233,0.15)',
                display: 'flex', alignItems: 'center', justifyContent: 'center',
            }}>
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" strokeWidth="1.8">
                    <path d="M3 9l1-5h16l1 5M3 9h18M3 9l2 12h14l2-12" strokeLinecap="round" strokeLinejoin="round"/>
                    <circle cx="12" cy="15" r="2" fill="#0ea5e9" stroke="none"/>
                </svg>
            </div>

            {/* Info berat */}
            <div style={{ flex: 1 }}>
                <div style={{ fontSize: 12, color: '#64748b', marginBottom: 4 }}>
                    {deviceName || deviceId || 'Timbangan IoT'} — Live Reading
                </div>
                <div style={{
                    fontSize: 32, fontWeight: 800, letterSpacing: -1,
                    color: hasWeight ? '#0ea5e9' : '#475569',
                    transition: 'color 0.3s ease',
                }}>
                    {hasWeight ? `${Number(data.weight).toFixed(3)} KG` : '— KG'}
                </div>
                <div style={{ fontSize: 11, color: '#475569', marginTop: 4 }}>
                    {hasWeight
                        ? `Update: ${formatTime(data.timestamp)}`
                        : 'Menunggu data dari sensor…'}
                </div>
            </div>

            {/* Indikator koneksi WebSocket */}
            <div style={{ textAlign: 'right' }}>
                <div style={{
                    width: 10, height: 10, borderRadius: '50%',
                    background: connected ? '#22c55e' : '#f59e0b',
                    boxShadow: connected ? '0 0 10px #22c55e' : '0 0 8px #f59e0b',
                    marginLeft: 'auto',
                    animation: 'pulse 2s infinite',
                }} />
                <div style={{ fontSize: 9, color: connected ? '#22c55e' : '#f59e0b', marginTop: 4, whiteSpace: 'nowrap' }}>
                    {connected ? 'WS Connected' : 'Connecting…'}
                </div>
            </div>
        </div>
    );
}
