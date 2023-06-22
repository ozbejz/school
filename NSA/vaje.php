Na vašem mySQL strežniku imate bazo bazaOseb.
V PB bazaOseb sta tabeli kraj(KID:N, imeKraja:A20) in
oseba(id:N, ime:A10, priimek:A20, rojstvo:D, KID:NKraj, spol: A1, email:A20, opiso:A150).
V tabeli kraj je že zapisanih nekaj podatkov, ravno tako tudi v tabeli oseba. Ime strežnika je localhost, uporabniško ime root, geslo je prazno. 


Naloga 1
a)	Izdelajte obrazec za izbiro enega ali več meril za razvrščanje podatkov. Če je izbranih več meril, si kriteriji razvrščanja sledijo po vrstnem redu na obrazcu (prvo merilo je ime kraja, drugo priimek in tretje starost. Zahtevana oblik obrazca:
 
Skripta naj izpiše ime kraja, priimek, ime in datum rojstva osebe. Razvrščene podatke naj program izpiše v HTML tabeli. Primer zahtevane tabelarične oblike izpisa, če je izbrano razvrščanje le po priimkih:
 
<form action="nal1izpis.php">
    razvrsti po <br/>
    <input type="checkbox" name="kraj" value="imeKraja"> krajih </input> <br/>
    <input type="checkbox" name="priimek" value="priimek"> priimkih </input><br/>
    <input type="checkbox" name="starost" value="rojstvo"> starosti </input><br/>
    <input type="submit" value="izpis" />
</form>

<link rel="stylesheet" href="stili.css" />
<?php

$mysqli = new mysqli("localhost", "root", "", "bazaOseb");

$q = "SELECT o.ime, o.priimek, o.rojstvo, k.imeKraja FROM oseba o INNER JOIN Kraj k ON k.KID = o.KID ORDER BY ?,?,?";

$order = "";
$c = 0;
if(isset($_GET["kraj"])) {
    $order = "imeKraja";
    $c++;
}
if(isset($_GET["priimek"])) {
    if($c>0)
        $order .= ", priimek";
    else
        $order .= "priimek";
    $c++;
}
if(isset($_GET["starost"])) {
    if($c>0)
        $order .= ", rojstvo";
    else
        $order .= "rojstvo";
}

$q = "SELECT o.ime, o.priimek, o.rojstvo, k.imeKraja FROM oseba o INNER JOIN Kraj k ON k.KID = o.KID ORDER BY $order";

$rs = $mysqli->query($q);

echo '<table>';
while($r = $rs->fetch_assoc()){
    echo '<tr><td>'.$r["imeKraja"].'</td><td>'.$r["priimek"].'</td><td>'.$r["ime"].'</td><td>'.$r["rojstvo"].'</td></tr>';
}
echo '</table>';

?>

b) Predelajte izpis tako, da je ozadje stolpca s podatkom, ki je prvo merilo razvrščanja, v svetlo zeleni barvi. (v zgornjem primeru je to 2. stolpec).
<link rel="stylesheet" href="stili.css" />
<?php

$mysqli = new mysqli("localhost", "root", "", "bazaOseb");

$q = "SELECT o.ime, o.priimek, o.rojstvo, k.imeKraja FROM oseba o INNER JOIN Kraj k ON k.KID = o.KID ORDER BY ?,?,?";

$prvi = "";
$order = "";
$c = 0;
if(isset($_GET["kraj"])) {
    $order = "imeKraja";
    $c++;
    $prvi = "imeKraja";
}
if(isset($_GET["priimek"])) {
    if($c>0)
        $order .= ", priimek";
    else{
        $prvi = "priimek";
        $order .= "priimek";
    }
    $c++;
}
if(isset($_GET["starost"])) {
    if($c>0)
        $order .= ", rojstvo";
    else{
        $order .= "rojstvo";
        $prvi = "rojstvo";
    }
}

$q = "SELECT o.ime, o.priimek, o.rojstvo, k.imeKraja FROM oseba o INNER JOIN Kraj k ON k.KID = o.KID ORDER BY $order";

$rs = $mysqli->query($q);

