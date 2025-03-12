<?php
session_start();
require_once 'config.php';
require_once 'ApiClient.php';

if (!isset($_SESSION['authToken'])) {
    header('Location: login.php');
    exit;
}

$api = new ApiClient(API_BASE_URL, $_SESSION['authToken']);
$message = '';
$messageType = '';

// Pobierz listę wolontariuszy
$volunteers = $api->getVolunteers();

// Funkcja pomocnicza do formatowania daty
function formatDate($dateString) {
    if (empty($dateString)) return '';
    $date = new DateTime($dateString);
    return $date->format('d.m.Y H:i');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista Wolontariuszy - Schronisko dla Zwierząt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #4e73df;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: 600;
            color: white;
        }
        .navbar-brand img {
            width: 40px;
            margin-right: 10px;
        }
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s;
        }
        .navbar-nav .nav-link:hover {
            color: white;
        }
        .navbar-nav .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        .main-content {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .page-header h2 {
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        .btn-action {
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-action:hover {
            background-color: #375ad3;
            transform: translateY(-2px);
            color: white;
        }
        .table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e3e6f0;
            font-weight: 600;
            color: #4e73df;
            padding: 15px;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e3e6f0;
        }
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .btn-group .btn {
            margin-right: 5px;
        }
        .btn-group .btn:last-child {
            margin-right: 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 30px;
        }
        .volunteer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #36b9cc;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }
        .no-data {
            text-align: center;
            padding: 50px 0;
            color: #6c757d;
        }
        .no-data i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #d1d3e2;
        }
        .availability-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            background-color: #e8f4fd;
            color: #4e73df;
            display: inline-block;
        }
        .date-joined {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://cdn-icons-png.flaticon.com/512/3047/3047928.png" alt="Logo">
                Schronisko dla Zwierząt
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="animals.php"><i class="fas fa-paw me-2"></i>Zwierzęta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="staff.php"><i class="fas fa-user-tie me-2"></i>Pracownicy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="volunteers.php"><i class="fas fa-hands-helping me-2"></i>Wolontariusze</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Wyloguj</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container main-content">
        <div class="page-header">
            <h2><i class="fas fa-hands-helping me-2"></i>Lista Wolontariuszy</h2>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (empty($volunteers)): ?>
            <div class="no-data">
                <i class="fas fa-user-slash"></i>
                <p>Brak wolontariuszy do wyświetlenia.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th style="width: 80px;">Avatar</th>
                            <th>Imię i Nazwisko</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th>Dostępność</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($volunteers as $volunteer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($volunteer['id']); ?></td>
                            <td>
                                <div class="volunteer-avatar">
                                    <?php echo strtoupper(substr($volunteer['volunteer_name'], 0, 1)); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($volunteer['volunteer_name']); ?></td>
                            <td><?php echo htmlspecialchars($volunteer['email']); ?></td>
                            <td><?php echo htmlspecialchars($volunteer['phone_number']); ?></td>
                            <td>
                                <span class="availability-badge">
                                    <?php echo htmlspecialchars($volunteer['availability']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Schronisko dla Zwierząt. Wszystkie prawa zastrzeżone.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Skrypt do obsługi modalu usuwania
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteVolunteerModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const volunteerId = button.getAttribute('data-volunteer-id');
                    const volunteerName = button.getAttribute('data-volunteer-name');
                    
                    const volunteerNameElement = document.getElementById('volunteerName');
                    const volunteerIdInput = document.getElementById('volunteerIdInput');
                    
                    volunteerNameElement.textContent = volunteerName;
                    volunteerIdInput.value = volunteerId;
                });
            }
        });
    </script>
</body>
</html>