/**
 * formatters.js
 * Kumpulan fungsi utilitas untuk format tampilan data.
 */

/**
 * Format angka desimal menjadi tampilan berat (KG).
 * @param {number|null} val
 * @returns {string}
 */
export const formatWeight = (val) =>
    val !== null && val !== undefined
        ? `${Number(val).toFixed(3)} KG`
        : '—';

/**
 * Format angka menjadi format mata uang Rupiah.
 * @param {number|null} val
 * @returns {string}
 */
export const formatCurrency = (val) =>
    val !== null && val !== undefined
        ? 'Rp ' + Number(val).toLocaleString('id-ID')
        : '—';

/**
 * Format ISO string waktu menjadi format lokal Indonesia.
 * @param {string|null} isoString
 * @returns {string}
 */
export const formatDateTime = (isoString) =>
    isoString
        ? new Date(isoString).toLocaleString('id-ID')
        : '—';

/**
 * Format ISO string waktu menjadi jam:menit:detik saja.
 * @param {string|null} isoString
 * @returns {string}
 */
export const formatTime = (isoString) =>
    isoString
        ? new Date(isoString).toLocaleTimeString('id-ID')
        : '—';

/**
 * Konfigurasi warna dan label untuk setiap status costing.
 */
export const STATUS_CONFIG = {
    Pending:  { label: 'Pending',    dot: '#f59e0b', bg: 'rgba(245,158,11,0.12)',  text: '#fbbf24' },
    Weighed:  { label: 'Ditimbang',  dot: '#3b82f6', bg: 'rgba(59,130,246,0.12)', text: '#60a5fa' },
    Approved: { label: 'Approved',   dot: '#22c55e', bg: 'rgba(34,197,94,0.12)',   text: '#4ade80' },
};
