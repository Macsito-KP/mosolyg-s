<?php
session_start();
ini_set('display_errors', 0);

$servername = "localhost";
$username = "root";
$password_db = "";
$dbname = "mosolygos";

$conn = new mysqli($servername, $username, $password_db, $dbname);
$conn->set_charset("utf8mb4");

if($conn->connect_error){ die(); }

function check_input($str){
    return htmlspecialchars(stripslashes(trim($str ?? '')), ENT_QUOTES, 'UTF-8');
}

$error = "";
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["login_button"])){
    $email_username = check_input($_POST["email_username"]);
    $password = $_POST["password"];
    $salt = "sdtgdrgdthgdr57656756ztrzgf";
    $hashed_password = $salt . md5($password);

    $stmt = $conn->prepare("SELECT id, name, email, phone FROM users WHERE (nickname = ? OR email = ?) AND pwd_sec = ? LIMIT 1");
    $stmt->bind_param("sss", $email_username, $email_username, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['user_phone'] = $row['phone'];
        header("Location: rendeles.php");
        exit();
    } else {
        $error = "Hibás adatok!";
    }
    $stmt->close();
}
?>
<!doctype html>
<html lang="hu">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Bejelentkezés</title>
  <style>
  :root{ --bg:#EDDCC6; --card:#0b1320; --muted:#94a3b8; --radius:14px; --maxw:420px; font-family: Inter, sans-serif; }
  *{box-sizing:border-box}
  body{ margin:0; background: var(--bg); color:#808080; display:flex; align-items:center; justify-content:center; padding:32px; min-height:100vh; }
  .wrap{ width:100%; max-width: calc(var(--maxw) + 32px); padding:20px; }
  .card{ background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border-radius:var(--radius); box-shadow: 0 8px 30px rgba(2,6,23,0.6); border: 1px solid rgba(255,255,255,0.04); max-width:var(--maxw); margin: 0 auto; overflow:hidden; }
  .card-header{ padding:28px; display:flex; gap:16px; align-items:center; background: rgba(255,255,255,0.01); }
  .logo img { width: 56px; height: 56px; border-radius: 12px; object-fit: contain; }
  .title h1{ margin:0; font-size:18px; color:#333; }
  .card-body{ padding:28px; }
  form .field{ margin-bottom:14px; }
  label{ display:block; font-size:13px; color:var(--muted); margin-bottom:8px; }
  input{ width:100%; display:block; padding:12px; border-radius:10px; border:1px solid rgba(255,255,255,0.04); background:rgba(255,255,255,0.01); color:#000; font-size:15px; outline:none; box-shadow: 0 2px 10px rgba(2,6,23,0.35) inset; }
  button[type="submit"]{ width:100%; padding:12px; border-radius:12px; font-weight:600; background: #BF4646; color:#FFF4EA; border:none; cursor:pointer; }
  .small{ font-size:13px; color:var(--muted); text-align:center; padding:12px; border-top:1px solid rgba(255,255,255,0.02); }
  .small a{ color:#BF4646; text-decoration:none; font-weight:600; }
  .error{ color:#BF4646; text-align:center; margin-bottom:10px; }
  </style>
</head>
<body>
  <div class="wrap">
    <section class="card">
      <header class="card-header">
        <div class="logo"><img src="picsvg_download.svg" alt="Logó"></div>
        <div class="title"><h1>Bejelentkezés</h1><p>Add meg az adataidat.</p></div>
      </header>
      <div class="card-body">
        <?php if($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form action="" method="POST">
          <div class="field"><label>E-mail vagy Felhasználónév</label><input name="email_username" type="text" required /></div>
          <div class="field"><label>Jelszó</label><input name="password" type="password" required /></div>
          <button type="submit" name="login_button">Bejelentkezés</button>
        </form>
      </div>
      <div class="small">Nincs fiókod? <a href="register2.php">Regisztráció</a></div>
    </section>
  </div>
</body>
</html>