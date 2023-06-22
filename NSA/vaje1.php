Naloga 1
Napišite program, ki omogoča dodajanje podatkov o stavbi. Na obrazcu (glej sliko) uporabnik vnese le podatke StavbaID (celo število > 0), Naslov (niz vsaj 2, največ 30 znakov, prvi znak moram biti velika črka, sledi zaporedje velikih / malih črk, števk in presledkov) in Kraj (niz vsaj dve, največ tridesetih znakov, velika začetnica, ostali znaki so velike / male črke in presledki). V naslovu in kraju so lahko tudi šumniki. Veljavnost podatkov validirajte s atributom pattern. Očiščene podatke zapišite v tabelo Stavba. Skripto za dodajanje zapisa realizirajte z uporabo prepared statement. Vrednost podatka SteviloPrebivalcev ne vpisujete (defaut vrednost bo 0 – glej show create table Stavba). Po dodajanju zapisa, naj uporabnik dobi obvestilo: zapis dodan ali napaka pri dodajanju, zapis NI dodan.
 


Testirajte program z vnosom naslednjih podatkov:
1, Vegova 1, ljubljana // zapis mora biti zavrnjen
1, Vegova 1, Ljubljana
2, Vegova 4, Ljubljana
3, Šišenska 23a, Ljubljana
4, Dunajska 22, Ljubljana
5, Rateče 3, Rateče
6, Koroška 33, Kranjska Gora
6, Slovenska 2, Želimlje // zapis mora biti zavrnjen
7, Slovenska 2, Želimlje
8, Slovenska 33, Ljubljana
9, Ižanska 2, Ljubljana
10, Ljubljanska 1, Ig

<form action="nal1.php">
    StavbaID <input type="number" min="0" name="StavbaID" /><br/>
    Naslov <input type="text" name="naslov" pattern="[A-ZČŠŽ][a-zčšž0-9\s]+" minlength="2" maxlength="30" /> <br/>
    Kraj <input type="text" name="kraj" pattern="[A-ZČŠŽa-zčšž\s]+" minlength="2" maxlength="30" /> <br/>
    <input type="submit" value="shrani"/>
</form>


<?php

$mysqli = new mysqli("localhost", "root", "", "GeodetskaUprava");

if($mysqli->connect_error){
    die("zapis ni dodan");
}

$q = "INSERT INTO Stavba(StavbaID,Naslov,Kraj) VALUES(?,?,?)";

$stmt = $mysqli->prepare($q);

$stmt->bind_param("iss", $_GET["StavbaID"], $_GET["naslov"], $_GET["kraj"]);

if($stmt->execute()){
    die("dodajanje uspesno");
}
else{
    die("zapis ni dodan");
}
?>

Naloga 2
Napišite program PHP, ki omogoča dodajanje podatkov o enem stanovanju. Vnos podatka StavbaID naj bo realizirana s pomočjo elementa select, ostale podatke uporabnik vpiše v navadne elemente input. Za prenos podatkov uporabite metodo get. Pri vnosu vrednosti in površine omogočite vnos realnih števil z 2 decimalki. Najmanjša dovoljene vrednosti so: za podatek Številka stanovanja 1; za podatek Površina 1; za podatke Prijavljenih oseb 0 in za podatek Vrednost 1000. Po dodajanju uporabnik mora dobiti obvestilo 'podatki so uspešno dodani' ali 'podatki niso dodani'. Po uspešno dodanem stanovanju je potrebno poskrbeti za ustrezno posodobitev podatka SteviloPrebivalvec v tabeli Stavba. Pričakovana oblika uporabniškega vmesnika za vnos podatkov:
 
Skripto testirajte s podatki:
4; 1; 120,25; 3; 350015,15
4; 2; 32,25; 1; 50015,50
4; 3; 200,55; 2; 48000,00
1; 1; 77,50; 2; 290000,50

<form action="nal2.php">Stavba 
    <select name="stavba">
        <?php
        $mysqli = new mysqli("localhost", "root", "", "GeodetskaUprava");
        $q = "SELECT * FROM Stavba";
        $res = $mysqli->query($q);
        while($r = $res->fetch_assoc()){
            echo '<option value="'.$r["StavbaID"].'">'.$r["StavbaID"].' ('.$r["Kraj"].', '.$r["Naslov"].')'.'</option>';
        }
        ?>
    </select><br/>
    Številka stanovanja <input type="number" min="1" name="stevilka" /><br/>
    Površina <input type="number" step=".01" name="povrsina" min="1"/><br/>
    Prijavljenih oseb <input type="number" name="stOseb" min="0"/><br/>
    Vrednost <input type="number" step=".01" name="vrednost" min="1000"/><br/>
    <input type="submit" value="shrani"/>
</form>

<?php

$mysqli = new mysqli("localhost", "root", "", "GeodetskaUprava");

if($mysqli->connect_error){
    die("zapis ni dodan");
}

$q = "INSERT INTO Stanovanje(StavbaID,Zap_ST,Povrsina_kvadrati,Prijavljenih,VrednostStanovanja) VALUES(?,?,?,?,?)";

$stmt = $mysqli->prepare($q);

$stmt->bind_param("iiiii", $_GET["stavba"], $_GET["stevilka"], $_GET["povrsina"], $_GET["stOseb"], $_GET["vrednost"]);

if($stmt->execute()){
    $q = "UPDATE Stavba SET SteviloPrebivalcev = SteviloPrebivalcev + ? WHERE StavbaID=?";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("ii", $_GET["stOseb"], $_GET["stavba"]);
    $stmt->execute();
    die("dodajanje uspesno");
}
else{
    die("zapis ni dodan");
}

?>

Naloga 3
Napišite program PHP, ki v tabelarični obliki izpiše naslove stavb v kraju X, ki imajo več kot N prebivalcev. Ime kraja uporabnik izbere iz elementa select (napolnite ga tako, da vnesete kraje iz tabele Stavba), število prebivalcev N pa vpiše (minimalno število je 0). Izpis realizirajte s funkcijo in naj bo urejen po padajočem številu prebivalcev. 
Pričakovana oblika uporabniškega vmesnika:
 

Primer izpisa za kraj Ljubljana in število prebivalcev > 1.  

<form action="nal3.php">
    <select name="stavba">
        <?php
        $mysqli = new mysqli("localhost", "root", "", "GeodetskaUprava");
        $q = "SELECT DISTINCT Kraj FROM Stavba ORDER BY Kraj";
        $res = $mysqli->query($q);
        while($r = $res->fetch_assoc()){
            echo '<option value="'.$r["Kraj"].'">'.$r["Kraj"].'</option>';
        }
        ?>
    </select><br/>
    <input type="number" min="0" name="stOseb" /><br/>
    <input type="submit" value="shrani"/>
</form>

<?php

$mysqli = new mysqli("localhost", "root", "", "GeodetskaUprava");

if($mysqli->connect_error){
    die("zapis ni dodan");
}

$q = "SELECT * FROM Stavba WHERE Kraj = ? AND SteviloPrebivalcev > ?";

$stmt = $mysqli->prepare($q);

$stmt->bind_param("ii", $_GET["stavba"], $_GET["stOseb"]);

if($stmt->execute()){
    $res = $stmt->get_result();
    echo '<table><td>Naslov</td><td>Št. prebivalcev</td>';
    while($r = $res->fetch_assoc()){
        echo '<tr><td>'.$r["Naslov"].'</td><td>'.$r["SteviloPrebivalcev"].'</tr>';
    }
}
else{
    die("neki ne dela");
}
?>