echo '<table>';
while($r = $rs->fetch_assoc()){
    switch($prvi){
        case "imeKraja":
            echo '<tr><td class="prvi">'.$r["imeKraja"].'</td><td>'.$r["priimek"].'</td><td>'.$r["ime"].'</td><td>'.$r["rojstvo"].'</td></tr>';
            break;    
        case "priimek":
            echo '<tr><td>'.$r["imeKraja"].'</td><td class="prvi">'.$r["priimek"].'</td><td>'.$r["ime"].'</td><td>'.$r["rojstvo"].'</td></tr>';
            break;  
        case "rojstvo":
            echo '<tr><td>'.$r["imeKraja"].'</td><td>'.$r["priimek"].'</td><td>'.$r["ime"].'</td><td class="prvi">'.$r["rojstvo"].'</td></tr>';
            break;  
    }
}
echo '</table>';

?>

c) Dopolnite obrazec iz a) dela naloge tako, da uporabnik poleg razvrščanja ima možnost izbire podatkov, ki jih želi prikazati. Nato naj skripta izpiše le zahtevani podatek /zahtevane podatke razvrščene po izbranem (izbranih) kriterijih. Izhodišče je rešitev prejšnje naloge. Ni nujno, ampak predlagam, da za izbiro podatka/podatkov za prikaz uporabite element select. Primer izpisa, če sta izbrana podatka bila ime kraja in priimek osebe in je kriterij sortiranja bil priimek osebe.
    <link rel="stylesheet" href="stili.css" />
<?php

$mysqli = new mysqli("localhost", "root", "", "bazaOseb");

$q = "SELECT o.ime, o.priimek, o.rojstvo, k.imeKraja FROM oseba o INNER JOIN Kraj k ON k.KID = o.KID ORDER BY ?,?,?";

$order = "";
$c = 0;
if(isset($_GET["kraj"])) {
    $order = "imeKraja";
    $c++;
}
if(isset($_GET["priimek"])) {
    if($c>0)
        $order .= ", priimek";
    else
        $order .= "priimek";
    $c++;
}
if(isset($_GET["starost"])) {
    if($c>0)
        $order .= ", rojstvo";
    else
        $order .= "rojstvo";
}

$q = "SELECT o.ime, o.priimek, o.rojstvo, k.imeKraja FROM oseba o INNER JOIN Kraj k ON k.KID = o.KID ORDER BY $order";

$rs = $mysqli->query($q);

echo '<table>'; 
while($r = $rs->fetch_assoc()){
    echo '<tr>';
    foreach($_GET["sort"] as $val){
        echo '<td>'.$r[$val] .'</td>';
    }
    echo '</tr>';
}

echo '</table>';

?>

Naloga 2
Ustvarite formo in napišite program, ki omogoča vnos zapisa v tabelo barve. Veljavnost podatkov preverite na strani odjemalca in strežnika. Po dodajanju uporabnik dobi obvestilo 'Zapis dodan' ali 'Prišlo je do napake, zapis NI dodan'. Primer oblike uporabniškega vmesnika:
 
Skripto testirajte z naslednjo sekvenco podatkov:
3, zelena
4, rdeča
4, črna  // zapis mora biti zavrnjen
5, črna
6, a2kca // zapis mora biti zavrnjen
6, rumena

<form action="nal2izpis.php">
    <input type="number" name="ID" placeholder="celo stevilo" pattern="\d" required/><br/>
    <input type="text" name="barva" placeholder="barva z besedo" pattern="[a-zčšž]+" required/><br/>
    <input type="submit" value ="submit">
</form>


<link rel="stylesheet" href="stili.css" />
<?php

$mysqli = new mysqli("localhost", "root", "", "geometrija");

$q = "INSERT INTO Barve VALUES(?,?)";

$stmt = $mysqli->prepare($q);

if(preg_match("/^[0-9]+$/", $_GET["ID"]) && preg_match("/^[a-zčšž]+$/", $_GET["barva"])){
    $stmt->bind_param("is", $_GET["ID"], $_GET["barva"]);
}
else{
    die("napaka");
}
?>