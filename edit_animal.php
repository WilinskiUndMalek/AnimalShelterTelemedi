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
$message = '';
$messageType = '';

// Pobierz dane zwierzaka do edycji
$animal = $api->getAnimalDetails($animalId);

// Sprawdź strukturę odpowiedzi - może dane są zagnieżdżone w innym kluczu
if (isset($animal['animal'])) {
    $animal = $animal['animal'];
} elseif (isset($animal['result'])) {
    $animal = $animal['result'];
}

// Jeśli nie znaleziono zwierzaka, przekieruj do listy
if (empty($animal)) {
    header('Location: animals.php');
    exit;
}

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobierz dane z formularza
    $animalData = [
        'animal_id' => $animalId,
        'name' => $_POST['name'] ?? '',
        'species' => $_POST['species'] ?? '',
        'breed' => $_POST['breed'] ?? '',
        'age' => $_POST['age'] ?? '',
        'adoption_status' => $_POST['adoption_status'] ?? '',
        'arrival_date' => $_POST['arrival_date'] ?? '',
        'description' => $_POST['description'] ?? '',
        'medical_conditions' => $_POST['medical_conditions'] ?? '',
        'photo' => $_POST['photo'] ?? '',
        'video' => $_POST['video'] ?? '',
        'weight' => floatval($_POST['weight'] ?? 0),
        'vaccinations' => $_POST['vaccinations'] ?? '',
        'special_needs' => $_POST['special_needs'] ?? '',
        'behavioral_notes' => $_POST['behavioral_notes'] ?? ''
    ];
    
    // Aktualizuj zwierzaka przez API
    $result = $api->updateAnimal($animalId, $animalData);
    
    if (!isset($result['error'])) {
        $message = 'Dane zwierzaka zostały pomyślnie zaktualizowane!';
        $messageType = 'success';
        // Odśwież dane zwierzaka
        $animal = $api->getAnimalDetails($animalId);
        if (isset($animal['animal'])) {
            $animal = $animal['animal'];
        } elseif (isset($animal['result'])) {
            $animal = $animal['result'];
        }
    } else {
        $message = 'Wystąpił błąd podczas aktualizacji danych zwierzaka: ' . ($result['message'] ?? 'Nieznany błąd');
        $messageType = 'danger';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edytuj Zwierzaka - Schronisko dla Zwierząt</title>
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
        .btn-submit {
            background-color: #1cc88a;
        }
        .btn-submit:hover {
            background-color: #18a978;
        }
        .form-label {
            font-weight: 500;
            color: #333;
        }
        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        .form-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .form-section h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 30px;
        }
        .preview-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-top: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
            <h2><i class="fas fa-edit me-2"></i>Edytuj Zwierzaka</h2>
            <div>
                <a href="animal_details.php?id=<?php echo $animalId; ?>" class="btn btn-action btn-back"><i class="fas fa-eye me-2"></i>Podgląd</a>
                <a href="animals.php" class="btn btn-action btn-back"><i class="fas fa-arrow-left me-2"></i>Powrót</a>
            </div>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-section">
                <h3><i class="fas fa-info-circle me-2"></i>Podstawowe informacje</h3>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Imię*</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($animal['name'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="species" class="form-label">Gatunek*</label>
                        <select class="form-select" id="species" name="species" required>
                            <option value="">Wybierz gatunek</option>
                            <option value="Pies" <?php echo ($animal['species'] ?? '') === 'Pies' ? 'selected' : ''; ?>>Pies</option>
                            <option value="Kot" <?php echo ($animal['species'] ?? '') === 'Kot' ? 'selected' : ''; ?>>Kot</option>
                            <option value="Królik" <?php echo ($animal['species'] ?? '') === 'Królik' ? 'selected' : ''; ?>>Królik</option>
                            <option value="Świnka morska" <?php echo ($animal['species'] ?? '') === 'Świnka morska' ? 'selected' : ''; ?>>Świnka morska</option>
                            <option value="Chomik" <?php echo ($animal['species'] ?? '') === 'Chomik' ? 'selected' : ''; ?>>Chomik</option>
                            <option value="Ptak" <?php echo ($animal['species'] ?? '') === 'Ptak' ? 'selected' : ''; ?>>Ptak</option>
                            <option value="Inne" <?php echo ($animal['species'] ?? '') === 'Inne' ? 'selected' : ''; ?>>Inne</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="breed" class="form-label">Rasa</label>
                        <input type="text" class="form-control" id="breed" name="breed" value="<?php echo htmlspecialchars($animal['breed'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="age" class="form-label">Wiek</label>
                        <input type="text" class="form-control" id="age" name="age" placeholder="np. 2 lata" value="<?php echo htmlspecialchars($animal['age'] ?? ''); ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="weight" class="form-label">Waga (kg)</label>
                        <input type="number" class="form-control" id="weight" name="weight" step="0.1" min="0" value="<?php echo htmlspecialchars($animal['weight'] ?? '0'); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="arrival_date" class="form-label">Data przyjęcia</label>
                        <input type="date" class="form-control" id="arrival_date" name="arrival_date" value="<?php echo htmlspecialchars($animal['arrival_date'] ?? ''); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="adoption_status" class="form-label">Status adopcji*</label>
                    <select class="form-select" id="adoption_status" name="adoption_status" required>
                        <option value="">Wybierz status</option>
                        <option value="Dostępny" <?php echo ($animal['adoption_status'] ?? '') === 'Dostępny' ? 'selected' : ''; ?>>Dostępny</option>
                        <option value="W trakcie adopcji" <?php echo ($animal['adoption_status'] ?? '') === 'W trakcie adopcji' ? 'selected' : ''; ?>>W trakcie adopcji</option>
                        <option value="Zaadoptowany" <?php echo ($animal['adoption_status'] ?? '') === 'Zaadoptowany' ? 'selected' : ''; ?>>Zaadoptowany</option>
                        <option value="Kwarantanna" <?php echo ($animal['adoption_status'] ?? '') === 'Kwarantanna' ? 'selected' : ''; ?>>Kwarantanna</option>
                        <option value="Leczenie" <?php echo ($animal['adoption_status'] ?? '') === 'Leczenie' ? 'selected' : ''; ?>>Leczenie</option>
                    </select>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-file-medical me-2"></i>Informacje medyczne</h3>
                <div class="mb-3">
                    <label for="medical_conditions" class="form-label">Stan zdrowia</label>
                    <textarea class="form-control" id="medical_conditions" name="medical_conditions" rows="3"><?php echo htmlspecialchars($animal['medical_conditions'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="vaccinations" class="form-label">Szczepienia</label>
                    <textarea class="form-control" id="vaccinations" name="vaccinations" rows="2"><?php echo htmlspecialchars($animal['vaccinations'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="special_needs" class="form-label">Specjalne potrzeby</label>
                    <textarea class="form-control" id="special_needs" name="special_needs" rows="2"><?php echo htmlspecialchars($animal['special_needs'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-align-left me-2"></i>Opis i zachowanie</h3>
                <div class="mb-3">
                    <label for="description" class="form-label">Opis</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($animal['description'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="behavioral_notes" class="form-label">Notatki o zachowaniu</label>
                    <textarea class="form-control" id="behavioral_notes" name="behavioral_notes" rows="3"><?php echo htmlspecialchars($animal['behavioral_notes'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-image me-2"></i>Media</h3>
                <div class="mb-3">
                    <label for="photo" class="form-label">URL zdjęcia</label>
                    <input type="url" class="form-control" id="photo" name="photo" placeholder="https://example.com/photo.jpg" value="<?php echo htmlspecialchars($animal['photo'] ?? ''); ?>">
                    <div class="form-text">Wprowadź pełny URL do zdjęcia zwierzaka.</div>
                    <?php if (!empty($animal['photo'])): ?>
                        <div class="mt-2">
                            <p>Aktualne zdjęcie:</p>
                            <img src="<?php echo htmlspecialchars($animal['photo']); ?>" alt="Zdjęcie zwierzaka" class="preview-image" style="max-height: 200px;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="video" class="form-label">URL wideo</label>
                    <input type="url" class="form-control" id="video" name="video" placeholder="https://youtube.com/embed/xyz" value="<?php echo htmlspecialchars($animal['video'] ?? ''); ?>">
                    <div class="form-text">Wprowadź pełny URL do wideo zwierzaka (najlepiej link do osadzonego wideo).</div>
                    <?php if (!empty($animal['video'])): ?>
                        <div class="mt-2">
                            <p>Aktualne wideo:</p>
                            <div class="video-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%;">
                                <iframe src="<?php echo htmlspecialchars($animal['video']); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border:0;" allowfullscreen></iframe>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="animal_details.php?id=<?php echo $animalId; ?>" class="btn btn-secondary">Anuluj</a>
                <button type="submit" class="btn btn-action btn-submit"><i class="fas fa-save me-2"></i>Zapisz zmiany</button>
            </div>
        </form>
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