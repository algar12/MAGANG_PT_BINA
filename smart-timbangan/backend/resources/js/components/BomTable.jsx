import React from 'react';
import StatusBadge from './StatusBadge';
import { formatWeight, formatCurrency, formatDateTime } from './utils/formatters';

/**
 * BomTable.jsx
 *
 * Menampilkan tabel daftar BOM items dari Production Order.
 * Data diperbarui secara real-time oleh parent (OperatorApp)
 * melalui polling setiap 2 detik.
 *
 * Baris yang sudah ditimbang (Weighed/Approved) akan
 * diberi highlight hijau tipis.
 *
 * @param {{ costings: Array }} props
 */
export default function BomTable({ costings }) {
    if (costings.length === 0) {
        return <EmptyState />;
    }

    return (
        <div style={{ overflowX: 'auto' }}>
            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                <TableHeader />
                <tbody>
                    {costings.map((costing, index) => (
                        <BomRow key={costing.id} costing={costing} index={index} />
                    ))}
                </tbody>
            </table>
        </div>
    );
}

/** Header kolom tabel */
function TableHeader() {
    const columns = ['#', 'Bahan / Material', 'Mix ID', 'Netto Target', 'Netto Produksi', 'Sub Cost', 'Status', 'Waktu Timbang'];
    return (
        <thead>
            <tr style={{ background: 'rgba(255,255,255,0.04)' }}>
                {columns.map(col => (
                    <th key={col} style={{
                        padding: '12px 14px',
                        textAlign: 'left',
                        color: '#64748b',
                        fontWeight: 600,
                        fontSize: 11,
                        textTransform: 'uppercase',
                        letterSpacing: '0.5px',
                        borderBottom: '1px solid rgba(255,255,255,0.07)',
                    }}>
                        {col}
                    </th>
                ))}
            </tr>
        </thead>
    );
}

/** Satu baris data BOM item */
function BomRow({ costing, index }) {
    const isDone = costing.status === 'Weighed' || costing.status === 'Approved';

    return (
        <tr style={{
            borderBottom: '1px solid rgba(255,255,255,0.05)',
            background: isDone ? 'rgba(34,197,94,0.03)' : 'transparent',
            transition: 'background 0.4s ease',
        }}>
            {/* No */}
            <td style={{ padding: '14px 14px', color: '#475569' }}>{index + 1}</td>

            {/* Nama & kode material */}
            <td style={{ padding: '14px 14px' }}>
                <div style={{ fontWeight: 600, color: '#e2e8f0' }}>
                    {costing.bom_item?.material?.nama_produk ?? `BOM #${costing.bom_item_id}`}
                </div>
                <div style={{ fontSize: 11, color: '#475569', marginTop: 2 }}>
                    {costing.bom_item?.material?.kode_produk ?? ''}
                </div>
            </td>

            {/* Mix ID */}
            <td style={{ padding: '14px 14px', color: '#64748b', fontSize: 12 }}>
                {costing.bom_item?.mix_id ?? '—'}
            </td>

            {/* Netto Target */}
            <td style={{ padding: '14px 14px', color: '#94a3b8' }}>
                {formatWeight(costing.netto_target)}
            </td>

            {/* Netto Produksi (hasil timbang aktual) */}
            <td style={{
                padding: '14px 14px',
                color: costing.netto_produksi !== null ? '#60a5fa' : '#475569',
                fontWeight: costing.netto_produksi !== null ? 700 : 400,
            }}>
                {formatWeight(costing.netto_produksi)}
            </td>

            {/* Sub Cost Price */}
            <td style={{ padding: '14px 14px', color: '#94a3b8' }}>
                {formatCurrency(costing.sub_cost_price)}
            </td>

            {/* Status badge */}
            <td style={{ padding: '14px 14px' }}>
                <StatusBadge status={costing.status} />
            </td>

            {/* Waktu timbang */}
            <td style={{ padding: '14px 14px', color: '#475569', fontSize: 11 }}>
                {formatDateTime(costing.weighed_at)}
            </td>
        </tr>
    );
}

/** Tampilan jika tidak ada data */
function EmptyState() {
    return (
        <div style={{ textAlign: 'center', padding: '48px', color: '#475569' }}>
            Tidak ada data BOM untuk order ini.
        </div>
    );
}
