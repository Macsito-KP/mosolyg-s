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

$msg = "";
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["register_button"])){
    $fullname = check_input($_POST["fullname"]);
    $username_input = check_input($_POST["username"]);
    $email_input = check_input($_POST["email"]);
    $phone_input = check_input($_POST["phone"]);
    $password = $_POST["password"];
    $password_confirm = $_POST["password_confirm"];

    if(empty($fullname) || empty($username_input) || empty($email_input) || empty($phone_input) || empty($password)){
        $msg = "Minden mezőt tölts ki!";
    } elseif($password !== $password_confirm){
        $msg = "A jelszavak nem egyeznek!";
    } else {
        $salt = "sdtgdrgdthgdr57656756ztrzgf";
        $password_hashed = $salt . md5($password);

        $stmt = $conn->prepare("INSERT INTO users (name, nickname, email, phone, pwd_sec) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $username_input, $email_input, $phone_input, $password_hashed);
        
        if($stmt->execute()){
            header("Location: login.php?success=1");
            exit();
        } else {
            $msg = "Hiba: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="hu">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Regisztráció</title>
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
  .error{ color:#BF4646; text-align:center; margin-bottom:10px; font-size:14px; }
  </style>
</head>
<body>
  <div class="wrap">
    <section class="card">
      <header class="card-header">
        <div class="logo"><img src="picsvg_download.svg" alt="Logó"></div>
        <div class="title"><h1>Regisztráció</h1><p>Hozd létre a fiókod.</p></div>
      </header>
      <div class="card-body">
        <?php if($msg): ?><div class="error"><?php echo $msg; ?></div><?php endif; ?>
        <form action="" method="POST">
          <div class="field"><label>Teljes név</label><input name="fullname" type="text" required /></div>
          <div class="field"><label>Felhasználónév</label><input name="username" type="text" required /></div>
          <div class="field"><label>E-mail cím</label><input name="email" type="email" required /></div>
          <div class="field"><label>Telefonszám</label><input name="phone" type="text" required /></div>
          <div class="field"><label>Jelszó</label><input name="password" type="password" required minlength="6" /></div>
          <div class="field"><label>Jelszó újra</label><input name="password_confirm" type="password" required /></div>
          <button type="submit" name="register_button">Regisztráció</button>
        </form>
      </div>
      <div class="small">Már van fiókod? <a href="login.php">Bejelentkezés</a></div>
    </section>
  </div>
</body>
</html>