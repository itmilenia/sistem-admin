<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Kantor</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* --- Latar Belakang & Layout Utama --- */
        .login-bg {
            background-color: #dcdddf;
            /* Warna abu-abu sangat terang */
        }

        /* --- Desain Kartu yang Bersih --- */
        .card-clean {
            border: 1px solid #dee2e6;
            /* Border halus */
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
            /* Bayangan sangat lembut */
            transition: all 0.3s ease-in-out;
            animation: fadeIn 0.7s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- Kustomisasi Form Input --- */
        .form-control-custom,
        .input-group-text-custom,
        .btn-outline-secondary-custom {
            background-color: #ffffff;
            border-color: #ced4da;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control-custom:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            background-color: #ffffff;
            z-index: 3;
        }

        .input-group-text-custom {
            color: #6c757d;
            /* Warna ikon abu-abu */
        }

        .form-floating>label {
            color: #6c757d;
        }

        /* --- Tombol Utama yang Profesional --- */
        .btn-custom-primary {
            background-color: #0d6efd;
            /* Biru korporat */
            border: none;
            padding: 0.8rem 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-custom-primary:hover {
            background-color: #0b5ed7;
            /* Biru lebih gelap saat hover */
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        /* --- Link & Footer --- */
        a {
            color: #0d6efd;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .footer-text {
            color: #6c757d;
        }
    </style>
</head>

<body>

    <div class="container-fluid vh-100 d-flex flex-column justify-content-center align-items-center login-bg">
        <div class="col-11 col-sm-10 col-md-8 col-lg-5 col-xl-4 col-xxl-3">
            <div class="card card-clean border-0 rounded-4">
                <div class="card-body p-4 p-sm-5">

                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/images/logo/logo.jpeg') }}" alt="Logo Perusahaan" class="img-fluid"
                            style="max-height: 70px;">
                    </div>

                    <h4 class="card-title text-center mb-1 fw-bold">Login ke Akun Anda</h4>
                    <p class="card-text text-center text-muted mb-4">Selamat datang kembali!</p>

                    @if ($errors->any())
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control form-control-custom" id="floatingUname"
                                name="uname" value="{{ old('uname') }}" placeholder="Username Anda" required>
                            <label for="floatingUname"><i class="bi bi-person me-2"></i>Username</label>
                        </div>

                        <div class="input-group mb-4">
                            <div class="form-floating">
                                <input type="password" class="form-control form-control-custom" id="floatingPwd"
                                    name="pwd" placeholder="Password Anda" required style="border-right: 0; ">
                                <label for="floatingPwd"><i class="bi bi-lock me-2"></i>Password</label>
                            </div>
                            <button class="btn btn-outline-secondary btn-outline-secondary-custom" type="button"
                                id="togglePassword" style="border-left: 0;">
                                <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                            </button>
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-custom-primary text-white" type="submit">LOGIN</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <footer class="mt-4 text-center footer-text">
            <p class="small">&copy; {{ date('Y') }} Milenia Group. All Rights Reserved.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#floatingPwd');
        const toggleIcon = document.querySelector('#togglePasswordIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function(e) {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                toggleIcon.classList.toggle('bi-eye');
                toggleIcon.classList.toggle('bi-eye-slash');
            });
        }
    </script>
</body>

</html>
