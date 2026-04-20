<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: rendeles.php");
    exit;
}

$host = 'localhost'; $db = 'mosolygos'; $user = 'root'; $pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt_foods = $pdo->prepare("SELECT id, name, price FROM foods WHERE id IN ($placeholders)");
    $stmt_foods->execute($ids);
    $foods_data = $stmt_foods->fetchAll(PDO::FETCH_ASSOC);

    $total_items_price = 0;
    $etelek_szoveg = "";
    foreach ($foods_data as $food) {
        $qty = $_SESSION['cart'][$food['id']];
        $total_items_price += $food['price'] * $qty;
        $etelek_szoveg .= $food['name'] . " (" . $qty . " db)\n";
    }

    $delivery_fee = ($total_items_price < 50000) ? 5000 : 0;
    $total_all = $total_items_price + $delivery_fee;

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, customer_email, customer_phone, customer_address, delivery_date, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'] ?? null,
        $_POST['customer_name'], 
        $_POST['customer_email'], 
        $_POST['customer_phone'], 
        $_POST['customer_address'], 
        $_POST['delivery_date'], 
        $total_all
    ]);
    $order_id = $pdo->lastInsertId();

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'mosolygosbufe2008@gmail.com'; 
    $mail->Password   = 'leca uwaj snrc kqry'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('no-reply@mosolygos.hu', 'Mosolygos Etlap');
    $mail->addAddress('A_TE_GMAIL_CIMED@gmail.com'); 

    $mail->isHTML(false);
    $mail->Subject = "Uj rendeles: #" . $order_id;
    $mail->Body    = "Rendelő: " . $_POST['customer_name'] . "\n"
                   . "Telefon: " . $_POST['customer_phone'] . "\n"
                   . "Cím: " . $_POST['customer_address'] . "\n"
                   . "Ételek:\n" . $etelek_szoveg . "\n"
                   . "Összesen: " . number_format($total_all, 0, ',', ' ') . " Ft";

    $mail->send();

    unset($_SESSION['cart']);

} catch (Exception $e) {
    die("Hiba: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Sikeres rendelés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-center py-5">
    <div class="container bg-white p-5 rounded shadow" style="max-width: 500px;">
        <h2 class="text-success">Rendelés elküldve!</h2>
        <p>A visszaigazolást megkapod e-mailben.</p>
        <a href="rendeles.php" class="btn btn-success mt-3">Vissza az étlaphoz</a>
    </div>
</body>
</html>