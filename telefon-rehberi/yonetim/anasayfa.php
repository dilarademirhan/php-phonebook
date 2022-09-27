<?php 
    error_reporting(0);
    session_start();
    include("ayar.php");
    
    if ($_SESSION["giris"] != sha1(md5("var")) || $_COOKIE["kullanici"] != "dilara") {
        header("Location: cikis.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style-yonetim.css">
    <title>Telefon Rehberi</title>
</head>
<body>
    <h1>Kişiler</h1>
    <table id="kisiler" width="100%" border="1">
        <tr align="center">
            <th></th>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Numara</th>
            <th></th>
        </tr>
        <?php
            $sirano = 0;
            $sorgu = $baglan->query("select * from rehber");
            while ($satir = $sorgu->fetch_object()) {
                $numaralar = $satir->numara;
                $deger = "<li id='$satir->numara' class='numara'>$satir->numara</li>";
                $ilk = mb_substr($numaralar, 0, 1);
                if($ilk == "["){
                    $numaralar = substr($numaralar, 1, -1);
                    $numaralar = trim($numaralar, " ");
                    $numaralar = explode(',', $numaralar);
                    $deger = "";
                    foreach ($numaralar as $n) {
                       $deger = $deger."<li id='$n' class='numara'>$n</li>";
                    }
                }

                $sirano++;
                $soyadid = ($satir->soyad).($satir->id).'soyad';
                $adid = ($satir->ad).($satir->id).'ad';
                $soyadd = "abcd";
                echo "<tr align='center'>
                    <td>$sirano</td>
                    <td><p class='isim' id=$adid>$satir->ad</p></td>
                    <td><p class='isim' id=$soyadid>$satir->soyad</p></td>
                    <td>
                    <ul>
                        $deger
                    </ul>   

                    </td>
                    <td><a class='sil-btn' href='anasayfa.php?islem=sil&id=$satir->id'>✖</td>
                </tr>";
               
            }
        ?>
    </table>

    <form id="ekle-form" action="anasayfa.php?islem=ekle" method="post">
        <div>
            <p id="yeni-ekle">Yeni Kişi-Numara Ekle</p>
            <input class="ekle-input" name="ad" type="text" placeholder="Ad">
            <br>    
            <input name="soyad" type="text" placeholder="Soyad">
            <br>    
            <input name="numara" type="number" placeholder="Telefon Numarası">
            <br>
            <p id="message"></p>
            <button class="btn" type="submit">Kaydet</button>
        </div>
    </form>

    <a class='cikis' href="cikis.php">Çıkmak için tıklayın</a>

    
</body>
</html>

<?php 
        
    $islem = $_GET["islem"]; 

    if ($islem == "sil") {
        $id = $_GET["id"];
        $sorgu = $baglan->query("delete from rehber where (id='$id')");
        echo "<script> window.location.href='anasayfa.php'; </script>";
    }

    if ($islem == 'ekle') {
        $ad = $_POST["ad"];
        $soyad = $_POST["soyad"];
        $numara = $_POST["numara"];
        
        $sorgu = $baglan->query("select * from rehber where (ad='$ad' && soyad='$soyad')");
        $numarasorgu = $baglan->query("select * from rehber where numara='$numara'");
        $numara_array_sorgu = $baglan->query("select * from rehber where JSON_CONTAINS(numara, '$numara')");
        $kayitsay = $sorgu->num_rows;
        $numarasay = $numarasorgu->num_rows +  $numara_array_sorgu->num_rows;

        if($numara == ''){
            echo "<script> 
                document.getElementById('message').innerHTML  = 'Lütfen bir numara girin'
                setTimeout(() => {
                    document.getElementById('message').innerHTML  = ''
                    window.location.href='anasayfa.php'; 
                }, 2000)
             </script>";
        }elseif ($numarasay > 0) {
            echo "<script> 
                document.getElementById('message').innerHTML  = 'Bu numara zaten kayıtlı!'
                setTimeout(() => {
                    document.getElementById('message').innerHTML  = ''
                    window.location.href='anasayfa.php'; 
                }, 2000)
            </script>";
        } elseif($kayitsay > 0){
            $sorgu = $baglan->query("Update rehber set numara=JSON_ARRAY_APPEND(numara, '$', $numara) where (ad='$ad' && soyad='$soyad')");
            echo "<script>
                document.getElementById('message').innerText = ''
                window.location.href='anasayfa.php'; 
             </script>";
        } else {
            $sorgu = $baglan->query("insert into rehber (ad, soyad, numara) values ('$ad','$soyad', '$numara')");
            echo "<script> 
                document.getElementById('message').innerText = ''
                window.location.href='anasayfa.php'; 
            </script>";
        }
    }
    
  
    function varmiKisi($baglan, $ad, $soyad){
        $sorgu = $baglan->query("select * from rehber where (ad='$ad' && soyad='$soyad')");
        $kayitsay = $sorgu->num_rows;
        if($kayitsay > 0) return true;
        return false;
    }

    function varmiNumara( $baglan, $numara){

        $numarasorgu = $baglan->query("select * from rehber where numara='$numara'");
        $numara_array_sorgu = $baglan->query("select * from rehber where JSON_CONTAINS(numara, '$numara')");
        $numarasay = $numarasorgu->num_rows +  $numara_array_sorgu->num_rows; 
        if($numarasay > 0) return true;
        return false;
    }
        
    if ($islem == "guncelle") {
        $id = $_GET["id"];
        $tur = $_GET["turu"];
        $deger = $_GET["deger"];
        $eski = $_GET["eski"];
        if($tur == 'numara'){
            if($deger == ''){
            echo "<script>
                alert('Lütfen bir numara girin!'); window.location.href='anasayfa.php';
            </script>";
            }elseif(varmiNumara($baglan, $deger)){
                echo "<script>
                    alert('Bu numara zaten kayıtlı!'); window.location.href='anasayfa.php';
                </script>";
            } else {
                $sorgu = "update rehber set numara=$deger where numara=$eski";
                $sorgu = $baglan->query($sorgu);
                echo "<script> window.location.href='anasayfa.php'; </script>";
            }
        } else if($tur == 'ad'){
            $soyadsorgu = "select soyad from rehber where id=$id";
            $soyadsorgu = $baglan->query($soyadsorgu);
            $soyadsorgu = $soyadsorgu->fetch_object();
            $soyad = $soyadsorgu->soyad;
            if(varmiKisi($baglan, $deger, $soyad)){
                echo "<script>
                    alert('Bu kişi zaten kayıtlı!'); window.location.href='anasayfa.php';
                </script>";
            }else {
                $sorgu = "update rehber set $tur='$deger' where id=$id";
                $sorgu = $baglan->query($sorgu);
                echo "<script> window.location.href='anasayfa.php'; </script>";
            }
        } else if($tur == 'soyad'){
            $adsorgu = "select ad from rehber where id=$id";
            $adsorgu = $baglan->query($adsorgu);
            $adsorgu = $adsorgu->fetch_object();
            $ad = $adsorgu->ad;
            if(varmiKisi($baglan, $ad, $deger)){
                echo "<script>
                    alert('Bu kişi zaten kayıtlı!'); window.location.href='anasayfa.php';
                </script>";
            }else {
                $sorgu = "update rehber set $tur='$deger' where id=$id";
                $sorgu = $baglan->query($sorgu);
                echo "<script> window.location.href='anasayfa.php'; </script>";
            }
        }

       
    
    }
     
                

    echo "<script>
        [...document.getElementsByClassName('isim')].forEach(
            (element) => {
            element.addEventListener('click', (e) => {
                degeriGuncelle(element, element.id)
            })}
        );

        [...document.getElementsByClassName('isim')].forEach(
            (element) => {
            if(!element.innerHTML){
                element.innerHTML = 'Değer atamak için tıklayınız.'
            }}
        );

        [...document.getElementsByClassName('numara')].forEach(
            (element) => {
            element.addEventListener('click', (e) => {
                numarayiGuncelle(element, element.innerText)
            })}
        );
        
        function rakamlariDondur(id){
            let sayi = ''
            for(let e of id){
                if(/^\d+$/.test(e)) sayi += e
            }
            return sayi
        }
      
        function numarayiGuncelle(element, numara){
            let d = document.createElement('input')
            d.type = 'number'
            d.innerText = numara
           
            let tur = 'numara'
            d.addEventListener('focusout', (e) => {
                d.id = numara
                numaraGuncelle(d, e.target.value)
                window.location.href='anasayfa.php?islem=guncelle&turu='+tur+'&deger='+e.target.value+'&eski='+numara;
            });

             element.parentNode.replaceChild(d, element)
             d.focus()
        }

        function numaraGuncelle(element, value){
            let d = document.createElement('p')
            d.className = 'isim'
            d.innerHTML = value
            if(value == '') {
                d.innerHTML = 'Değer atamak için tıklayınız.'
            }
            d.id = value
            d.onclick = (e) => {
                numarayiGuncelle(d)
            }
            element.parentNode.replaceChild(d, element)
            d.focus()
        }

        function degeriGuncelle(element, id){
            let d = document.createElement('input')
        
            const regex = /^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøğıùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ðş ,.'-]+$/u;
            d.onkeydown = () => regex.test(event.key)
            let tur = element.id.split(rakamlariDondur(element.id))[1]
            d.addEventListener('focusout', (e) => {
                d.id = e.target.value + rakamlariDondur(element.id) + tur
                metinGuncelle(d, e.target.value)
                window.location.href='anasayfa.php?islem=guncelle&eski=&turu='+tur+'&deger='+e.target.value+'&id='+rakamlariDondur(element.id);
            });
            element.parentNode.replaceChild(d, element)
            d.focus()
        }

        function metinGuncelle(element, value){
            let d = document.createElement('p')
            d.className = 'isim'
            d.innerHTML = value
            if(value == '') {
                d.innerHTML = 'Değer atamak için tıklayınız.'
            }
            d.id = element.id
            d.onclick = (e) => {
                degeriGuncelle(d)
            }
            element.parentNode.replaceChild(d, element)
        }
    
    </script>";

?>






