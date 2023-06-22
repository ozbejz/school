Prenesite in namestite PB GeodetskaUprava, ki ste jo ustvarili z izvedbo 23. vaje. Baza ima nekaj testnih podatkov (če je prazna, ponovno izvedite vnose iz prejšnje vaje). V bazi sta tabeli
•	Stavba(StavbaID:N, Naslov:A20, Kraj:A20, SteviloPrebivalcev:N) in
•	Stanovanje(StavbaID:N-->Stavba, Zap_ST:N, Povrsina_kvadrati:N, Prijavljenih:N, VrednostStanovanja:N).
Pri realizaciji nalog uporabite
•	API mysqli in prepared statements, regularne izraze in seje

Naloga 1
V PB ustvarite še tabelo 
Uporabnik (imeUporabnika:A20, geslo:A100, datumRegistracije:D, ime:A10, priimek:A20, eMail:A20, datumZadnjegDostopa:D, stNeuspesnihPrijav:N)
Napišite program PHP, ki omogoča registracijo uporabnika. Pri registraciji mora uporabnik upoštevati naslednje omejitve:
•	dolžina gesla je vsaj 8 znakov, med znaki mora biti vsaj 1 mala črka, vsaj 1 velika črka in vsaj 1 števka;
•	v imenu in priimku se lahko pojavijo le črke, ime in priimek morata imeti veliko začetnico.
Veljavnost podatkov preverite v brskalniku in v PHP skripti.
Geslo shranite v razpršeni obliki (lahko uporabite katerikoli razpršilni algoritem, priporoča se bcrypt, ki je obenem tudi default algoritem  za razpršitev).
Datum registracije in datum zadnjeg dostopa nastavite na sistemski datum. Podatek stNeuspesnihPrijav naj bo 0. Podatek eMail je enoličen (2 uporabnika ne moreta imeti enak mail naslov).
Po uspešni registraciji izvedite preusmeritev uporabnika na prijavno stran.

<form action="process-registracija.php" method="post">
    <input type="text" name="upIme" placeholder="uporabniško ime" required/> <br/>
    <input type="text" name="ime" placeholder="ime" pattern="[A-ZČŠŽ][a-zčšž]+" title="velika začetnica, samo črke" required/> <br/>
    <input type="text" name="priimek" placeholder="priimek" pattern="[A-ZČŠŽ][a-zčšž]+" title="velika začetnica, samo črke" required/> <br/>
    <input type="text" name="email" placeholder="email" required/> <br/>
    <input type="password" name="geslo" placeholder="geslo" minlength="8" required/> <br/>
    <input type="submit" value="registracija" />
</form>

<?php

if(isset($_POST["ime"]) && isset($_POST["geslo"])){
    $pattern = "/[A-ZČŠŽ][a-zčšž]+/";
    if(!preg_match($pattern, $_POST["ime"]) || !preg_match($pattern, $_POST["priimek"])){
        die("napaka");
    }
    $mysqli = new mysqli('localhost', 'root', '', 'geodetskauprava');
    if($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    $q = "INSERT INTO uporabnik(imeUporabnika, geslo, datumRegistracije, ime, priimek, eMail, datumZadnjegaDostopa) VALUES(?,?,?,?,?,?,?)";
    $stmt = $mysqli->prepare($q);

    $password_hash = password_hash($_POST["geslo"], PASSWORD_DEFAULT);
    $date = date("Y/m/d");
    $stmt->bind_param("sssssss", $_POST["upIme"], $password_hash, $date, $_POST["ime"], $_POST["priimek"], $_POST["email"], $date);

    if($stmt->execute()){
        header("location: prijava.php");
    }
    $stmt->close();
}

?>

Naloga 2
Napišite program PHP, ki omogoča prijavo. Po uspešno izvedeni prijavi, uporabnika preusmerite na stran meni.php, ki ponuja 3 povezave:
•	Iskanje stanovanja, s tem da določimo minimalno kvadraturo oz. ceno (naloga 2-1 prejšnje vaje),
•	Iskanje stanovanj po kraju (funkcionalnost je potrebno realizirati; izpis stanovanj naj bo deljen na več strani – po 5 zapisov na stran) in
•	Odjavo.
Dostop do strain meni.php in strain za iskanje zagotovite le registriranim uporabnikom, vse druge poizkuse preusmerite na prijavno stran.
Če uporabnik pri prijavi vnese napačne podatke in uporabniško ime obstaja, povečajte števec neuspešnih prijav za 1. Če (ko) števec doseže vrednost 6, naj uporabnik dobi obvestilo
 ‘Vaš račun je blokiran, obrnite se na skrbnika sistema’. Od takrat naprej uporabnik (kljub morebitni pravilni prijavi) nima več dostopa do aplikacije. Če pa se uporabnik pravočasno ‘spomni’ pravega gesla, 
 njegov števec neuspešnih poskusov ponastavite na 0.

<?php
session_start();
if(!isset($_SESSION["user_id"])){
    header('location: http://localhost/prijava.php');
}
?>

<a href="iskanje.php">iskanje</a>

<?php

function ban($mysqli){
    $q = "SELECT stNeuspesnihPrijav FROM uporabnik WHERE imeUporabnika = ?";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("s", $_POST["ime"]);
    $stmt->execute();
    $rs = $stmt->get_result();
    $n = $rs->fetch_assoc();
    $stmt->close();
    if($n["stNeuspesnihPrijav"] >= 6)
        return true;
    else
        return false;
}

if(isset($_POST["ime"]) && isset($_POST["geslo"])){
    if(!isset($_SESSION)) {
        session_start();
     }
    $mysqli = new mysqli('localhost', 'root', '', 'geodetskauprava');
    if($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    $q = "SELECT * FROM uporabnik WHERE imeUporabnika = ?";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("s", $_POST["ime"]);
    $stmt->execute();
    $rs = $stmt->get_result();
    $user = $rs->fetch_assoc();
    $stmt->close();

    if($user){ 
        if(ban($mysqli)){
            die("bannan");
        }
        if(password_verify($_POST["geslo"], $user["geslo"])){
            $_SESSION["user_id"] = $user["imeUporabnika"];
            header("location: meni.php");
            $q = "UPDATE uporabnik SET stNeuspesnihPrijav = 0 WHERE imeUporabnika = ?";
            $stmt = $mysqli->prepare($q);
            $stmt->bind_param("s", $_POST["ime"]);
            $stmt->execute();
            $stmt->close();
        }
        else{
            $q = "UPDATE uporabnik SET stNeuspesnihPrijav = stNeuspesnihPrijav+1 WHERE imeUporabnika = ?";
            $stmt = $mysqli->prepare($q);
            $stmt->bind_param("s", $_POST["ime"]);
            $stmt->execute();
            $stmt->close();
            if(ban($mysqli)){
                die("bannan");
            }
        }
    }
}
?>

Naloga 3
Napišite skripto za varno odjavo. Ko se uporabnik odjavi, ga preusmerite nazaj na prijavno stran.

<?php
session_start();

$_SESSION = array();
session_destroy();

header("location: prijava.php");
?>
