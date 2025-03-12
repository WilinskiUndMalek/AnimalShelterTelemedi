<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wylogowanie - Schronisko dla Zwierząt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta http-equiv="refresh" content="5;url=login.php">
    <style>
        body {
            background-color: #f8f9fa;
            background-image: url('https://images.unsplash.com/photo-1450778869180-41d0601e046e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logout-container {
            max-width: 500px;
            width: 100%;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.9);
            text-align: center;
            padding: 40px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 20px;
        }
        h2 {
            color: #3a3a3a;
            font-weight: 600;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .btn-primary {
            background-color: #4e73df;
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #375ad3;
            transform: translateY(-2px);
        }
        .countdown {
            font-size: 24px;
            font-weight: bold;
            color: #4e73df;
            margin: 20px 0;
        }
        .success-checkmark {
            font-size: 60px;
            color: #1cc88a;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container logout-container">
        <div class="card">
            <div class="success-checkmark">
                <i class="fas fa-check-circle"></i>
            </div>
            <img src="https://cdn-icons-png.flaticon.com/512/3047/3047928.png" alt="Logo" class="logo mx-auto">
            <h2>Wylogowanie zakończone pomyślnie</h2>
            <p>Dziękujemy za korzystanie z systemu zarządzania schroniskiem.</p>
            <div class="countdown">
                <span id="countdown">5</span>
            </div>
            <p class="text-muted">Za chwilę nastąpi przekierowanie na stronę logowania...</p>
            <a href="login.php" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i>Przejdź do logowania
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Odliczanie do przekierowania
        let seconds = 5;
        const countdownElement = document.getElementById('countdown');
        
        const countdown = setInterval(function() {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdown);
            }
        }, 1000);
    </script>
</body>
</html>