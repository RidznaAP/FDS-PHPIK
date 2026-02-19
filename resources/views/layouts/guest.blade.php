<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>@yield('title', 'Login') — FDS-HPIK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        :root { --tblr-font-sans-serif: 'Inter', sans-serif; }
        body { font-family: 'Inter', sans-serif; background-color: #f4f6fa; }
    </style>
</head>
<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-normal py-4">
            <div class="row align-items-center g-4">
                {{-- Illustration (Left Side) --}}
                <div class="col-lg d-none d-lg-block">
                    <div class="text-center">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/fisherman-fishing-at-sea-illustration-download-in-svg-png-gif-file-formats--fish-boat-ocean-occupation-pack-people-illustrations-7517006.png" height="300" class="d-block mx-auto" alt="">
                        <h2 class="h1 mt-4 mb-2 text-primary">Sistem Pemantauan HPIK</h2>
                        <p class="text-muted fs-3">Monitor sebaran Hama Penyakit Ikan Karantina secara real-time, akurat, dan terintegrasi GIS.</p>
                    </div>
                </div>

                {{-- Login Form (Right Side) --}}
                <div class="col-lg">
                    <div class="container-tight">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script>
        function togglePassword() {
            var x = document.getElementById("password");
            var icon = document.getElementById("eye-icon");
            if (x.type === "password") {
                x.type = "text";
                icon.classList.remove("ti-eye");
                icon.classList.add("ti-eye-off");
            } else {
                x.type = "password";
                icon.classList.remove("ti-eye-off");
                icon.classList.add("ti-eye");
            }
        }
    </script>
</body>
</html>
