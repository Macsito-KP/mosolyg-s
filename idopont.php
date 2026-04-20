<?php
session_start();
if (empty($_SESSION['cart'])) {
    header("Location: rendeles.php");
    exit;
}
$is_logged_in = isset($_SESSION['user_id']);
$logged_name = $is_logged_in ? $_SESSION['user_name'] : '';
$logged_email = $is_logged_in ? $_SESSION['user_email'] : '';
$logged_phone = $is_logged_in ? $_SESSION['user_phone'] : '';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Szállítási adatok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body class="bg-light">
<div class="container my-5 shadow p-4 bg-white rounded" style="max-width: 500px;">
    <h2 class="text-center mb-4">Szállítási adatok</h2>
    <div class="alert alert-warning mb-4" style="font-size: 0.9rem;">
        <strong>Szállítási információk:</strong><br>
        • 📍 Kizárólag Budapest területén szállítunk!<br>
        • Rendelés leadása: Hétfőtől - Péntekig (08:00 - 15:00)
    </div>
    <form action="rendeles_mentes.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Teljes név</label>
            <input type="text" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($logged_name); ?>" <?php echo $is_logged_in ? 'readonly' : 'required'; ?>>
        </div>
        <div class="mb-3">
            <label class="form-label">E-mail cím</label>
            <input type="email" name="customer_email" class="form-control" value="<?php echo htmlspecialchars($logged_email); ?>" <?php echo $is_logged_in ? 'readonly' : 'required'; ?>>
        </div>
        <div class="mb-3">
            <label class="form-label">Telefonszám</label>
            <input type="text" name="customer_phone" class="form-control" value="<?php echo htmlspecialchars($logged_phone); ?>" <?php echo $is_logged_in ? 'readonly' : 'required'; ?>>
        </div>
        <div class="mb-3">
            <label class="form-label">Szállítási cím (Budapest)</label>
            <input type="text" name="customer_address" class="form-control" placeholder="Város, utca, házszám" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Mikorra kéred?</label>
            <input type="text" name="delivery_date" id="naptar" class="form-control" readonly required>
        </div>
        <div class="d-grid"><button type="submit" class="btn btn-success btn-lg">Rendelés véglegesítése</button></div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/hu.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#naptar", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            time_24hr: true,
            locale: "hu",
            minuteIncrement: 15,
            disable: [function(date) { return (date.getDay() === 6 || date.getDay() === 0); }],
            minTime: "08:00",
            maxTime: "15:00"
        });
    });
</script>
</body>
</html>