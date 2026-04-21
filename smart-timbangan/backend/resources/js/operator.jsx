import React from 'react';
import { createRoot } from 'react-dom/client';
import OperatorApp from './components/OperatorApp';

const el = document.getElementById('operator-root');
if (el) {
    const orderId    = el.dataset.orderId    || null;
    const userName   = el.dataset.userName   || 'Operator';
    const deviceId   = el.dataset.deviceId   || null;
    const deviceName = el.dataset.deviceName || null;

    createRoot(el).render(
        <OperatorApp
            orderId={orderId}
            userName={userName}
            deviceId={deviceId}
            deviceName={deviceName}
        />
    );
}
