<?php
session_start();
require_once 'config.php';
require_once 'ApiClient.php';

if (!isset($_SESSION['authToken'])) {
    header('Location: login.php');
    exit;
}

// Sprawdź czy ID zwierzaka zostało przekazane
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: animals.php');
    exit;
}

$animalId = $_GET['id'];
$api = new ApiClient(API_BASE_URL, $_SESSION['authToken']);

// Pobierz dane zwierzaka
$animal = $api->getAnimalDetails($animalId);

// Dodaj debugowanie - zapisz odpowiedź do pliku
file_put_contents('api_response_debug.log', print_r($animal, true));

// Sprawdź strukturę odpowiedzi - może dane są zagnieżdżone w innym kluczu
if (isset($animal['animal'])) {
    $animal = $animal['animal'];
} elseif (isset($animal['result'])) {
    $animal = $animal['result'];
}

// Nie przekierowuj, nawet jeśli dane są puste - zamiast tego pokaż komunikat
?>

<!DOCTYPE html>
<html>
<head>
    <title>Szczegóły Zwierzaka - Schronisko dla Zwierząt</title>
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
            margin-left: 10px;
        }
        .btn-action:hover {
            background-color: #375ad3;
            transform: translateY(-2px);
            color: white;
        }
        .btn-back {
            background-color: #6c757d;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
        .btn-edit {
            background-color: #1cc88a;
        }
        .btn-edit:hover {
            background-color: #18a978;
        }
        .animal-photo {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .animal-info {
            margin-bottom: 30px;
        }
        .animal-name {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        .animal-breed {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 20px;
        }
        .badge {
            padding: 8px 15px;
            font-weight: 500;
            border-radius: 30px;
            font-size: 0.9rem;
            margin-right: 10px;
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
        .info-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .info-item {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: 600;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .video-container {
        position: relative;
        padding-bottom: 56.25%; /* Proporcje 16:9 */
        height: 530px;
        overflow: hidden;
        margin-top: 20px;
        margin-bottom: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 10px;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
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
            <h2><i class="fas fa-paw me-2"></i>Szczegóły Zwierzaka</h2>
            <div>
                <a href="animals.php" class="btn btn-action btn-back"><i class="fas fa-arrow-left me-2"></i>Powrót</a>
                <a href="edit_animal.php?id=<?php echo htmlspecialchars($animal['id']); ?>" class="btn btn-action btn-edit"><i class="fas fa-edit me-2"></i>Edytuj</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6">
                <?php if (!empty($animal['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($animal['photo']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>" class="animal-photo">
                <?php else: ?>
                    <img src="https://via.placeholder.com/800x600?text=Brak+zdjęcia" alt="Brak zdjęcia" class="animal-photo">
                <?php endif; ?>
                
                <?php if (!empty($animal['video'])): ?>
                    <div class="video-container">
                        <iframe src="<?php echo htmlspecialchars($animal['video']); ?>" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-6">
                <div class="animal-info">
                    <h1 class="animal-name"><?php echo htmlspecialchars($animal['name']); ?></h1>
                    <div class="animal-breed"><?php echo htmlspecialchars($animal['species']); ?> - <?php echo htmlspecialchars($animal['breed']); ?></div>
                    
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
                    
                    <div class="info-section mt-4">
                        <h3><i class="fas fa-info-circle me-2"></i>Podstawowe informacje</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">Wiek:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($animal['age']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">Waga:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($animal['weight']); ?> kg</div>
                                </div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Data przyjęcia:</div>
                            <div class="info-value"><?php echo htmlspecialchars($animal['arrival_date']); ?></div>
                        </div>
                    </div>
                    
                    <div class="info-section">
                        <h3><i class="fas fa-file-medical me-2"></i>Informacje medyczne</h3>
                        <div class="info-item">
                            <div class="info-label">Stan zdrowia:</div>
                            <div class="info-value"><?php echo !empty($animal['medical_conditions']) ? htmlspecialchars($animal['medical_conditions']) : 'Brak informacji'; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Szczepienia:</div>
                            <div class="info-value"><?php echo !empty($animal['vaccinations']) ? htmlspecialchars($animal['vaccinations']) : 'Brak informacji'; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Specjalne potrzeby:</div>
                            <div class="info-value"><?php echo !empty($animal['special_needs']) ? htmlspecialchars($animal['special_needs']) : 'Brak'; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="info-section">
                    <h3><i class="fas fa-align-left me-2"></i>Opis</h3>
                    <div class="info-value">
                        <?php echo !empty($animal['description']) ? nl2br(htmlspecialchars($animal['description'])) : 'Brak opisu'; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="info-section">
                    <h3><i class="fas fa-paw me-2"></i>Zachowanie</h3>
                    <div class="info-value">
                        <?php echo !empty($animal['behavioral_notes']) ? nl2br(htmlspecialchars($animal['behavioral_notes'])) : 'Brak informacji o zachowaniu'; ?>
                    </div>
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
</body>
</html>