import React from 'react';

/**
 * StatsBar.jsx
 *
 * Menampilkan 4 angka ringkasan dari data costings:
 * - Total bahan dalam BOM
 * - Sudah ditimbang (Weighed / Approved)
 * - Belum ditimbang (Pending)
 * - Progress dalam persen
 *
 * @param {{ costings: Array }} props
 */
export default function StatsBar({ costings }) {
    // Hitung statistik dari array costings
    const total   = costings.length;
    const weighed = costings.filter(c => c.status === 'Weighed' || c.status === 'Approved').length;
    const pending = costings.filter(c => c.status === 'Pending').length;
    const pct     = total > 0 ? Math.round((weighed / total) * 100) : 0;

    const stats = [
        { label: 'Total Bahan',    value: total,       color: '#94a3b8' },
        { label: 'Sudah Timbang',  value: weighed,     color: '#22c55e' },
        { label: 'Belum Timbang',  value: pending,     color: '#f59e0b' },
        { label: 'Progress',       value: `${pct}%`,   color: pct === 100 ? '#22c55e' : '#3b82f6' },
    ];

    return (
        <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(4, 1fr)',
            gap: 12,
            marginBottom: 24,
        }}>
            {stats.map(stat => (
                <StatCard key={stat.label} {...stat} />
            ))}
        </div>
    );
}

/**
 * Satu kotak statistik individual.
 */
function StatCard({ label, value, color }) {
    return (
        <div style={{
            background: 'rgba(255,255,255,0.04)',
            border: '1px solid rgba(255,255,255,0.07)',
            borderRadius: 12,
            padding: '16px 20px',
            textAlign: 'center',
        }}>
            <div style={{ fontSize: 26, fontWeight: 800, color }}>{value}</div>
            <div style={{ fontSize: 11, color: '#64748b', marginTop: 4 }}>{label}</div>
        </div>
    );
}
