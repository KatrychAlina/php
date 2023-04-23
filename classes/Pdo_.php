<?php

require_once 'C:\xampp\htdocs\app\htmlpurifier-4.15.0\library\HTMLPurifier.auto.php';
require_once 'Aes.php';
require_once 'C:\xampp\htdocs\app\PHPMailer-master\src\PHPMailer.php';

class Pdo_
{
    private $db;
    private $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $this->purifier = new HTMLPurifier($config);
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=news', 'app_user', 'student');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die();
        }
    }

public function add_user($login, $email, $password)
{
    $login = $this->purifier->purify($login);
    $email = $this->purifier->purify($email);

    // Hash the password using Argon2id
    $password_hash = password_hash($password, PASSWORD_ARGON2ID);

     $otp_code = $this->generate_code();
    $otp_timelife = date('Y-m-d H:i:s', strtotime('+10 minutes')); // OTP code is valid for 10 minutes

    try {
        $sql = "INSERT INTO user(login, email, hash, id_status, password_form, otp_code, otp_timelife)
            VALUES (:login, :email, :hash, :id_status, :password_form, :otp_code, :otp_timelife)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':hash', $password_hash);
        $stmt->bindParam(':id_status', $id_status);
        $stmt->bindParam(':password_form', $password_form);
        $stmt->bindParam(':otp_code', $otp_code);
        $stmt->bindParam(':otp_timelife', $otp_timelife);
        $stmt->execute();
        
        $this->send_otp_code($email, $otp_code); // Send OTP code to user's email
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

public function log_user_in($login, $password) {
    $login = $this->purifier->purify($login);

    $sql = "SELECT * FROM user WHERE login = :login";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(array(':login' => $login));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $hash = $row['hash'];
        if (password_verify($password, $hash)) {
            // Ask user for OTP code
            $_SESSION['otp_login'] = $login;
            header('Location: otp_login.php');
        } else {
            // nieprawidłowe hasło
            echo "Incorrect password";
        }
    } else {
        // użytkownik o podanym loginie nie istnieje
        echo "User does not exist";
    }
}

public function log_2F_step2($login, $code)
    {
        $login = $this->purifier->purify($login);
        $code = $this->purifier->purify($code);

        try {
            $sql = "SELECT id, login, sms_code, code_timelife FROM user WHERE login=:login";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['login' => $login]);
            $user_data = $stmt->fetch();

            if ($code == $user_data['sms_code'] && time() < strtotime($user_data['code_timelife'])) {
                // login successful
                echo 'Login successful<BR/>';
                return true;
            } else {
                echo 'login FAILED<BR/>';
 return false;
 }
 } catch (Exception $e) {
 print 'Exception' . $e->getMessage();
 }
 }
 
 public function send_email($to, $subject, $message)
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';
        $mail->Password = 'your-email-password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Your Name');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;

        if ($mail->send()) {
            echo 'Email sent';
        } else {
            echo 'Email not sent';
        }
    }

    
    public function generate_code()
{
    $length = 6; // Length of OTP code
    $chars = "0123456789"; // Characters to include in OTP code
    $code = "";
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $code;
}
public function send_otp_code($email, $code)
{
    $subject = 'Your OTP code for logging in to MyApp';
    $message = 'Your OTP code is: ' . $code;
    $this->send_email($email, $subject, $message);
}


public function change_password($login, $old_password, $new_password)
{
    $login = $this->purifier->purify($login);

    try {
        $sql = "SELECT id,hash,salt FROM user WHERE login=:login";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['login' => $login]);
        $user_data = $stmt->fetch();

        $old_password = hash('sha512', $old_password . $user_data['salt']);
        if ($old_password != $user_data['hash']) {
            echo 'Old password is incorrect<BR/>';
            return;
        }

        $salt = bin2hex(random_bytes(32));
        $new_hash = hash('sha512', $new_password . $salt);

        $sql = "UPDATE user SET hash=:hash, salt=:salt WHERE id=:id";
        $data = [
            'hash' => $new_hash,
            'salt' => $salt,
            'id' => $user_data['id'],
        ];
        $this->db->prepare($sql)->execute($data);

        echo 'Password changed successfully<BR/>';
    } catch (Exception $e) {
        
        print 'Exception' . $e->getMessage();
    }
}

}
