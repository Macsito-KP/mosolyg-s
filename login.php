<?php
// SQL szerver company adatbázis users tábla
// ID , name , nickname , pwd_sec , last_login , office
header('Content-Type: text/html; charset=utf-8');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "company";

$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error){
    die("Connection error!");
}else{
    //echo "Sikeres sql kapcsolat!";
}





function check_input($str){
    $filtered = trim($str ?? '');
    $filtered = stripslashes($filtered);
    $filtered = htmlspecialchars($filtered, ENT_QUOTES, 'UTF-8');
    return $filtered;
}

$email_username = "";
$password = "";

// érkezet a formból POST üzenet?
if($_SERVER['REQUEST_METHOD'] == "POST"){
    if(isset($_POST["login_button"])){
       // input mezők tisztítása
       if(isset($_POST["email_username"])){
           $email_username = check_input($_POST["email_username"]);
       }else{
           echo "Adj meg email címet vagy felhasználó nevet<br>";
       }

       if(isset($_POST["password"])){
           $password = check_input($_POST["password"]);
       }else{
           echo "Nem töltötted ki a jelszó mezőt!<br>";
       }
 echo $password."<br>";
       // jelszó előkészítése
       $salt = "sdtgdrgdthgdr57656756ztrzgf";
       $password = ($salt.md5($password));
 echo $password."<br>";


    }


    //felhasznalo ellenorzese 
    $sql = "SELECT* FROM users";
    $result = $conn->query($sql);
    sql_query($result, $email_username);

    function sql_query($result, $name) {
        while($row = $result->fetch_assoc()) {
            if ($row["nickname"] == $name || $row["email"] == $name) {
        }else{
            echo "nincs ilyen felhasznalo";
        }
    }
} 


    $sql = "SELECT* FROM users";
    $result = $conn->query($sql);
    sql_query($result, $email_username);

    function sql_query($result, $name) {
        while($row = $result->fetch_assoc()) {
            if ($row["nickname"] == $name || $row["email"] == $name) {
        }else{
            echo "nincs ilyen felhasznalo";
        }
    }
} 
}
?>

