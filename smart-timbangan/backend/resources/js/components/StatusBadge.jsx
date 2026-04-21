import React from 'react';
import { STATUS_CONFIG } from './utils/formatters';

/**
 * StatusBadge.jsx
 *
 * Menampilkan badge berwarna sesuai status costing:
 * - Pending  → kuning
 * - Weighed  → biru
 * - Approved → hijau
 *
 * @param {{ status: 'Pending'|'Weighed'|'Approved' }} props
 */
export default function StatusBadge({ status }) {
    const cfg = STATUS_CONFIG[status] ?? STATUS_CONFIG.Pending;

    return (
        <span style={{
            display: 'inline-flex',
            alignItems: 'center',
            gap: 5,
            padding: '3px 10px',
            borderRadius: 20,
            background: cfg.bg,
            color: cfg.text,
            fontSize: 12,
            fontWeight: 600,
        }}>
            {/* Dot indicator berwarna */}
            <span style={{
                width: 6,
                height: 6,
                borderRadius: '50%',
                background: cfg.dot,
                boxShadow: `0 0 6px ${cfg.dot}`,
            }} />
            {cfg.label}
        </span>
    );
}
