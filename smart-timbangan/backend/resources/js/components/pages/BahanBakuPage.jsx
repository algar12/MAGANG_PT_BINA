import React, { useState, useEffect } from 'react';
import { formatCurrency } from '../utils/formatters';

/**
 * BahanBakuPage.jsx
 *
 * Halaman daftar Bahan Baku (Material) untuk referensi operator.
 * Data diambil dari GET /api/materials.
 * Operator dapat mencari bahan berdasarkan nama atau kode.
 */
export default function BahanBakuPage() {
    const [materials, setMaterials] = useState([]);
    const [loading, setLoading]     = useState(true);
    const [search, setSearch]       = useState('');

    useEffect(() => {
        fetch('/api/materials')
            .then(r => r.json())
            .then(json => { setMaterials(json.data ?? []); setLoading(false); })
            .catch(() => setLoading(false));
    }, []);

    // Filter berdasarkan input pencarian
    const filtered = materials.filter(m =>
        m.nama_produk.toLowerCase().includes(search.toLowerCase()) ||
        m.kode_produk.toLowerCase().includes(search.toLowerCase())
    );

    return (
        <div style={{ padding: '28px', maxWidth: 900 }}>

            {/* Header */}
            <div style={{ marginBottom: 24 }}>
                <h1 style={{ fontSize: 20, fontWeight: 800, marginBottom: 6 }}>Bahan Baku</h1>
                <p style={{ color: '#64748b', fontSize: 13 }}>
                    Daftar seluruh material yang terdaftar dalam sistem.
                </p>
            </div>

            {/* Search */}
            <SearchBar value={search} onChange={setSearch} />

            {/* Table */}
            <div style={{
                background: 'rgba(255,255,255,0.03)',
                border: '1px solid rgba(255,255,255,0.07)',
                borderRadius: 16, overflow: 'hidden',
            }}>
                {/* Header tabel */}
                <div style={{
                    padding: '14px 22px',
                    borderBottom: '1px solid rgba(255,255,255,0.07)',
                    display: 'flex', alignItems: 'center', justifyContent: 'space-between',
                }}>
                    <div style={{ fontWeight: 700, fontSize: 14 }}>Material List</div>
                    <div style={{ fontSize: 12, color: '#475569' }}>
                        {filtered.length} bahan ditemukan
                    </div>
                </div>

                {loading ? (
                    <div style={{ padding: '40px', textAlign: 'center', color: '#475569' }}>
                        Memuat data bahan baku…
                    </div>
                ) : (
                    <MaterialTable materials={filtered} />
                )}
            </div>
        </div>
    );
}

/** Input pencarian */
function SearchBar({ value, onChange }) {
    return (
        <div style={{ position: 'relative', marginBottom: 16, maxWidth: 360 }}>
            <svg
                width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="#475569" strokeWidth="2"
                style={{ position: 'absolute', left: 12, top: '50%', transform: 'translateY(-50%)' }}
            >
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35" strokeLinecap="round"/>
            </svg>
            <input
                type="text"
                placeholder="Cari nama atau kode bahan…"
                value={value}
                onChange={e => onChange(e.target.value)}
                style={{
                    width: '100%', padding: '10px 14px 10px 36px',
                    background: 'rgba(255,255,255,0.05)',
                    border: '1px solid rgba(255,255,255,0.1)',
                    borderRadius: 10, color: '#e2e8f0',
                    fontSize: 13, fontFamily: 'inherit', outline: 'none',
                }}
            />
        </div>
    );
}

/** Tabel material */
function MaterialTable({ materials }) {
    if (materials.length === 0) {
        return (
            <div style={{ padding: '40px', textAlign: 'center', color: '#475569' }}>
                Tidak ada bahan baku yang ditemukan.
            </div>
        );
    }

    const headers = ['Kode Produk', 'Nama Bahan', 'Satuan', 'Harga Standar', 'Status'];

    return (
        <div style={{ overflowX: 'auto' }}>
            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                <thead>
                    <tr style={{ background: 'rgba(255,255,255,0.03)' }}>
                        {headers.map(h => (
                            <th key={h} style={{
                                padding: '12px 16px', textAlign: 'left',
                                color: '#64748b', fontWeight: 600, fontSize: 11,
                                textTransform: 'uppercase', letterSpacing: '0.5px',
                                borderBottom: '1px solid rgba(255,255,255,0.06)',
                            }}>
                                {h}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {materials.map(m => (
                        <MaterialRow key={m.id} material={m} />
                    ))}
                </tbody>
            </table>
        </div>
    );
}

/** Satu baris material */
function MaterialRow({ material }) {
    return (
        <tr style={{ borderBottom: '1px solid rgba(255,255,255,0.04)' }}>
            <td style={{ padding: '13px 16px', color: '#64748b', fontFamily: 'monospace', fontSize: 12 }}>
                {material.kode_produk}
            </td>
            <td style={{ padding: '13px 16px', fontWeight: 600, color: '#e2e8f0' }}>
                {material.nama_produk}
            </td>
            <td style={{ padding: '13px 16px', color: '#94a3b8' }}>
                {material.uom_dasar}
            </td>
            <td style={{ padding: '13px 16px', color: '#94a3b8' }}>
                {formatCurrency(material.standart_cost)}
            </td>
            <td style={{ padding: '13px 16px' }}>
                <span style={{
                    display: 'inline-flex', alignItems: 'center', gap: 5,
                    padding: '3px 10px', borderRadius: 20, fontSize: 11, fontWeight: 600,
                    background: material.is_active ? 'rgba(34,197,94,0.1)' : 'rgba(100,116,139,0.1)',
                    color: material.is_active ? '#4ade80' : '#64748b',
                }}>
                    <span style={{
                        width: 5, height: 5, borderRadius: '50%',
                        background: material.is_active ? '#22c55e' : '#475569',
                    }} />
                    {material.is_active ? 'Aktif' : 'Nonaktif'}
                </span>
            </td>
        </tr>
    );
}
