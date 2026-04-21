<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Operator — Smart Timbangan IoT</title>
    @viteReactRefresh
    @vite(['resources/js/operator.jsx'])
</head>
<body style="margin:0; background:#0f172a;">

    {{--
        React akan di-mount di sini.
        Data dari Laravel diteruskan via data-* attributes.
        order_id default ke 1 jika tidak ada; sesuaikan jika ada session/parameter.
    --}}
    <div
        id="operator-root"
        data-order-id="{{ request('order_id', 1) }}"
        data-user-name="{{ auth()->user()?->name ?? 'Operator' }}"
        data-device-id="{{ request('device_id', '') }}"
        data-device-name=""
    ></div>

</body>
</html>
