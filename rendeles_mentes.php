<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: rendeles.php");
    exit;
}

if (!isset($_POST['delivery_date'])) {
    die("Hiba: Hiányzik az időpont!");
}

$host = 'localhost'; $db = 'mosolygos'; $user = 'root'; $pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Árak ÉS NEVEK lekérése
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt_foods = $pdo->prepare("SELECT id, name, price FROM foods WHERE id IN ($placeholders)");
    $stmt_foods->execute($ids);
    $foods_data = $stmt_foods->fetchAll(PDO::FETCH_ASSOC);

    $total_items_price = 0; // Csak az ételek ára
    $etlap_szoveg = ""; 

    foreach ($foods_data as $food) {
        $id = $food['id'];
        $qty = $_SESSION['cart'][$id];
        $subtotal = $food['price'] * $qty;
        $total_items_price += $subtotal;
        $etlap_szoveg .= "- " . $food['name'] . " (" . $qty . " db) - " . number_format($subtotal, 0, ',', ' ') . " Ft\n";
    }

    // --- SZÁLLÍTÁSI DÍJ KISZÁMÍTÁSA ---
    $limit = 50000;
    $delivery_fee = ($total_items_price < $limit) ? 5000 : 0;
    $total_all = $total_items_price + $delivery_fee; // Ez a végső fizetendő összeg

    // 2. FŐ RENDELÉS MENTÉSE (A $total_all már tartalmazza a szállítási díjat)
    $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, delivery_date, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['customer_name'], 
        $_POST['customer_email'], 
        $_POST['customer_phone'], 
        $_POST['customer_address'], 
        $_POST['delivery_date'], 
        $total_all
    ]);
    $order_id = $pdo->lastInsertId();

    // 3. TÉTELEK MENTÉSE
    $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, food_id, food_name, quantity, price_at_order) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($foods_data as $food) {
        $mentendo_nev = !empty($food['name']) ? $food['name'] : "HIBA: Nincs név (ID: ".$food['id'].")";
        
        $stmt_item->execute([
            $order_id, 
            $food['id'], 
            $mentendo_nev, 
            $_SESSION['cart'][$food['id']], 
            $food['price']
        ]);
    }

    // 4. AZ EMAIL ÖSSZEÁLLÍTÁSA (Részletezve a szállítási díjat)
    $to = "a-te-emailed@gmail.com"; 
    $subject = "ÚJ RENDELÉS: #" . $order_id . " - " . $_POST['customer_name'];
    
    $message = "Szia! Új rendelésed érkezett!\n\n"
             . "--- VEVŐ ADATAI ---\n"
             . "Név: " . $_POST['customer_name'] . "\n"
             . "IDŐPONT: " . $_POST['delivery_date'] . "\n"
             . "HELYSZÍN: " . $_POST['customer_address'] . "\n"
             . "TELEFON: " . $_POST['customer_phone'] . "\n\n"
             . "--- RENDELT ÉTELEK ---\n"
             . $etlap_szoveg
             . "---------------------------\n"
             . "Ételek ára: " . number_format($total_items_price, 0, ',', ' ') . " Ft\n"
             . "Szállítási díj: " . ($delivery_fee > 0 ? number_format($delivery_fee, 0, ',', ' ') . " Ft" : "Ingyenes") . "\n"
             . "ÖSSZESEN FIZETENDŐ: " . number_format($total_all, 0, ',', ' ') . " Ft";

    $headers = "From: rendeles@mosolygos.hu\r\nContent-Type: text/plain; charset=UTF-8";
    @mail($to, $subject, $message, $headers);

    // Kosár ürítése a sikeres mentés után
    unset($_SESSION['cart']);

} catch (Exception $e) {
    die("Hiba történt a mentés során: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Sikeres rendelés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5 text-center shadow p-5 bg-white rounded" style="max-width: 500px;">
        <div class="mb-4 text-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
        </div>
        <h2 class="mb-3">Köszönjük a rendelést!</h2>
        <p class="text-muted">A rendelésedet rögzítettük.</p>
        <p>Fizetendő összeg: <strong><?php echo number_format($total_all, 0, ',', ' '); ?> Ft</strong></p>
        <hr>
        <p>Rendelési azonosító: #<strong><?php echo $order_id; ?></strong></p>
        <a href="rendeles.php" class="btn btn-success mt-3">Vissza az étlaphoz</a>
    </div>
</body>
</html>