<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Página Principal')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        }

    .container {
        display: grid;
        grid-template-columns: 200px auto;
        grid-template-rows: auto 1fr auto;
        height: 100vh;
        gap: 5px;
        box-sizing: border-box;
    }

    .sidebar {
        grid-row: 1 / 4;
        background-color: #0b2c4d;
        padding-top: 20px;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar li {
        margin-bottom: 10px;
    }

    .sidebar a {
        display: block;
        background-color: #2468a2; 
        color: white;
        text-decoration: none;
        padding: 12px 16px;
        margin: 0 10px;
        border-radius: 4px;
        font-weight: bold;
        text-align: center;
        transition: background-color 0.3s ease;
    }

    .sidebar a:hover {
        background-color: #1e5485;
    }

    .topbar {
        grid-column: 2;
        padding: 10px;
        background-color: #eaeaea;
        border: 1px solid #ccc;
        height: 80px;         
        overflow-y: scroll;
    }

    .box1 {
        grid-column: 2;
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 5px;
    }

    .left, .right, .bottombox {
        padding: 10px;
        border: 1px solid #ccc;
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            @yield('sidebar', 'Aquí va el menú lateral')
        </div>

        <div class="topbar">
            @yield('topbar')
        </div>

        <div class="box1">
            <div class="left">
                @yield('leftbox', 'Caja izquierda')
            </div>
            <div class="right">
                @yield('rightbox', 'Caja derecha grande')
            </div>
        </div>
       <div class="bottombox" style="display: flex; padding: 10px; border-top: 1px solid #ccc;">
            <div style="width: 40%; padding-right: 15px; border-right: 1px solid #aaa;">
                @include('components.bottombox-estado')
            </div>
            <div style="width: 40%; padding-left: 15px;">
                @include('components.bottombox-resultado')
            </div>
        </div>
    </div>
</body>
</html>

