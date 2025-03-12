<?php
class ApiClient {
    private $baseUrl;
    private $authToken;

    public function __construct($baseUrl, $authToken = null) {
        $this->baseUrl = $baseUrl;
        $this->authToken = $authToken;
    }

    public function login($email, $password) {
        $ch = curl_init($this->baseUrl . LOGIN_ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'email' => $email,
            'password' => $password
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (isset($data['authToken'])) {
            $this->authToken = $data['authToken'];
            $_SESSION['authToken'] = $data['authToken'];
            return true;
        }
        return false;
    }

    public function getAnimals() {
        $ch = curl_init($this->baseUrl . ANIMALS_ENDPOINT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . $this->authToken
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function getAnimalDetails($id) {
        // Używamy stałej ANIMAL_DETAILS_ENDPOINT zamiast bezpośredniej ścieżki
        $url = $this->baseUrl . ANIMAL_DETAILS_ENDPOINT . '/' . $id;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->authToken
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Zapisz informacje debugowania
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        file_put_contents('curl_debug.log', "URL: $url\nResponse Code: $httpCode\nError: $error\nInfo: " . print_r($info, true) . "\nResponse: $response\n\n", FILE_APPEND);
        
        curl_close($ch);
        
        if ($httpCode == 200) {
            return json_decode($response, true);
        } else {
            // W przypadku błędu zwracamy pustą tablicę
            return [];
        }  
    }
    public function addAnimal($animalData) {
        // Używamy stałej ADD_ANIMAL_ENDPOINT zamiast bezpośredniej ścieżki
        $url = $this->baseUrl . ADD_ANIMAL_ENDPOINT;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->authToken
        ];
        
        // Konwertujemy dane na format JSON
        $data = json_encode($animalData);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        // Zapisz informacje debugowania
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        file_put_contents('curl_add_animal_debug.log', "URL: $url\nData: $data\nResponse Code: $httpCode\nError: $error\nInfo: " . print_r($info, true) . "\nResponse: $response\n\n", FILE_APPEND);
        
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            // W przypadku błędu zwracamy informację o błędzie
            return [
                'error' => true,
                'message' => 'Błąd podczas dodawania zwierzaka. Kod HTTP: ' . $httpCode,
                'response' => json_decode($response, true)
            ];
        }
    }

    public function updateAnimal($id, $animalData) {
        // Używamy stałej UPDATE_ANIMAL_ENDPOINT zamiast bezpośredniej ścieżki
        $url = $this->baseUrl . UPDATE_ANIMAL_ENDPOINT . '/' . $id;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->authToken
        ];
        
        // Konwertujemy dane na format JSON
        $data = json_encode($animalData);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        // Zapisz informacje debugowania
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        file_put_contents('curl_update_animal_debug.log', "URL: $url\nData: $data\nResponse Code: $httpCode\nError: $error\nInfo: " . print_r($info, true) . "\nResponse: $response\n\n", FILE_APPEND);
        
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            // W przypadku błędu zwracamy informację o błędzie
            return [
                'error' => true,
                'message' => 'Błąd podczas aktualizacji zwierzaka. Kod HTTP: ' . $httpCode,
                'response' => json_decode($response, true)
            ];
        }
    }

    public function deleteAnimal($id) {
        // Używamy stałej DELETE_ANIMAL_ENDPOINT zamiast bezpośredniej ścieżki
        $url = $this->baseUrl . DELETE_ANIMAL_ENDPOINT . '/' . $id;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->authToken
        ];
        
        // Przygotowujemy dane do wysłania
        $data = json_encode(['animal_id' => $id]);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        // Zapisz informacje debugowania
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        file_put_contents('curl_delete_animal_debug.log', "URL: $url\nData: $data\nResponse Code: $httpCode\nError: $error\nInfo: " . print_r($info, true) . "\nResponse: $response\n\n", FILE_APPEND);
        
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            // W przypadku błędu zwracamy informację o błędzie
            return [
                'error' => true,
                'message' => 'Błąd podczas usuwania zwierzaka. Kod HTTP: ' . $httpCode,
                'response' => json_decode($response, true)
            ];
        }
    }
    public function getStaff() {
        $url = $this->baseUrl . STAFF_ENDPOINT;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->authToken
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Zapisz informacje debugowania
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        file_put_contents('curl_staff_debug.log', "URL: $url\nResponse Code: $httpCode\nError: $error\nInfo: " . print_r($info, true) . "\nResponse: $response\n\n", FILE_APPEND);
        
        curl_close($ch);
        
        if ($httpCode == 200) {
            return json_decode($response, true);
        } else {
            // W przypadku błędu zwracamy pustą tablicę
            return [];
        }
    }

    public function getVolunteers() {
        $url = $this->baseUrl . VOLUNTEER_ENDPOINT;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $this->authToken
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Zapisz informacje debugowania
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        file_put_contents('curl_volunteers_debug.log', "URL: $url\nResponse Code: $httpCode\nError: $error\nInfo: " . print_r($info, true) . "\nResponse: $response\n\n", FILE_APPEND);
        
        curl_close($ch);
        
        if ($httpCode == 200) {
            return json_decode($response, true);
        } else {
            // W przypadku błędu zwracamy pustą tablicę
            return [];
        }
    }
}