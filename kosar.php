<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$host = 'localhost';
$db   = 'mosolygos';
$user = 'root';
$pass = ''; 

try {
     $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
} catch (Exception $e) {
     die("Adatbázis hiba!");
}

$cart_items = [];
$total_sum = 0; // Ez csak az ételek ára

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    $sql = "SELECT * FROM foods WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $p) {
        $quantity = $_SESSION['cart'][$p['id']];
        if ($quantity <= 0) continue; 

        $subtotal = $p['price'] * $quantity;
        $total_sum += $subtotal;
        
        $cart_items[] = [
            'id' => $p['id'],
            'name' => $p['name'],
            'price' => $p['price'],
            'qty' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

// --- SZÁLLÍTÁSI DÍJ LOGIKA ---
$limit = 50000;
$delivery_fee = ($total_sum > 0 && $total_sum < $limit) ? 5000 : 0;
$final_total = $total_sum + $delivery_fee;
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Kosár</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<div class="container my-5 shadow p-4 bg-white rounded" style="max-width: 650px;">
    <h2 class="mb-4 text-center">Rendelésed összesítése</h2>

    <?php if (empty($cart_items)): ?>
        <div class="text-center">
            <p>A kosarad jelenleg üres.</p>
            <a href="rendeles.php" class="btn btn-primary">Vissza az étlaphoz</a>
        </div>
    <?php else: ?>
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Étel</th>
                    <th class="text-center">Mennyiség</th>
                    <th class="text-end">Összesen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="update_cart.php?action=minus&id=<?php echo $item['id']; ?>" class="btn btn-outline-danger">-</a>
                            <span class="btn btn-light disabled" style="width: 40px;"><?php echo $item['qty']; ?></span>
                            <a href="update_cart.php?action=plus&id=<?php echo $item['id']; ?>" class="btn btn-outline-success">+</a>
                        </div>
                    </td>
                    <td class="text-end"><?php echo number_format($item['subtotal'], 0, ',', ' '); ?> Ft</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="border-top pt-3 mt-3">
            <div class="d-flex justify-content-between mb-2">
                <span>Részösszeg:</span>
                <span><?php echo number_format($total_sum, 0, ',', ' '); ?> Ft</span>
            </div>
            <div class="d-flex justify-content-between mb-2 <?php echo ($delivery_fee > 0) ? 'text-danger' : 'text-success fw-bold'; ?>">
                <span>Szállítási díj:</span>
                <span><?php echo ($delivery_fee > 0) ? number_format($delivery_fee, 0, ',', ' ') . " Ft" : "Ingyenes"; ?></span>
            </div>

            <?php if ($delivery_fee > 0): ?>
                <div class="alert alert-warning py-2 mb-3" style="font-size: 0.85rem;">
                    🚚 Még <strong><?php echo number_format($limit - $total_sum, 0, ',', ' '); ?> Ft</strong> és a szállításod <b>ingyenes</b> lesz!
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between fs-4 fw-bold text-success border-top pt-2">
                <span>Végösszeg:</span>
                <span><?php echo number_format($final_total, 0, ',', ' '); ?> Ft</span>
            </div>
        </div>

        <div class="d-grid gap-2 mt-4">
            <a href="idopont.php" class="btn btn-success btn-lg">Tovább az időpont kiválasztásához</a>
            <a href="rendeles.php" class="btn btn-outline-secondary">Étel hozzáadása</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>