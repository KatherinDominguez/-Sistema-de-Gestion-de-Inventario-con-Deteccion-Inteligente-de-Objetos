<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Reportes')</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            background-color: #f4f6f9;
        }
        header {
            background-color: #003b73;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0px 0px 8px rgba(0,0,0,0.1);
        }
        .card {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #ddd;
        }
        h3 {
            color: #003b73;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #ddd;
        }
        .critico {
            background-color: #ffc9c9;
        }
        .btn-volver {
            background-color: #003b73;
            color: white;
            padding: 8px 18px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        canvas {
            margin-top: 10px;
        }
    </style>
    @yield('head')
</head>
<body>
    <header>
        Reportes
    </header>

    <div class="container">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
