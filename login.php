<?php
session_start();

// Sprawdzenie, czy użytkownik jest już zalogowany
/*if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: index.php'); // Przekierowanie użytkownika do strony głównej
    exit();
}*/

// Sprawdzenie, czy użytkownik wysłał formularz logowania
if (isset($_POST['login'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    
    // Sprawdzenie, czy podane dane logowania są poprawne
    if ($login === 'test123' && $password === 'student') {
        // Ustawienie zmiennej sesyjnej oznaczającej zalogowanie użytkownika
        $_SESSION['logged_in'] = true;
        
        // Przekierowanie użytkownika do strony autoryzacji dwupoziomowej
        header('Location: two_factor_auth.php');
        exit();
    } else {
        $error_message = 'Niepoprawny login lub hasło'; // Komunikat błędu
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logowanie</title>
</head>
<body>
    <?php if (isset($error_message)): ?>
        <div><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <form method="post">
        <table>
            <tr>
                <td>Login:</td>
                <td><input type="text" name="login" required></td>
            </tr>
            <tr>
                <td>Hasło:</td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td colspan="2"><button type="submit">Zaloguj się</button></td>
            </tr>
        </table>
    </form>
</body>
</html>