<!doctype html>
<html lang="hu">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Bejelentkezés</title>
  <meta name="description" content="Bejelentkező oldal" />

  <style>
  :root{
   --bg:#EDDCC6;     /* sötét kék-fekete */
   --card:#0b1320;
   --accent:#6ee7b7;   /* halvány zöld */
   --muted:#94a3b8;
   --glass: rgba(255,255,255,0.04);
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
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:32px;
    }

    .wrap{
      width:100%;
      max-width: calc(var(--maxw) + 32px);
      padding:20px;
    }

    .card{
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border-radius:var(--radius);
      box-shadow: 0 8px 30px rgba(2,6,23,0.6);
      border: 1px solid rgba(255,255,255,0.04);
      max-width:var(--maxw);
      margin: 0 auto;
      overflow:hidden;
    }

    .card-header{
      padding:28px 28px 18px 28px;
      display:flex;
      gap:16px;
      align-items:center;
      background: linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.005));
    }
    .logo {
      width: 56px;
      height: 56px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;

    }

    .logo img {
      width: 140%;
      height: 140%;
      object-fit: contain; /* a kép arányosan illeszkedik */
      display: block;      /* eltávolítja az alap inline spacing-et */
      border-radius: 12px; /* illeszkedik a div lekerekítéséhez */
    }
    .title{
      line-height:1;
    }
    .title h1{
      margin:0;
      font-size:18px;
      letter-spacing:0.2px;
    }
    .title p{
      margin:2px 0 0;
      color:var(--muted);
      font-size:13px;
    }

    .card-body{
      padding:22px 28px 28px 28px;
    }

    form .field{
      margin-bottom:14px;
    }
    label{
      display:block;
      font-size:13px;
      color:var(--muted);
      margin-bottom:8px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"]{
      width:100%;
      display:block;
      padding:12px 12px;
      border-radius:10px;
      border:1px solid rgba(255,255,255,0.04);
      background:linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.005));
      color:#000000;
      font-size:15px;
      outline:none;
      transition:box-shadow .12s, border-color .12s, transform .06s;
      box-shadow: 0 2px 10px rgba(2,6,23,0.35) inset;
    }
    input:focus{
      border-color: rgba(110,231,183,0.9);
      box-shadow: 0 6px 24px rgba(110,231,183,0.06);
      transform:translateY(-1px);
    }

    .row{
      display:flex;
      gap:12px;
      align-items:center;
      justify-content:space-between;
    }

    .checkbox{
      display:flex;
      gap:8px;
      align-items:center;
      font-size:13px;
      color:var(--muted);
    }
    .checkbox input{width:16px;height:16px}

    .forgot{
      font-size:13px;
    }
    .forgot a{
      color:var(--muted);
      text-decoration:none;
    }
    .forgot a:hover{ text-decoration:underline; color:#BF4646 }

    .actions{
      margin-top:18px;
    }
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
      box-shadow: 0 8px 20px rgba(2,6,23,0.45);
    }
    button[type="submit"]:active{ transform:translateY(1px) }
    .secondary{
      background:transparent;
      border:1px solid rgba(255,255,255,0.04);
      color:var(--muted);
      padding:10px 12px;
      border-radius:10px;
      width:100%;
      margin-top:10px;
    }

    .or{
      text-align:center;
      color:var(--muted);
      font-size:13px;
      margin:16px 0;
    }

    .small{
      font-size:13px;
      color:var(--muted);
      text-align:center;
      padding:12px 18px 22px;
      border-top:1px solid rgba(255,255,255,0.02);
      background: linear-gradient(180deg, rgba(255,255,255,0.005), transparent);
    }
    .small a{ color:#BF4646; text-decoration:none; font-weight:600 }

    /* show password toggle */
    .pw-row{
      position:relative;
    }
    .toggle-pw{
      position:absolute;
      right:8px;
      top:65%;              /* lejjebb került */
      transform:translateY(-50%);
      background:transparent;
      border:none;
      color:var(--muted);
      font-size:13px;
      padding:6px 8px;
      cursor:pointer;
    }

    /* responsive */
    @media (max-width:480px){
      .card{ margin:12px; }
      .card-header{ padding:20px }
      .card-body{ padding:18px }
    }
  </style>
</head>
<body>
  <!-- PHP PROCESSING HERE -->
  <div class="wrap" role="main">
    <section class="card" aria-labelledby="login-heading">
      <header class="card-header">
        <div class="logo" aria-hidden="true">
          <img src="picsvg_download.svg" alt="Céges logó" />
        </div>
        <div class="title">
          <h1 id="login-heading">Üdvözlünk — Jelentkezz be</h1>
          <p>Fiókba való belépéshez add meg az adataidat.</p>
        </div>
      </header>

      <div class="card-body">
        <form action="<?php echo $_SERVER['PHP_SELF']?>" enctype="multipart/form-data" method="POST">
          <div class="field">
            <label for="email">E-mail vagy felhasználónév</label>
            <input id="email" name="email_username" type="text" inputmode="email" autocomplete="username" placeholder="pl. neve@pelda.hu" required aria-required="true" />
          </div>

          <div class="field pw-row">
            <label for="password">Jelszó</label>
            <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Jelszavad" required aria-required="true" minlength="6" />
            <button type="button" class="toggle-pw" aria-pressed="false" id="togglePw">Mutasd</button>
          </div>

          <div class="row">
            <label class="checkbox">
              <input type="checkbox" id="remember" name="remember" value="1" />
              <span>Maradjak belépve</span>
            </label>

            <div class="forgot">
              <a href="#" onclick="alert('Ez a funkció még nem működik.'); return false;">Elfelejtetted a jelszót?</a>
            </div>
          </div>

          <div class="actions">
            <button type="submit" name="login_button">Bejelentkezés</button>
            <button type="button" class="secondary" onclick="document.getElementById('loginForm').reset()">Űrlap törlése</button>
          </div>


        </form>
      </div>

      <div class="small">
        Nincs még fiókod? <a href="register2.php">Regisztrálj</a>
      </div>
    </section>
  </div>

  <script>
    // Show/hide password
    (function(){
      const pw = document.getElementById('password');
      const btn = document.getElementById('togglePw');
      btn.addEventListener('click', function(){
        const showing = pw.type === 'text';
        pw.type = showing ? 'password' : 'text';
        //btn.textContent = showing ? 'Mutasd' : 'Elrejt';
        btn.setAttribute('aria-pressed', String(!showing));
      });

      // Basic client-side validation UX
      const form = document.getElementById('loginForm');
      form.addEventListener('submit', function(e){
        // Let the server-side PHP handle actual auth and secure validation.
        // Here we only prevent accidental blank submissions in browsers without built-in support.
        if (!form.checkValidity()) {
          e.preventDefault();
          const firstInvalid = form.querySelector(':invalid');
          firstInvalid.focus();
        }
      });
    })();
  </script>
</body>
</html>