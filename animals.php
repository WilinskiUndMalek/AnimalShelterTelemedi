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

// Obsługa usuwania zwierzaka
if (isset($_POST['delete_animal']) && isset($_POST['animal_id'])) {
    $animalId = $_POST['animal_id'];
    $result = $api->deleteAnimal($animalId);
    
    if (!isset($result['error'])) {
        $message = 'Zwierzak został pomyślnie usunięty!';
        $messageType = 'success';
    } else {
        $message = 'Wystąpił błąd podczas usuwania zwierzaka: ' . ($result['message'] ?? 'Nieznany błąd');
        $messageType = 'danger';
    }
}

// Pobierz listę zwierząt
$animals = $api->getAnimals();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista Zwierząt - Schronisko dla Zwierząt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
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
        .btn-add {
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-add:hover {
            background-color: #375ad3;
            transform: translateY(-2px);
            color: white;
        }
        table.dataTable {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #555;
            border: none;
            padding: 12px 15px;
        }
        .table td {
            vertical-align: middle;
            padding: 12px 15px;
            border-top: 1px solid #f2f2f2;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(78, 115, 223, 0.03);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            border-radius: 30px;
        }
        .badge-success {
            background-color: #1cc88a;
        }
        .badge-warning {
            background-color: #f6c23e;
        }
        .badge-danger {
            background-color: #e74a3b;
        }
        .badge-info {
            background-color: #36b9cc;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .action-buttons .btn {
            margin-right: 5px;
        }
        .action-buttons .btn:last-child {
            margin-right: 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin-top: 30px;
        }
        .animal-name {
            color: #4e73df;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        .animal-name:hover {
            color: #375ad3;
            text-decoration: underline;
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
                        <a class="nav-link active" href="animals.php"><i class="fas fa-paw me-2"></i>Zwierzęta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="staff.php"><i class="fas fa-users me-2"></i>Pracownicy</a>
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
            <h2><i class="fas fa-paw me-2"></i>Lista Zwierząt</h2>
            <a href="add_animal.php" class="btn btn-add"><i class="fas fa-plus me-2"></i>Dodaj Zwierzę</a>
        </div>
        
        <table id="animalsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Gatunek</th>
                    <th>Rasa</th>
                    <th>Wiek</th>
                    <th>Status adopcji</th>
                    <th>Data przyjęcia</th>
                    <th>Waga (kg)</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($animals['result']) && is_array($animals['result'])): ?>
                    <?php foreach ($animals['result'] as $animal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($animal['id']); ?></td>
                        <td>
                            <a href="animal_details.php?id=<?php echo htmlspecialchars($animal['id']); ?>" class="animal-name">
                                <?php echo htmlspecialchars($animal['name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($animal['species']); ?></td>
                        <td><?php echo htmlspecialchars($animal['breed']); ?></td>
                        <td><?php echo htmlspecialchars($animal['age']); ?></td>
                        <td>
                            <?php 
                            $status = htmlspecialchars($animal['adoption_status']);
                            $badgeClass = 'badge-info';
                            
                            if ($status == 'Dostępny') {
                                $badgeClass = 'badge-success';
                            } elseif ($status == 'W trakcie adopcji') {
                                $badgeClass = 'badge-warning';
                            } elseif ($status == 'Zaadoptowany') {
                                $badgeClass = 'badge-danger';
                            }
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($animal['arrival_date']); ?></td>
                        <td><?php echo htmlspecialchars($animal['weight']); ?></td>
                        <td class="action-buttons">
                            <a href="edit_animal.php?id=<?php echo htmlspecialchars($animal['id']); ?>" class="btn btn-sm btn-primary" title="Edytuj"><i class="fas fa-edit"></i></a>
                            <a href="animal_details.php?id=<?php echo htmlspecialchars($animal['id']); ?>" class="btn btn-sm btn-success" title="Szczegóły"><i class="fas fa-eye"></i></a>
                            <button type="button" class="btn btn-sm btn-danger" title="Usuń" 
                                        data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                        data-animal-id="<?php echo $animal['id']; ?>" 
                                        data-animal-name="<?php echo htmlspecialchars($animal['name']); ?>">
                                    <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Brak danych lub problem z połączeniem z API</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
   <!-- Modal do potwierdzenia usunięcia -->
   <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Potwierdź usunięcie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Czy na pewno chcesz usunąć zwierzaka <strong id="animalName"></strong>?</p>
                    <p class="text-danger">Ta operacja jest nieodwracalna!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <form method="post" action="">
                        <input type="hidden" name="animal_id" id="animalIdInput" value="">
                        <button type="submit" name="delete_animal" class="btn btn-danger">Usuń</button>
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
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const animalId = button.getAttribute('data-animal-id');
                    const animalName = button.getAttribute('data-animal-name');
                    
                    const animalNameElement = document.getElementById('animalName');
                    const animalIdInput = document.getElementById('animalIdInput');
                    
                    animalNameElement.textContent = animalName;
                    animalIdInput.value = animalId;
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#animalsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/pl.json"
                },
                "responsive": true,
                "order": [[0, "desc"]],
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Wszystkie"]]
            });
            
            // Przekierowanie po kliknięciu na nazwę zwierzaka
            $(document).on('click', '.animal-name', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                window.location.href = url;
            });
            
            // Przekierowanie po kliknięciu na przycisk szczegółów
            $(document).on('click', '.btn-success', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                window.location.href = url;
            });
            
            // Potwierdzenie usunięcia
            $(document).on('click', '.delete-animal', function(e) {
                e.preventDefault();
                if (confirm('Czy na pewno chcesz usunąć to zwierzę?')) {
                    // Tutaj można dodać kod do usuwania
                    const animalId = $(this).data('id');
                    alert('Funkcja usuwania nie została jeszcze zaimplementowana. ID zwierzęcia: ' + animalId);
                }
            });
        });
    </script>
</body>
</html>