<?php
session_start();

$host = 'localhost';
$db = 'mosolygos';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die($e->getMessage());
}

if (isset($_POST['add_to_cart'])) {
    $food_id = $_POST['food_id'];
    $_SESSION['cart'][$food_id] = ($_SESSION['cart'][$food_id] ?? 0) + 1;
    header("Location: rendeles.php");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]--;
        if ($_SESSION['cart'][$id] <= 0) unset($_SESSION['cart'][$id]);
    }
    header("Location: rendeles.php");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    unset($_SESSION['cart'][$_GET['id']]);
    header("Location: rendeles.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM foods");
$foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendelés - Mosolygós Étlap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #EDDCC6 !important; 
            color: #000000 !important; 
        }

        /* NAVBAR */
        .navbar-custom { 
            background-color: rgba(0, 0, 0, 0.25) !important; 
            min-height: 70px; 
            position: relative; 
            transition: all 0.3s ease;
        }
        .navbar-custom .nav-link { 
            color: #000000 !important; 
            font-weight: 600; 
            transition: color 0.2s;
        }
        .navbar-custom .nav-link:hover { 
            color: #104200 !important; 
        }
        .navbar-custom .nav-link.active { 
            color: #104200 !important; 
        }

        .login-icon-container { 
            position: absolute; 
            right: 20px; 
            top: 50%; 
            transform: translateY(-50%); 
        }
        .login-icon-container a { 
            color: #000000 !important; 
            text-decoration: none; 
            font-weight: bold; 
        }
        .profil-img { 
            width: 30px; 
            transition: transform 0.2s;
        }
        .profil-img:hover { 
            transform: scale(1.1); 
        }

        /* KÁRTYA ANIMÁCIÓK */
        .food-card, .cart-card { 
            background-color: rgba(255, 255, 255, 0.4) !important; 
            color: #000000 !important; 
            border: 1px solid rgba(0,0,0,0.05) !important; 
            border-radius: 15px; 
            margin-bottom: 20px; 
            transition: all 0.3s ease-in-out; /* Simítás */
        }

        .food-card:hover { 
            transform: translateY(-10px); /* Felemelkedés */
            background-color: rgba(255, 255, 255, 0.6) !important; /* Kicsit kivilágosodik */
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; /* Árnyék */
        }

        .price-tag { 
            color: #104200 !important; 
            font-weight: 800; 
            font-size: 1.25rem; 
        }

        .text-muted { 
            color: #444444 !important; 
        }

        /* GOMBOK */
        .btn-qty-group { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }

        .btn-qty { 
            width: 32px; 
            height: 32px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border: 1px solid #104200; 
            color: #104200; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            background: transparent;
            transition: all 0.2s;
        }

        .btn-qty:hover { 
            background: #104200; 
            color: #ffffff; 
            transform: scale(1.1);
        }

        .btn-add-to-cart { 
            background-color: #104200 !important; 
            color: #ffffff !important; 
            font-weight: bold; 
            border: none; 
            padding: 8px 18px; 
            border-radius: 6px; 
            transition: all 0.2s;
        }

        .btn-add-to-cart:hover { 
            background-color: #1a6300 !important; 
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(16, 66, 0, 0.3);
        }

        .cart-item { 
            border-bottom: 1px solid rgba(0,0,0,0.1); 
            padding-bottom: 10px; 
            margin-bottom: 10px; 
        }

        .sticky-top { 
            top: 20px; 
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom mb-4 shadow-sm">
    <div class="container position-relative">
        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.html">Főoldal</a></li>
                <li class="nav-item"><a class="nav-link" href="szolgáltatásaink.html">Szolgáltatásaink</a></li>
                <li class="nav-item"><a class="nav-link" href="csapatunk.html">Rólunk</a></li>
                <li class="nav-item"><a class="nav-link active" href="rendeles.php">Rendelés</a></li>
            </ul>
        </div>
        <div class="login-icon-container">
            <?php if(isset($_SESSION['user_name'])): ?>
                <a href="logout.php"><?php echo $_SESSION['user_name']; ?></a>
            <?php else: ?>
                <a href="login.php"><img src="img/profil.svg" class="profil-img"></a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h2 class="mb-4 fw-bold">Étlap</h2>
            <div class="row">
                <?php foreach ($foods as $food): ?>
                    <div class="col-md-6">
                        <div class="card food-card shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold"><?php echo $food['name']; ?></h5>
                                <p class="small text-muted"><?php echo $food['description']; ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="price-tag"><?php echo number_format($food['price'], 0, ',', ' '); ?> Ft</span>
                                    <form method="POST">
                                        <input type="hidden" name="food_id" value="<?php echo $food['id']; ?>">
                                        <button type="submit" name="add_to_cart" class="btn btn-add-to-cart">Kosárba</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="sticky-top">
                <div class="card cart-card shadow">
                    <div class="card-body">
                        <h4 class="fw-bold mb-4 text-center">Kosarad</h4>
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <?php $total = 0; foreach ($_SESSION['cart'] as $id => $qty): 
                                $stmt = $pdo->prepare("SELECT name, price FROM foods WHERE id = ?");
                                $stmt->execute([$id]);
                                $item = $stmt->fetch();
                                $subtotal = $item['price'] * $qty;
                                $total += $subtotal;
                            ?>
                                <div class="cart-item">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold"><?php echo $item['name']; ?></span>
                                        <a href="rendeles.php?action=delete&id=<?php echo $id; ?>" class="text-danger" style="text-decoration:none;">×</a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="btn-qty-group">
                                            <a href="rendeles.php?action=remove&id=<?php echo $id; ?>" class="btn-qty">-</a>
                                            <span><?php echo $qty; ?> db</span>
                                            <form method="POST" style="margin:0;">
                                                <input type="hidden" name="food_id" value="<?php echo $id; ?>">
                                                <button type="submit" name="add_to_cart" class="btn-qty">+</button>
                                            </form>
                                        </div>
                                        <span class="fw-bold"><?php echo number_format($subtotal, 0, ',', ' '); ?> Ft</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="d-flex justify-content-between fw-bold mt-4">
                                <span>Összesen:</span>
                                <span class="price-tag"><?php echo number_format($total, 0, ',', ' '); ?> Ft</span>
                            </div>
                            <button class="btn btn-add-to-cart w-100 mt-3 py-2">RENDELÉS</button>
                        <?php else: ?>
                            <p class="text-center">A kosarad üres.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>