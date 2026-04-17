<?php
session_start();
if (empty($_SESSION['cart'])) {
    header("Location: rendeles.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Időpont választás</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<div class="container my-5 shadow p-4 bg-white rounded" style="max-width: 500px;">
    <h2 class="text-center mb-4">Szállítási adatok</h2>
    
    <div class="alert alert-warning mb-4" style="font-size: 0.9rem;">
        <strong>Szállítási információk:</strong><br>
        • <strong>Kizárólag Budapest területén szállítunk!</strong><br>
        • Rendelés leadása: Hétfőtől - Péntekig<br>
        • Idősáv: 08:00 - 15:00 között<br>
        • Hétvégén a szállítás szünetel!
    </div>
    
    <form action="rendeles_mentes.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Teljes név</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">E-mail cím</label>
            <input type="email" name="customer_email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Telefonszám</label>
            <input type="text" name="customer_phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Szállítási cím (város, utca, házszám)</label>
            <input type="text" name="customer_address" class="form-control" placeholder="Pl: 1051 Budapest, Kossuth tér 1." required>
        </div>

        <div class="mb-4">
            <label class="form-label">Mikorra kéred a rendelést?</label>
            <input type="text" name="delivery_date" id="naptar" class="form-control" placeholder="Válassz egy hétköznapot 8 és 15 óra között..." readonly required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-success btn-lg">Rendelés véglegesítése</button>
        </div>
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
            
            // 1. Hétvégék letiltása (6 = szombat, 0 = vasárnap)
            "disable": [
                function(date) {
                    return (date.getDay() === 6 || date.getDay() === 0);
                }
            ],
            
            // 2. Órák korlátozása (8-tól 15-ig)
            minTime: "08:00",
            maxTime: "15:00",
            
            // Biztonsági ellenőrzés: ha hétvégére ugrana a naptár alapból, ne engedje
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const selDate = selectedDates[0];
                    if (selDate.getDay() === 0 || selDate.getDay() === 6) {
                        instance.clear();
                        alert("Hétvégén nem vállalunk kiszállítást!");
                    }
                }
            }
        });
    });
</script>
</body>
</html>