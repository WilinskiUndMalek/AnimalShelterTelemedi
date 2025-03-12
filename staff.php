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

// Pobierz listę pracowników
$staffList = $api->getStaff();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista Pracowników - Schronisko dla Zwierząt</title>
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
        .staff-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4e73df;
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
                        <a class="nav-link active" href="staff.php"><i class="fas fa-user-tie me-2"></i>Pracownicy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="volunteers.php"><i class="fas fa-hands-helping me-2"></i>Wolontariusze</a>
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
            <h2><i class="fas fa-user-tie me-2"></i>Lista Pracowników</h2>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (empty($staffList)): ?>
            <div class="no-data">
                <i class="fas fa-user-slash"></i>
                <p>Brak pracowników do wyświetlenia.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th style="width: 80px;">Avatar</th>
                            <th>Imię i Nazwisko</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staffList as $index => $staff): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td>
                                <div class="staff-avatar">
                                    <?php echo strtoupper(substr($staff['name'], 0, 1)); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($staff['name']); ?></td>
                            <td><?php echo htmlspecialchars($staff['email']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal do potwierdzenia usunięcia -->
    <div class="modal fade" id="deleteStaffModal" tabindex="-1" aria-labelledby="deleteStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStaffModalLabel">Potwierdź usunięcie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Czy na pewno chcesz usunąć pracownika <strong id="staffName"></strong>?</p>
                    <p class="text-danger">Ta operacja jest nieodwracalna!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <form method="post" action="">
                        <input type="hidden" name="staff_id" id="staffIdInput" value="">
                        <button type="submit" name="delete_staff" class="btn btn-danger">Usuń</button>
                    </form>
                </div>
            </div>
        </div>
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
            const deleteModal = document.getElementById('deleteStaffModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const staffId = button.getAttribute('data-staff-id');
                    const staffName = button.getAttribute('data-staff-name');
                    
                    const staffNameElement = document.getElementById('staffName');
                    const staffIdInput = document.getElementById('staffIdInput');
                    
                    staffNameElement.textContent = staffName;
                    staffIdInput.value = staffId;
                });
            }
        });
    </script>
</body>
</html>