<?php
session_start();
$host = 'localhost'; $db = 'mosolygos'; $user = 'root'; $pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // MÓDOSÍTOTT LEKÉRDEZÉS: Összekötjük a két táblát
   $sql = "SELECT orders.*, 
               order_items.food_name, 
               order_items.quantity, 
               order_items.price_at_order 
        FROM orders 
        JOIN order_items ON orders.id = order_items.order_id 
        ORDER BY orders.id DESC";
            
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Hiba: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin - Rendelések</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2 class="mb-4">Beérkezett rendelések</h2>
    <table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Vevő neve</th>
            <th>Telefon</th> <th>Cím</th>
            <th>Rendelt étel</th>
            <th>Menny.</th>
            <th>Ár (Ft)</th>
            <th>Szállítási idő</th>
            <th>Státusz</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td>#<?php echo $o['id']; ?></td>
            <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
            
            <td><?php echo htmlspecialchars($o['customer_phone']); ?></td>
            
            <td><?php echo htmlspecialchars($o['customer_address']); ?></td>
            <td><strong><?php echo htmlspecialchars($o['food_name']); ?></strong></td>
            <td><?php echo $o['quantity']; ?> db</td>
            <td><?php echo number_format($o['price_at_order'], 0, ',', ' '); ?> Ft</td>
            <td><?php echo $o['delivery_date']; ?></td>
            
            <td>
                <?php 
                    // Színkódolás a státuszhoz
                    $color = ($o['status'] == 'pending') ? 'bg-warning text-dark' : 'bg-success';
                ?>
                <span class="badge <?php echo $color; ?>">
                    <?php echo htmlspecialchars($o['status']); ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>