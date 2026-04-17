<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$kosar_db = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $mennyiseg) {
        $kosar_db += $mennyiseg;
    }
}

// 1. Kapcsolódás az adatbázishoz (PDO)
$host = 'localhost';
$db   = 'mosolygos';
$user = 'root';
$pass = ''; // XAMPP/WAMP esetén alapértelmezetten üres
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// 2. Ételek lekérése
$stmt = $pdo->query("SELECT * FROM foods");
$foods = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rendelés</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    /* Egy kis extra a gombnak, hogy jól nézzen ki */
    .nav-link{
  padding-top: 8px;
}
    .btn-kosar {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
    }
    .btn-kosar:hover { background-color: #218838; }
    .price { font-weight: bold; color: #28a745; font-size: 1.2rem; }
  </style>
</head>
<body>
    <nav class="navbar">
        <div class="menu-toggle" id="menu-toggle">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <div class="menu-bezar" id="menu-bezar">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
            </svg>
        </div>
        <ul class="nav_links" id="nav_links">
            <li><a class="nav-link" href="index.html">Főoldal</a></li>
            <li><a class="nav-link" href="szolgáltatásaink.html">Szolgáltatásaink</a></li>
            <li><a class="nav-link" href="csapatunk.html">Rólunk</a></li>
            <li><a class="nav-link" href="rendeles.php">Rendelés</a></li>
           <li>
                <a class="kosar position-relative d-inline-flex align-items-center" href="kosar.php">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312"/>
                    </svg>
                    <?php if (isset($kosar_db) && $kosar_db > 0): ?>
                        <span class="badge rounded-pill bg-danger" style="position: absolute; top: -5px; right: -10px; font-size: 0.65rem; padding: 0.25em 0.5em;">
                            <?php echo $kosar_db; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a class="profil" href="login.php"><img src="img/profil.svg" alt="Profil"></a></li>
        </ul>
    </nav>

  <h1 class="text-center my-4">Étlapunk</h1>

  <div class="kartyak">
    <?php if (count($foods) > 0): ?>
        <?php foreach ($foods as $food): ?>
            <div class="kartya">
                <img src="<?php echo !empty($food['image_url']) ? $food['image_url'] : 'img/slide1.jpg'; ?>" alt="<?php echo htmlspecialchars($food['name']); ?>">
                <div class="kartya_szoveg">
                    <h3><?php echo htmlspecialchars($food['name']); ?></h3>
                    <p><?php echo htmlspecialchars($food['description']); ?></p> 
                    <p class="price"><?php echo number_format($food['price'], 0, ',', ' '); ?> Ft</p>
                    <button class="btn-kosar" onclick="addToCart(<?php echo $food['id']; ?>)">Kosárba</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Jelenleg nincs elérhető étel az étlapon.</p>
    <?php endif; ?>
  </div>

  <script>
        document.addEventListener("DOMContentLoaded", () => {
                const menuToggle = document.getElementById("menu-toggle")
                const navLinks = document.getElementById("nav_links")
                const menuBezar = document.getElementById("menu-bezar");
                menuToggle.addEventListener("click", () => {
                    navLinks.classList.toggle("show");
                    menuToggle.classList.toggle("hide");
                    menuBezar.classList.toggle("show");
                })
                menuBezar.addEventListener("click", () => {
                    navLinks.classList.remove("show");
                    menuBezar.classList.remove("show");
                    menuToggle.classList.remove("hide");
                })
            }
        )
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function addToCart(foodId) {

        fetch('add_to_cart.php?id=' + foodId)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Frissítjük az oldalt, hogy a PHP újra lefusson 
                location.reload();
            }
        })
        .catch(error => console.error('Hiba:', error));
    }
</script>

</body>
</html>