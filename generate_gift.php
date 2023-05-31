<?php
    // Pobieranie danych z formularza POST
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $interests = $_POST['interests'];

    // Tworzenie wiadomości, która będzie wysyłana do modelu GPT-3.5-turbo
    $message = "Podaj mi pomysł na prezent dla ".$age." letniej/letniego ";
    $message .= ($gender == 'male') ? "mezczyzny " : (($gender == 'female') ? "kobiety " : "osoby ");
    $message .= "która interesuje się ".$interests.". Podaj same przykłady bez zbędnego opisu.";

    // Inicjalizacja sesji cURL
    $ch = curl_init();

    // Ustawienie opcji dla sesji cURL
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions'); // Adres URL, do którego zostanie wysłane żądanie
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Zwraca wynik jako ciąg, zamiast go wypisywać
    curl_setopt($ch, CURLOPT_POST, 1); // Ustawienie metody żądania na POST

    // Utworzenie tablicy nagłówków do wysłania wraz z żądaniem
    $headers = array();
    $headers[] = 'Content-Type: application/json'; // Informuje serwer, że dane są w formacie JSON
    $headers[] = 'Authorization: Bearer MIEJSCE_NA_KLUCZ_API'; // Klucz API dla autoryzacji PROSZĘ GO PODAĆ
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Dodaje nagłówki do żądania

    // Utworzenie danych POST do wysłania
    $post_data = array(
        'model' => 'gpt-3.5-turbo',
        'messages' => array(
            array(
                'role' => 'system',
                'content' => 'You are a helpful assistant.'
            ),
            array(
                'role' => 'user',
                'content' => $message
            )
        )
    );

    // Dodanie danych POST do żądania
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));

    // Wykonanie żądania i zapisanie wyniku
    $result = curl_exec($ch);
    // Sprawdzenie, czy wystąpił błąd
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    // Zamknięcie sesji cURL
    curl_close($ch);

    // Przetwarzanie wyniku
    $result_data = json_decode($result, true);

    // Sprawdzenie, czy w odpowiedzi jest treść wiadomości
    if (isset($result_data['choices'][0]['message']['content'])) {
        $messageContent = $result_data['choices'][0]['message']['content'];
        // Usuwanie niepotrzebnych białych znaków i normalizacja znaków nowego wiersza
        $messageContent = trim($messageContent);
        $messageContent = preg_replace('/\n+/', "\n", $messageContent);
        // Podział treści wiadomości na pomysły na prezenty
        $gift_ideas = explode("\n", $messageContent);

        // Wysyłanie pomysłów na prezenty z powrotem do klienta w formacie JSON
        echo json_encode($gift_ideas);
    } else {
        // Wysyłanie wiadomości o błędzie, jeśli nie znaleziono pomysłów na prezenty
        echo json_encode(array('Error: No gift ideas found'));
    }
?>
