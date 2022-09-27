<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Telefon Rehberi Giriş</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body style="text-align:center;padding-top:50px;">

        <form action="" method="post">
            <div class="giris-div">
                <input class="giris-input" name="kullanici" type="text" placeholder="Kullanıcı Adı">
                <input class="giris-input" name="sifre" type="password" placeholder="Parola">
                <p id="message"></p>
                <button class="giris-btn" type="submit">Giriş</button>
            </div>
        </form>
</body>
</html>

<?php
    session_start();
    include("ayar.php");

    if ($_POST) {
        $kullanici = $_POST["kullanici"];
        $sifre = $_POST["sifre"];

        $sorgu = $baglan->query("select * from kullanici where (kullanici='$kullanici' && sifre='$sifre')");
        $kayitsay = $sorgu->num_rows;
      
        if ($kayitsay > 0) {
            setcookie("kullanici","dilara",time()+60*60);
            $_SESSION["giris"] = sha1(md5("var"));
            
            echo "<script> window.location.href='anasayfa.php'; </script>";
        } else {
            echo "<script>
                document.getElementById('message').innerHTML  = 'Hatalı kullanıcı bilgisi!'
                setTimeout(() => {
                    document.getElementById('message').innerHTML  = ''
                }, 2000)
            </script>";
        }
    }
?>