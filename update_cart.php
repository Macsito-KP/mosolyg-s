<?php
session_start();

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if (isset($_SESSION['cart'][$id])) {
        if ($action === 'plus') {
            $_SESSION['cart'][$id]++;
        } elseif ($action === 'minus') {
            $_SESSION['cart'][$id]--;
            
            if ($_SESSION['cart'][$id] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
    }
}

header("Location: kosar.php");
exit;
?>