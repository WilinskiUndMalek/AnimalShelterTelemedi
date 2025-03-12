<?php
session_start();
require_once 'config.php';
require_once 'ApiClient.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = new ApiClient(API_BASE_URL);
    if ($api->login($_POST['email'], $_POST['password'])) {
        header('Location: animals.php');
        exit;
    } else {
        $error = "Nieprawidłowe dane logowania";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logowanie - Schronisko dla Zwierząt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            background-image: url('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
        .login-container {
            margin-top: 100px;
            max-width: 450px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.9);
        }
        .card-header {
            background-color: transparent;
            border-bottom: none;
            text-align: center;
            padding-top: 30px;
        }
        .card-header h3 {
            color: #3a3a3a;
            font-weight: 600;
        }
        .card-body {
            padding: 30px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #e1e1e1;
            margin-bottom: 15px;
            height: auto;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            background-color: #4e73df;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #375ad3;
            transform: translateY(-2px);
        }
        .input-group-text {
            background-color: transparent;
            border-right: none;
            padding: 12px;
            display: flex;
            align-items: center;
        }
        .input-group .form-control {
            border-left: none;
            margin-bottom: 0;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .logo {
            max-width: 80px;
            margin-bottom: 15px;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <img src="https://cdn-icons-png.flaticon.com/512/3047/3047928.png" alt="Logo" class="logo">
                        <h3>Schronisko Telemedi</h3>
                        <p class="text-muted">Zaloguj się do systemu zarządzania</p>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label"><i class="fas fa-envelope me-2"></i>Email:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Wprowadź adres email" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label"><i class="fas fa-lock me-2"></i>Hasło:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Wprowadź hasło" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Zaloguj się
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>