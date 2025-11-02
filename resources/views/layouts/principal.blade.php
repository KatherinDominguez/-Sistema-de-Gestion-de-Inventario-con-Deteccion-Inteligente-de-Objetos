<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi Aplicación')</title>
    
    <!-- CSS Responsive -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    
    <!-- MediaPipe para gestos -->
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Botón Hamburguesa (solo móvil) -->
    <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Abrir menú">
        ☰
    </button>
    
    <!-- Overlay para cerrar sidebar en móvil -->
    <div class="overlay" onclick="toggleSidebar()"></div>
    
    <div class="container-principal">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            @yield('sidebar')
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                @yield('topbar')
            </header>
            
            <!-- Left Box (Opciones) -->
            <section class="leftbox">
                @yield('leftbox')
            </section>
            
            <!-- Right Box (Gráfica/Resultados) -->
            <section class="rightbox">
                @yield('rightbox')
            </section>
            
            <!-- Bottom Box (Estado/Resultados) -->
            <footer class="bottombox">
                @yield('bottombox')
            </footer>
        </main>
    </div>

    <!-- JavaScript para el menú móvil -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.overlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Prevenir scroll del body cuando el menú está abierto
            if (sidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
        
        // Cerrar sidebar al cambiar de tamaño de pantalla
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.querySelector('.overlay');
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
        
        // Cerrar sidebar al hacer clic en un enlace (móvil)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 768) {
                const sidebarLinks = document.querySelectorAll('.sidebar a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        setTimeout(toggleSidebar, 200);
                    });
                });
            }
        });
    </script>
</body>
</html>