<?php
// SQL szerver company adatbázis users tábla
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "company";

$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error){
    die("Connection error!");
}

function check_input($str){
    $filtered = trim($str ?? '');
    $filtered = stripslashes($filtered);
    $filtered = htmlspecialchars($filtered, ENT_QUOTES, 'UTF-8');
    return $filtered;
}

$username_input = "";
$email_input = "";
$password = "";
$password_confirm = "";

// POST feldolgozás
if($_SERVER['REQUEST_METHOD'] == "POST"){
    if(isset($_POST["register_button"])){
        $username_input = check_input($_POST["username"] ?? "");
        $email_input = check_input($_POST["email"] ?? "");
        $password = check_input($_POST["password"] ?? "");
        $password_confirm = check_input($_POST["password_confirm"] ?? "");

        if(empty($username_input) || empty($email_input) || empty($password) || empty($password_confirm)){
            echo "<p style='color:red;'>Tölts ki minden mezőt!</p>";
        } elseif($password !== $password_confirm){
            echo "<p style='color:red;'>A jelszavak nem egyeznek!</p>";
        } else {
            $salt = "sdtgdrgdthgdr57656756ztrzgf";
            $password_hashed = $salt.md5($password);

            $stmt = $conn->prepare("INSERT INTO users (nickname, email, pwd_sec) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username_input, $email_input, $password_hashed);
            if($stmt->execute()){
                echo "<p style='color:green;'>Sikeres regisztráció!</p>";
            } else {
                echo "<p style='color:red;'>Hiba az adatbázisba írásnál: ".$conn->error."</p>";
            }
            $stmt->close();
        }
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
:root{
   --bg:#EDDCC6;
   --card:#0b1320;
   --accent:#6ee7b7;
   --muted:#94a3b8;
   --radius:14px;
   --maxw:420px;
   font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

*{box-sizing:border-box}
html,body{height:100%}
body{
   margin:0;
   background:
    radial-gradient(800px 400px at 10% 10%, rgba(110,231,183,0.06), transparent 10%),
    radial-gradient(600px 300px at 90% 90%, rgba(99,102,241,0.04), transparent 10%),
    var(--bg);
   color:#808080;
   display:flex;
   align-items:center;
   justify-content:center;
   padding:32px;
}

.wrap{width:100%; max-width: calc(var(--maxw)+32px); padding:20px;}
.card{
   background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
   border-radius:var(--radius);
   box-shadow:0 8px 30px rgba(2,6,23,0.6);
   border:1px solid rgba(255,255,255,0.04);
   max-width:var(--maxw);
   margin:0 auto;
   overflow:hidden;
}
.card-header{
   padding:28px 28px 18px 28px;
   display:flex;
   gap:16px;
   align-items:center;
   background: linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.005));
}
.logo{
   width:56px;
   height:56px;
   border-radius:12px;
   display:flex;
   align-items:center;
   justify-content:center;
}
.logo img{
   width:140%;
   height:140%;
   object-fit:contain;
   display:block;
   border-radius:12px;
}
.title{line-height:1;}
.title h1{margin:0;font-size:18px;letter-spacing:0.2px;}
.title p{margin:2px 0 0;color:var(--muted);font-size:13px;}

.card-body{padding:22px 28px 28px 28px;}
form .field{margin-bottom:14px;}
label{display:block;font-size:13px;color:var(--muted);margin-bottom:8px;}
input[type="text"], input[type="email"], input[type="password"]{
   width:100%;
   display:block;
   padding:12px;
   border-radius:10px;
   border:1px solid rgba(255,255,255,0.04);
   background:linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.005));
   color:#000000;
   font-size:15px;
   outline:none;
   box-shadow:0 2px 10px rgba(2,6,23,0.35) inset;
}
input:focus{border-color: rgba(110,231,183,0.9); box-shadow:0 6px 24px rgba(110,231,183,0.06);}

.pw-row{position:relative;}
.toggle-pw{position:absolute;right:8px;top:65%;transform:translateY(-50%);background:transparent;border:none;color:var(--muted);font-size:13px;padding:6px 8px;cursor:pointer;}

button[type="submit"]{
   width:100%;
   padding:12px 14px;
   border-radius:12px;
   font-weight:600;
   font-size:15px;
   background: #BF4646;
   color:#FFF4EA;
   border:none;
   cursor:pointer;
   box-shadow:0 8px 20px rgba(2,6,23,0.45);
}
button[type="submit"]:active{transform:translateY(1px);}
.secondary{background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted);padding:10px 12px;border-radius:10px;width:100%;margin-top:10px;}

.small{font-size:13px;color:var(--muted);text-align:center;padding:12px 18px 22px;border-top:1px solid rgba(255,255,255,0.02);background: linear-gradient(180deg, rgba(255,255,255,0.005), transparent);}
.small a{ color:#BF4646; text-decoration:none; font-weight:600; }

@media (max-width:480px){
  .card{margin:12px;}
  .card-header{padding:20px;}
  .card-body{padding:18px;}
}
</style>
</head>
<body>
<div class="wrap" role="main">
<section class="card" aria-labelledby="register-heading">
<header class="card-header">
<div class="logo"><img src="picsvg_download.svg" alt="Céges logó"></div>
<div class="title">
<h1 id="register-heading">Üdvözlünk — Regisztráció</h1>
<p>Hozd létre a fiókod a belépéshez.</p>
</div>
</header>
<div class="card-body">
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
<div class="field">
<label for="username">Felhasználónév</label>
<input id="username" name="username" type="text" placeholder="pl. KissPéter" required />
</div>
<div class="field">
<label for="email">Gmail cím</label>
<input id="email" name="email" type="email" placeholder="pl. valaki@gmail.com" required />
</div>
<div class="field pw-row">
<label for="password">Jelszó</label>
<input id="password" name="password" type="password" placeholder="Jelszavad" required minlength="6" />
<button type="button" class="toggle-pw" aria-pressed="false" id="togglePw">Mutasd</button>
</div>
<div class="field pw-row">
<label for="password_confirm">Jelszó újra</label>
<input id="password_confirm" name="password_confirm" type="password" placeholder="Jelszavad újra" required minlength="6" />
<button type="button" class="toggle-pw" aria-pressed="false" id="togglePw2">Mutasd</button>
</div>
<div class="actions">
<button type="submit" name="register_button">Regisztráció</button>
</div>
</form>
</div>
<div class="small">
Már van fiókod? <a href="login.php">Bejelentkezés</a>
</div>
</section>
</div>

<script>
// Mutasd / elrejt jelszó
(function(){
  const pw1 = document.getElementById('password');
  const btn1 = document.getElementById('togglePw');
  btn1.addEventListener('click', function(){
    const showing = pw1.type === 'text';
    pw1.type = showing ? 'password' : 'text';
    btn1.setAttribute('aria-pressed', String(!showing));
  });

  const pw2 = document.getElementById('password_confirm');
  const btn2 = document.getElementById('togglePw2');
  btn2.addEventListener('click', function(){
    const showing = pw2.type === 'text';
    pw2.type = showing ? 'password' : 'text';
    btn2.setAttribute('aria-pressed', String(!showing));
  });
})();
</script>
</body>
</html>