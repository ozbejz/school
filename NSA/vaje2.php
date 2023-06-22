Prenesite in namestite PB GeodetskaUprava, ki ste jo ustvarili z izvedbo 23. vaje. V skripti so še shranjeni testni podatki. V bazi sta tabeli
•	Stavba(StavbaID:N, Naslov:A20, Kraj:A20, SteviloPrebivalcev:N) in
•	Stanovanje(StavbaID:N-->Stavba, Zap_ST:N, Povrsina_kvadrati:N, Prijavljenih:N, VrednostStanovanja:N).
Pri realizaciji nalog uporabite
•	API mysqli in prepared statements

Naloga 0 - 0
V PB GeodetskaUprava ustvarite tabelo Uporabnik(uIme:A20, uGeslo:A200).
Naredite program (obrazec in skripto), ki omogoča registracijo uporabnika. Za razprševanje gesla uporabite algoritem sha1. V geslu mora biti vsaj 1 števka in vsaj 1 črka.


<form action="process-registracija.php" method="post">
    <input type="text" name="ime" placeholder="ime" required/> <br/>
    <input type="password" name="geslo" placeholder="geslo" required/> <br/>
    <input type="submit" value="registracija" />
</form>

<?php

if(isset($_POST["ime"]) && isset($_POST["geslo"])){
    $mysqli = new mysqli('localhost', 'root', '', 'geodetskauprava');
    if($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    $q = "INSERT INTO uporabnik(ime, geslo) VALUES(?,?)";
    $stmt = $mysqli->prepare($q);

    $password_hash = password_hash($_POST["geslo"], PASSWORD_DEFAULT);

    $stmt->bind_param("ss", $_POST["geslo"], $password_hash);

    if($stmt->execute()){
        echo 'dela';
    }
    $stmt->close();
}

?>

Naloga 0 – 1
Dostop do skript, ostalih nalog vaje mora biti zaščiten  ustvarite obrazec za prijavo in preverjanje podatkov o uporabniku.

<form action="process-prijava.php" method="post">
    <input type="text" name="ime" placeholder="ime" required/> <br/>
    <input type="password" name="geslo" placeholder="geslo" required/> <br/>
    <input type="submit" value="prijava" />
</form>

<?php

if(isset($_POST["ime"]) && isset($_POST["geslo"])){
    if(!isset($_SESSION)) {
        session_start();
     }
    $mysqli = new mysqli('localhost', 'root', '', 'geodetskauprava');
    if($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    $q = "SELECT * FROM uporabnik WHERE ime = ?";
    $stmt = $mysqli->prepare($q);
    $stmt->bind_param("s", $_POST["ime"]);
    $stmt->execute();
    $rs = $stmt->get_result();
    $user = $rs->fetch_assoc();
    $stmt->close();
    if($user){
        if(password_verify($_POST["geslo"], $user["geslo"])){
            $_SESSION["user_id"] = $user["ID"];
            echo 'prijavljen';
        }
    }
}
?>

Naloga 1-0
Ustvarite program (skripto)¸ki bo omogočala dostop do naslednjih funkcij programa: 
•	Iskanje (Naloga1)
•	Vnos stanovanja (Naloga2)
•	Izpis (Naloga3)
•	Odjava (logout)

Naloga 1-1
Napišite program PHP, ki omogoča iskanje podatkov o stanovanjih. Kriterija za iskanje sta najmanjša zahtevana kvadratura in/ali najmanjša zahtevana vrednost.
 Hkrati uporabnik izbere smer sortiranja (naraščajoše/padajoče) posameznega kriterija. Prvi kriterij razvrščanja je kvadratura, drugi je cena stanovanja.
Zahtevana oblika vmesnika:  

Nato se podatki izpišejo v tabelarični obliki, primer izpisa:
 
Testni PODATKI: 
1.	Min. kvadratura=0; min. cena =1; sortiranje naraščajoče
2.	Min. kvadratura=100; min. cena=1; sortiranje padajoče po kvadraturi
3.	V url dopiši SQL injection: select user,host,password,4,5,6 from mysql.user

<?php
session_start();
if(!isset($_SESSION["user_id"])){
    header('location: http://localhost/prijava.php');
}
?>
iskanje stanovanja<form method="post" action="process-iskanje.php">
    Min. kvadratura<input type="text" name="kvadratura" pattern="[0-9]+" required/>
    Razvrsti<input type="radio" name="razvrscanje" value="nar">naraščajoče</input>
    <input type="radio" name="razvrscanje" value="pad">padajoče</input><br/>
    Min. cena <input type="text" name="kvadratura" pattern="[0-9]+" required/>
    Razvrsti<input type="radio" name="razvrscanje1" value="nar">naraščajoče</input>
    <input type="radio" name="razvrscanje1" value="pad">padajoče</input><br/>
    <input type="submit" value="Poišči"/>
</form>
