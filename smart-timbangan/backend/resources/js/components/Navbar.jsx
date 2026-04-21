import React from 'react';
import { formatTime } from './utils/formatters';

/**
 * Navbar.jsx
 *
 * Navigasi atas operator timbangan dashboard.
 * Menampilkan nama user, indikator "Live" dengan waktu update terakhir,
 * dan tombol logout.
 *
 * @param {{ userName: string, currentPage: string, lastUpdate: Date|null, pulse: boolean }} props
 */
export default function Navbar({ userName, currentPage, lastUpdate, pulse }) {
    const pageTitles = {
        'dashboard':        'Dashboard',
        'mulai-menimbang':  'Mulai Menimbang',
        'bahan-baku':       'Bahan Baku',
    };
    const title = pageTitles[currentPage] ?? 'Dashboard';
    return (
        <nav style={{
            background: 'rgba(255,255,255,0.03)',
            borderBottom: '1px solid rgba(255,255,255,0.07)',
            padding: '0 24px',
            height: 60,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
            backdropFilter: 'blur(12px)',
            position: 'sticky',
            top: 0,
            zIndex: 100,
        }}>
            {/* Judul halaman aktif */}
            <div style={{ fontWeight: 700, fontSize: 15, color: '#e2e8f0' }}>{title}</div>
            <NavRight userName={userName} lastUpdate={lastUpdate} pulse={pulse} />
        </nav>
    );
}

/** Logo dan nama aplikasi */
function BrandLogo() {
    return (
        <div style={{ display: 'flex', alignItems: 'center', gap: 10, fontWeight: 700 }}>
            <div style={{
                width: 34, height: 34, borderRadius: 9,
                background: 'linear-gradient(135deg,#2563eb,#0ea5e9)',
                display: 'flex', alignItems: 'center', justifyContent: 'center',
            }}>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="1.8">
                    <path d="M3 9l1-5h16l1 5M3 9h18M3 9l2 12h14l2-12" strokeLinecap="round" strokeLinejoin="round"/>
                </svg>
            </div>
            Smart Timbangan IoT
        </div>
    );
}

/** Sisi kanan navbar: nama user, badge Live, tombol logout */
function NavRight({ userName, lastUpdate, pulse }) {
    return (
        <div style={{ display: 'flex', alignItems: 'center', gap: 14 }}>
            <div style={{ fontSize: 13, color: '#64748b' }}>
                Halo, <strong style={{ color: '#e2e8f0' }}>{userName}</strong>
                <span style={{ marginLeft: 6, fontSize: 11, color: '#475569' }}>(Operator Timbangan)</span>
            </div>

            {/* Badge Live */}
            <LiveBadge lastUpdate={lastUpdate} pulse={pulse} />

            {/* Tombol logout */}
            <LogoutButton />
        </div>
    );
}

/** Badge hijau menampilkan status "Live" + waktu update terakhir */
function LiveBadge({ lastUpdate, pulse }) {
    return (
        <div style={{
            display: 'flex', alignItems: 'center', gap: 6,
            fontSize: 12, color: '#22c55e',
            background: 'rgba(34,197,94,0.08)',
            border: '1px solid rgba(34,197,94,0.2)',
            padding: '4px 12px', borderRadius: 20,
        }}>
            <span style={{
                width: 6, height: 6, borderRadius: '50%',
                background: '#22c55e',
                boxShadow: '0 0 6px #22c55e',
                animation: pulse ? 'none' : 'pulse 2s infinite',
            }} />
            Live
            {lastUpdate && (
                <span style={{ color: '#475569', marginLeft: 4 }}>
                    {formatTime(lastUpdate.toISOString())}
                </span>
            )}
        </div>
    );
}

/** Form logout yang mengirim POST /logout dengan CSRF token */
function LogoutButton() {
    const csrfToken = document.querySelector('meta[name=csrf-token]')?.content;

    return (
        <form method="POST" action="/logout">
            <input type="hidden" name="_token" value={csrfToken} />
            <button
                type="submit"
                style={{
                    padding: '6px 14px', borderRadius: 8,
                    border: '1px solid rgba(255,255,255,0.1)',
                    background: 'transparent', color: '#94a3b8',
                    fontSize: 12, fontFamily: 'Inter,sans-serif', cursor: 'pointer',
                    transition: 'color 0.2s',
                }}
                onMouseOver={e => e.currentTarget.style.color = '#fca5a5'}
                onMouseOut={e  => e.currentTarget.style.color = '#94a3b8'}
            >
                Keluar
            </button>
        </form>
    );
}
