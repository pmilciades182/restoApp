<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <style>
            body {
                margin: 0;
                padding: 0;
                min-height: 100vh;
                background-color: #f5f5f5;
                background-image:
                    radial-gradient(circle at 100% 150%, #f5f5f5 24%, #f0f0f0 25%, #f0f0f0 28%, #f5f5f5 29%, #f5f5f5 36%, #f0f0f0 36%, #f0f0f0 40%, transparent 40%, transparent),
                    radial-gradient(circle at 0 150%, #f5f5f5 24%, #f0f0f0 25%, #f0f0f0 28%, #f5f5f5 29%, #f5f5f5 36%, #f0f0f0 36%, #f0f0f0 40%, transparent 40%, transparent),
                    radial-gradient(circle at 50% 100%, #f0f0f0 10%, #f5f5f5 11%, #f5f5f5 23%, #f0f0f0 24%, #f0f0f0 30%, #f5f5f5 31%, #f5f5f5 43%, #f0f0f0 44%, #f0f0f0 50%, #f5f5f5 51%, #f5f5f5 63%, #f0f0f0 64%, #f0f0f0 71%, transparent 71%, transparent),
                    radial-gradient(circle at 100% 50%, #f0f0f0 5%, #f5f5f5 6%, #f5f5f5 15%, #f0f0f0 16%, #f0f0f0 20%, #f5f5f5 21%, #f5f5f5 30%, #f0f0f0 31%, #f0f0f0 35%, #f5f5f5 36%, #f5f5f5 45%, #f0f0f0 46%, #f0f0f0 49%, transparent 50%, transparent),
                    radial-gradient(circle at 0 50%, #f0f0f0 5%, #f5f5f5 6%, #f5f5f5 15%, #f0f0f0 16%, #f0f0f0 20%, #f5f5f5 21%, #f5f5f5 30%, #f0f0f0 31%, #f0f0f0 35%, #f5f5f5 36%, #f5f5f5 45%, #f0f0f0 46%, #f0f0f0 49%, transparent 50%, transparent);
                background-size: 100px 50px;
                animation: bgAnimation 30s linear infinite;
            }

            @keyframes bgAnimation {
                0% { background-position: 0 0; }
                100% { background-position: 100px 0; }
            }

            .container {
                height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .nav {
                padding: 1.5rem;
                text-align: right;
                position: fixed;
                top: 0;
                right: 0;
                width: 100%;
                z-index: 10;
            }

            .login-button {
                color: white;
                text-decoration: none;
                font-size: 1rem;
                font-weight: 600;
                padding: 0.75rem 2rem;
                border-radius: 50px;
                background: linear-gradient(45deg, #2563eb, #3b82f6);
                box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .login-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
                background: linear-gradient(45deg, #1d4ed8, #2563eb);
            }

            .login-button:active {
                transform: translateY(0);
            }

            .content {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .logo-container {
                width: 800px;
                height: 800px;
                display: flex;
                justify-content: center;
                align-items: center;
                background: white;
                padding: 1rem;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .logo-container img {
                width: 800px;
                height: 800px;
                object-fit: cover;
                border-radius: 10px;
            }

            @media (max-width: 840px) {
                .logo-container {
                    width: 90vw;
                    height: 90vw;
                }

                .logo-container img {
                    width: 100%;
                    height: 100%;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <!-- Navegación -->
            <nav class="nav">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="login-button">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="login-button">Ingresar</a>
                    @endauth
                @endif
            </nav>

            <!-- Contenido Central -->
            <main class="content">
                <div class="logo-container">
                    <img src="{{ asset('img/r.jpg') }}" alt="Logo">
                </div>
            </main>
        </div>
    </body>
</html>
