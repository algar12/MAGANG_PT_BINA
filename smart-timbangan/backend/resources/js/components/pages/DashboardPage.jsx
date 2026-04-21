import React, { useState, useEffect } from 'react';
import StatsBar from '../StatsBar';

/**
 * DashboardPage.jsx
 *
 * Halaman ringkasan untuk Operator Timbangan.
 * Menampilkan statistik dari production order yang aktif.
 */
export default function DashboardPage({ userName }) {
    const [orders, setOrders]   = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetch('/api/production-orders/active')
            .then(r => r.json())
            .then(json => { setOrders(json.data ?? []); setLoading(false); })
            .catch(() => setLoading(false));
    }, []);

    return (
        <div style={{ padding: '32px 28px', maxWidth: 900 }}>
            {/* Greeting */}
            <div style={{ marginBottom: 28 }}>
                <h1 style={{ fontSize: 22, fontWeight: 800, marginBottom: 4 }}>
                    Selamat datang, {userName} 👋
                </h1>
                <p style={{ color: '#64748b', fontSize: 14 }}>
                    Berikut ringkasan production order yang sedang aktif.
                </p>
            </div>

            {/* Order cards */}
            {loading ? (
                <LoadingState />
            ) : orders.length === 0 ? (
                <EmptyState />
            ) : (
                <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                    {orders.map(order => (
                        <OrderCard key={order.id} order={order} />
                    ))}
                </div>
            )}
        </div>
    );
}

function OrderCard({ order }) {
    const statusColor = order.status === 'In Progress' ? '#22c55e' : '#f59e0b';
    const statusBg    = order.status === 'In Progress' ? 'rgba(34,197,94,0.1)' : 'rgba(245,158,11,0.1)';

    return (
        <div style={{
            background: 'rgba(255,255,255,0.04)',
            border: '1px solid rgba(255,255,255,0.07)',
            borderRadius: 14, padding: '20px 24px',
            display: 'flex', justifyContent: 'space-between',
            alignItems: 'center', gap: 12, flexWrap: 'wrap',
        }}>
            <div>
                <div style={{ fontWeight: 700, fontSize: 15, marginBottom: 4 }}>
                    {order.formula?.formula_name ?? 'Formula tidak ditemukan'}
                </div>
                <div style={{ fontSize: 12, color: '#64748b' }}>
                    Order #{order.order_number} &nbsp;·&nbsp; Qty: {order.qty_order} batch
                    &nbsp;·&nbsp; {order.start_date}
                </div>
            </div>
            <span style={{
                padding: '5px 14px', borderRadius: 20, fontSize: 12, fontWeight: 600,
                background: statusBg, color: statusColor,
            }}>
                {order.status}
            </span>
        </div>
    );
}

function LoadingState() {
    return <div style={{ color: '#475569', padding: '32px 0' }}>Memuat data…</div>;
}

function EmptyState() {
    return (
        <div style={{
            background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(255,255,255,0.07)',
            borderRadius: 14, padding: '48px', textAlign: 'center', color: '#475569',
        }}>
            Tidak ada production order yang aktif saat ini.
        </div>
    );
}
