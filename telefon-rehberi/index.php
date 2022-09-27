

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Telefon Rehberi</title>
</head>
<body>


    <form action="" method="post">
        <div class="giris-div">
            <input class="isim giris-input" name="ad" type="text" placeholder="Ad">
            <input class="isim giris-input" name="soyad" type="text" placeholder="Soyad">
            <input class="giris-input" name="numara" type="number" placeholder="Telefon Numarası">
            <p id="message"></p>
            <button class="giris-btn" type="submit">Kaydet</button>
        </div>
    </form>
    
</body>
</html>

<?php
    echo "<script>
        const regex = /^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøğıùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ðş ,.'-]+$/u;

        [...document.getElementsByClassName('isim')].forEach(
            (element) => {
            element.onkeydown = () => regex.test(event.key) }
        );

            
        </script>";
    include("yonetim/ayar.php");


    if ($_POST) {
        $ad = $_POST["ad"];
        $soyad = $_POST["soyad"];
        $numara = $_POST["numara"];

        $sorgu = $baglan->query("select * from rehber where (ad='$ad' && soyad='$soyad')");
        $kayitsay = $sorgu->num_rows;

        $sorgunumara = $baglan->query("select * from rehber where numara='$numara'");
        $numara_array_sorgu = $baglan->query("select * from rehber where JSON_CONTAINS(numara, '$numara')");
        $numarasay = $sorgunumara->num_rows + $numara_array_sorgu->num_rows; 

        if($numara == ''){
            echo "<script> 
                document.getElementById('message').innerHTML  = 'Lütfen bir numara girin'
                setTimeout(() => {
                    document.getElementById('message').innerHTML  = ''
                }, 2000)
             </script>";
        }elseif($numarasay > 0){
            echo "<script> 
                document.getElementById('message').innerHTML  = 'Bu numara zaten kayıtlı'
                setTimeout(() => {
                    document.getElementById('message').innerHTML  = ''
                }, 2000)
            </script>";
        }elseif ($kayitsay > 0) {
            $sorgu = $baglan->query("Update rehber set numara=JSON_ARRAY_APPEND(numara, '$', $numara) where (ad='$ad' && soyad='$soyad')");
            
        } else {
            $sorgu = $baglan->query("insert into rehber (ad, soyad, numara) values ('$ad','$soyad', '$numara')");
        }

    }
    

?> 