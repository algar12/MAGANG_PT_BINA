import React, { useState } from 'react';

/**
 * Sidebar.jsx
 *
 * Navigasi kiri operator dashboard.
 * Terdiri dari dua kelompok menu yang bisa di-collapse:
 *  - PENIMBANGAN UTAMA → Mulai Menimbang
 *  - PENGATURAN SISTEM  → Bahan Baku
 *
 * @param {{ currentPage: string, onNavigate: Function }} props
 */
export default function Sidebar({ currentPage, onNavigate }) {
    const [openSections, setOpenSections] = useState({
        penimbangan: true,
        pengaturan: true,
    });

    const toggleSection = (key) =>
        setOpenSections(prev => ({ ...prev, [key]: !prev[key] }));

    return (
        <aside style={{
            width: 260,
            minHeight: '100vh',
            background: '#111827',
            borderRight: '1px solid rgba(255,255,255,0.06)',
            display: 'flex',
            flexDirection: 'column',
            padding: '16px 0',
            flexShrink: 0,
        }}>
            {/* Logo */}
            <div style={{
                display: 'flex', alignItems: 'center', gap: 10,
                padding: '8px 20px 20px',
                borderBottom: '1px solid rgba(255,255,255,0.06)',
                marginBottom: 8,
            }}>
                <div style={{
                    width: 32, height: 32, borderRadius: 8, flexShrink: 0,
                    background: 'linear-gradient(135deg,#2563eb,#0ea5e9)',
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                }}>
                    <ScaleIcon size={16} color="white" />
                </div>
                <span style={{ fontWeight: 700, fontSize: 14, color: '#e2e8f0' }}>
                    Smart Timbangan
                </span>
            </div>

            {/* Dashboard link */}
            <div style={{ padding: '0 12px', marginBottom: 8 }}>
                <NavItem
                    icon={<HomeIcon />}
                    label="Dashboard"
                    active={currentPage === 'dashboard'}
                    onClick={() => onNavigate('dashboard')}
                />
            </div>

            {/* ── PENIMBANGAN UTAMA ── */}
            <SectionGroup
                label="PENIMBANGAN UTAMA"
                open={openSections.penimbangan}
                onToggle={() => toggleSection('penimbangan')}
            >
                <NavItem
                    icon={<ScaleIcon size={18} />}
                    label="MULAI MENIMBANG"
                    active={currentPage === 'mulai-menimbang'}
                    onClick={() => onNavigate('mulai-menimbang')}
                    bold
                />
            </SectionGroup>

            {/* ── PENGATURAN SISTEM ── */}
            <SectionGroup
                label="PENGATURAN SISTEM"
                open={openSections.pengaturan}
                onToggle={() => toggleSection('pengaturan')}
            >
                <NavItem
                    icon={<BoxIcon />}
                    label="Bahan Baku"
                    active={currentPage === 'bahan-baku'}
                    onClick={() => onNavigate('bahan-baku')}
                />
            </SectionGroup>
        </aside>
    );
}

/* ── Sub-components ── */

/** Satu item navigasi di sidebar */
function NavItem({ icon, label, active, onClick, bold = false }) {
    return (
        <button
            onClick={onClick}
            style={{
                width: '100%',
                display: 'flex', alignItems: 'center', gap: 12,
                padding: '10px 12px',
                borderRadius: 8,
                border: 'none',
                background: active ? 'rgba(217,119,6,0.15)' : 'transparent',
                color: active ? '#f59e0b' : '#9ca3af',
                fontSize: bold ? 13 : 14,
                fontWeight: bold ? 700 : 500,
                letterSpacing: bold ? '0.03em' : 'normal',
                fontFamily: 'inherit',
                cursor: 'pointer',
                textAlign: 'left',
                transition: 'all 0.15s ease',
            }}
            onMouseOver={e => {
                if (!active) e.currentTarget.style.background = 'rgba(255,255,255,0.05)';
                if (!active) e.currentTarget.style.color = '#e2e8f0';
            }}
            onMouseOut={e => {
                if (!active) e.currentTarget.style.background = 'transparent';
                if (!active) e.currentTarget.style.color = '#9ca3af';
            }}
        >
            <span style={{ color: active ? '#f59e0b' : '#6b7280', flexShrink: 0 }}>
                {icon}
            </span>
            {label}
        </button>
    );
}

/** Kelompok section dengan header dan collapse toggle */
function SectionGroup({ label, open, onToggle, children }) {
    return (
        <div style={{ marginBottom: 4 }}>
            {/* Header section */}
            <button
                onClick={onToggle}
                style={{
                    width: '100%',
                    display: 'flex', alignItems: 'center', justifyContent: 'space-between',
                    padding: '10px 20px 6px',
                    background: 'transparent', border: 'none',
                    color: '#6b7280', fontSize: 11, fontWeight: 600,
                    letterSpacing: '0.08em', textTransform: 'uppercase',
                    fontFamily: 'inherit', cursor: 'pointer',
                }}
            >
                {label}
                <ChevronIcon open={open} />
            </button>

            {/* Items (collapsible) */}
            {open && (
                <div style={{ padding: '2px 12px 8px' }}>
                    {children}
                </div>
            )}
        </div>
    );
}

/* ── Icons ── */
function HomeIcon() {
    return (
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" strokeLinecap="round" strokeLinejoin="round"/>
            <polyline points="9,22 9,12 15,12 15,22" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    );
}

function ScaleIcon({ size = 20, color = 'currentColor' }) {
    return (
        <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2">
            <path d="M16 16l-4-8-4 8" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M8 16h8" strokeLinecap="round"/>
            <path d="M12 2v6" strokeLinecap="round"/>
            <circle cx="12" cy="2" r="1" fill={color} stroke="none"/>
            <path d="M4 21h16" strokeLinecap="round"/>
        </svg>
    );
}

function BoxIcon() {
    return (
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" strokeLinecap="round" strokeLinejoin="round"/>
            <polyline points="3.27,6.96 12,12.01 20.73,6.96" strokeLinecap="round" strokeLinejoin="round"/>
            <line x1="12" y1="22.08" x2="12" y2="12" strokeLinecap="round"/>
        </svg>
    );
}

function ChevronIcon({ open }) {
    return (
        <svg
            width="14" height="14"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"
            style={{ transform: open ? 'rotate(0deg)' : 'rotate(-90deg)', transition: 'transform 0.2s' }}
        >
            <polyline points="18,15 12,9 6,15" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
    );
}
