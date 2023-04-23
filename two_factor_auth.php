<?php
session_start();

include_once "classes/Pdo_.php";
/*
// Sprawdzenie, czy użytkownik jest już zalogowany
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php'); // Przekierowanie użytkownika do strony logowania
    exit();
}

// Sprawdzenie, czy użytkownik wysłał formularz autoryzacji
if (isset($_POST['otp'])) {
    $otp = $_POST['otp'];
    
    // Sprawdzenie, czy podany kod OTP jest poprawny
    if ($otp === $_SESSION['otp']) {
        // Ustawienie zmiennej sesyjnej oznaczającej autoryzację użytkownika
        $_SESSION['authenticated'] = true;
        
        // Przekierowanie użytkownika do strony głównej
        header('Location: index.php');
        exit();
    } else {
        $error_message = 'Niepoprawny kod OTP'; // Komunikat błędu
    }
}

// Generowanie i zapisywanie kodu OTP w zmiennej sesyjnej
$otp = rand(100000, 999999); // Generowanie losowego 6-cyfrowego kodu
$_SESSION['otp'] = $otp;

// Wysłanie kodu OTP na adres email użytkownika (tutaj tylko symulacja)
$email = 'test@test.com'; // Adres email użytkownika
$message = 'Twój kod OTP to: ' . $otp; // Treść wiadomości email
mail($email, 'Kod OTP', $message); // Wysłanie wiadomości email*/


if (isset($_POST['login'])) {
    // Jeśli został naciśnięty przycisk "Log in", wykonaj poniższy kod

    // Tworzenie obiektu klasy Pdo_
    $pdo = new Pdo_();

    // Pobieranie wartości z formularza logowania
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Wywołanie metody log_user_in klasy Pdo_
    $pdo->log_user_in($login, $password);
    
    
    // Generowanie i zapisywanie kodu OTP w zmiennej sesyjnej
$otp = rand(100000, 999999); // Generowanie losowego 6-cyfrowego kodu
$_SESSION['otp'] = $otp;

// Wysłanie kodu OTP na adres email użytkownika (tutaj tylko symulacja)
$email = 'test@test.com'; // Adres email użytkownika
$message = 'Twój kod OTP to: ' . $otp; // Treść wiadomości email
mail($email, 'Kod OTP', $message); // Wysłanie wiadomości email*/
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Autoryzacja dwupoziomowa</title>
</head>
<body>
    <?php if (isset($error_message)): ?>
        <div><?php echo $error_message; ?></div>
    <?php endif; ?>
<form method="post">
    <table>
        <tr>
            <td>Podaj kod OTP:</td>
            <td><input type="text" name="otp" required></td>
        </tr>
        <tr>
            <td colspan="2"><button type="submit">Autoryzuj</button></td>
        </tr>
    </table>
</form>
</body>
</html>