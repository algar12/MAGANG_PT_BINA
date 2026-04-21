import React, { useState } from 'react';
import Sidebar from './Sidebar';
import Navbar from './Navbar';
import DashboardPage from './pages/DashboardPage';
import MulaiMenimbangPage from './pages/MulaiMenimbangPage';
import BahanBakuPage from './pages/BahanBakuPage';

/**
 * OperatorApp.jsx
 *
 * Root component operator dashboard.
 * Mengatur layout utama: Sidebar (kiri) + Konten (kanan).
 * State `currentPage` menentukan halaman mana yang ditampilkan.
 *
 * Routing dilakukan dengan simple state, tidak perlu React Router
 * karena ini adalah SPA embedded dalam Laravel blade.
 *
 * @param {{ orderId, userName, deviceId, deviceName }} props
 *   — data dari Blade view melalui data-* attributes
 */
export default function OperatorApp({ orderId, userName, deviceId, deviceName }) {
    const [currentPage, setCurrentPage] = useState('dashboard');

    // Render halaman yang aktif
    const renderPage = () => {
        switch (currentPage) {
            case 'mulai-menimbang':
                return (
                    <MulaiMenimbangPage
                        defaultOrderId={orderId}
                        deviceId={deviceId}
                        deviceName={deviceName}
                    />
                );
            case 'bahan-baku':
                return <BahanBakuPage />;
            case 'dashboard':
            default:
                return <DashboardPage userName={userName} />;
        }
    };

    return (
        <>
            {/* Global styles & Google Font */}
            <style>{`
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
                *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                html, body { height: 100%; font-family: 'Inter', sans-serif; background: #0f172a; color: #e2e8f0; }
                select option { background: #1e293b; }
                @keyframes pulse { 0%,100%{ opacity:1; } 50%{ opacity:0.4; } }
                @media (max-width: 640px) {
                    .stats-grid { grid-template-columns: repeat(2,1fr) !important; }
                }
            `}</style>

            <div style={{ display: 'flex', minHeight: '100vh' }}>
                {/* ── Sidebar navigasi kiri ── */}
                <Sidebar currentPage={currentPage} onNavigate={setCurrentPage} />

                {/* ── Area konten kanan ── */}
                <div style={{ flex: 1, display: 'flex', flexDirection: 'column', overflow: 'auto' }}>

                    {/* Top navbar */}
                    <Navbar
                        userName={userName}
                        currentPage={currentPage}
                        lastUpdate={null}
                        pulse={false}
                    />

                    {/* Halaman aktif */}
                    <main style={{ flex: 1 }}>
                        {renderPage()}
                    </main>

                </div>
            </div>
        </>
    );
}
