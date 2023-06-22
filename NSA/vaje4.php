PB Dijaki (datoteka dijaki1.sql) sestavljajo  tabele:
Uporabnik(imeUporabnika:A20,geslo:A64,steviloDostopov:N,datumZadnjegaDostopa:D) // gesla so razpršena z algoritmom sha1
Sola(SID:N,imeSole:A20,kraj:A20)
Dijak(DID:N, ime:A20, priimek:A30, SID:N-->Sola,Spol:A1)
PB je nameščena na strežniku localhost, za dostop uporabite uporabniško ime root, brez gesla.

Splošno navodilo: 
•	Izhodišče za reševanje naloge naj bo rešitev vaje 16.
•	Kodo nekaterih postopkov boste pisali večkrat. Premislite in (po potrebi) skupne postopke zapišite kot funkcije. 
Funkcije nato lahko shranite v ločeno datoteko in jo po potrebi vključite v posamezno skripto.
•	Datoteke s programsko kodo prekopirate v poročilo vaje in oddate v nabiralnik in shranite v eni arhivski datoteki (zip/rar/...) 
Naloga 1 // nalogo obvezno rešite brez prepared statements, kot opcijo lahko naredite tudi rešitev s prepared statements

<?php
$mysqli = new mysqli('localhost', 'root', '', 'dijaki');
if($mysqli->connect_error){
    die("Connection error: " . $mysqli->connect_error);
}
return $mysqli;
?>

a)	Napišite program (eno ali več skript), ki uporabnikom omogoča registracijo v aplikacijo. 
Po uspešno izvedeni registraciji, se uporabnika vrne na spletno stran za prijavo. 
Gesla morajo biti razpršena, po želji jih lahko še dodatno zavarujete s salti.

<form action="process-registracija.php" method="POST">
    <input type="text" name="ime" placeholder="ime" /> <br/>
    <input type="password" name="geslo" placeholder="geslo"/> <br/>
    <input type="submit" value="registracija"/>
</form>
<?php
$mysqli =  require __DIR__ . "/baza.php";
mysqli_report(MYSQLI_REPORT_STRICT);
$q = "INSERT INTO Uporabnik VALUES(?,?,?,?)";

$geslo_hash = sha1($_POST["geslo"]);
$datum = '0/0/0';
$st = 0;
$stmt = $mysqli->stmt_init();
$stmt->prepare($q);
$stmt->bind_param("ssis", $_POST["ime"], $geslo_hash, $st, $datum);
if($stmt->execute()){
    header("location: prijava.php");
}
else{
    die("registracija ni uspela");
}
?>

b)	Napišite program (eno ali več skript), ki uporabnikom omogoča prijavo v aplikacijo. 
V primeru veljavnega uporabniškega imena in gesla, se posodobita podatka o zadnjem dostopu in število dostopov in nato se prikaže osnovna spletna stran aplikacije z možnostjo izpisa podatkov 
in dodajanja novega dijaka, sicer se uporabnika vrne na prijavno stran. Spletna stran za prijavo naj ima še povezavo na spletno stran za registracijo novega uporabnika. 

<form action="process-prijava.php" method="POST">
    <input type="text" name="ime" placeholder="ime" /> <br/>
    <input type="password" name="geslo" placeholder="geslo"/> <br/>
    <input type="submit" value="prijava"/>
</form>
<a href="registracija.php">registracija</a>

<?php

$mysqli =  require __DIR__ . "/baza.php";
mysqli_report(MYSQLI_REPORT_STRICT);

$q = "SELECT * FROM Uporabnik WHERE imeUporabnika=?";
$stmt = $mysqli->stmt_init();
$stmt->prepare($q);
$stmt->bind_param("s", $_POST["ime"]);
$stmt->execute();
$res = $stmt->get_result();
$upo = $res->fetch_assoc();
$stmt->close();
if($upo){
    if(sha1($_POST["geslo"]) == $upo["geslo"]){
        $q = "UPDATE Uporabnik SET steviloDostopov = ?, datumZadnjegaDostopa=?";
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($q);
        $datum = date("Y/m/d");
        $st = $upo["steviloDostopov"]+1;
        $stmt->bind_param("is", $st, $datum);
        $stmt->execute();
        header("location: stran.php");
    }
    else{
        header("location: prijava.php");
    }
}
?>

c)	Napišite program (spletno stran sestavljeno iz ene ali več skript), ki predstavlja osnovni obrazec aplikacije. 
Stran naj bo dostopna le registriranim uporabnikom. Spletna stran naj ponuja izbiro izpisa dijakov, dodajanja novega dijaka in odjavo iz sistema. 
Spletna stran tudi prikaže uporabniško ime in datum zadnjega dostopa do aplikacije. Če uporabnik izbere odjavo, ga aplikacija vrne na spletno stran za prijavo v sistem. 

<?php
    session_start();
    if(!isset($_SESSION["ime"])){
        header("location: prijava.php");
    }
    echo $_SESSION["ime"];
    echo '<br/>';
    echo $_SESSION["datum"];
    echo '<br/>';
?>
<a href="izpisDijakov.php"> Izpis Dijakov </a><br/>
<a href="DodajanjeDijakov.php"> Dodajanje Dijaka</a><br/>
<a href="odjava.php"> odjava </a>

<?php
session_start();
unset($_SESSION);
session_destroy();
header("location:prijava.php");
?>

Izpis dijakov:
<?php
session_start();
if(!isset($_SESSION["ime"])){
    header("location: prijava.php");
}
$mysqli =  require __DIR__ . "/baza.php";
mysqli_report(MYSQLI_REPORT_STRICT);

$q = "SELECT * FROM Dijak d INNER JOIN Sola s ON s.SID = d.SID";
$stmt = $mysqli->stmt_init();
$stmt->prepare($q);
$stmt->execute();
$res = $stmt->get_result();
echo '<table><tr><td>'.'DID'.'</td><td>'.'ime'.'</td><td>'.'priimek'.'</td><td>'.'sola'.'</td><td>'.'spol'.'</td></tr>';
while($r = $res->fetch_assoc()){
   echo '<tr><td>'.$r["DID"].'</td>';
   echo '<td>'.$r["ime"].'</td>';
   echo '<td>'.$r["priimek"].'</td>';
   echo '<td>'.$r["imeSole"].'</td>';
   echo '<td>'.$r["spol"].'</td>';
   echo'</tr>';
}
$stmt->close();

?>
