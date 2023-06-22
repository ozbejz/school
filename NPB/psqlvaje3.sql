Vsebina: PSQL (Firebird) in shranjene procedure
•	Izvršne shranjene procedure
•	Shranjene procedure za izbiranje
•	Parametri
•	Uporaba vgrajenih funkcij
•	Uporaba procedur: oblikovanje izpisa, štetje

1.	naloga
Prenesite PB Trgovina.fdb.
Napišite shranjeno proceduro ki bo izpisala ime_izdelka, ceno izdelka in ceno izdelka z DDV. Izpis naj bo urejen glede na stopnjo DDV. Zahtevana oblika izpisa:
Izpis
===================
Stopnja DDV 8%
=============
ime_izdelka1	Cena1	Cena1_z_DDV
ime_izdelka2	Cena2	Cena2_z_DDV
….
Stopnja DDV 20%
=============
ime_izdelkan	Cenan	Cenan_z_DDV
…
Napišite klic shranjene procedure.

SET TERM !! ;
CREATE PROCEDURE naloga1
RETURNS (vrstica VARCHAR(80)) AS 
DECLARE VARIABLE ime VARCHAR(80);
DECLARE VARIABLE cena FLOAT;
DECLARE VARIABLE davek FLOAT;
DECLARE VARIABLE zacasna FLOAT;
BEGIN
  zacasna = 0;
  FOR SELECT i.ime_izdelka, i.cena, k.ddv FROM Izdelek i INNER JOIN Kategorija k ON k.KID = i.KID ORDER BY k.ddv
  INTO :ime, :cena, :davek DO BEGIN
    IF(zacasna <> davek) THEN BEGIN
      vrstica = 'stopnja DDV ' || (davek*100);
      SUSPEND;
      vrstica = '=============';
      SUSPEND;
      zacasna = davek;
    END
    cena = cena*(1+davek);
    vrstica = ime || ' ' || cena;
    SUSPEND;
  END
END !!
SET TERM ; !!

SELECT * FROM naloga1;

 
2.	naloga
Ustvarite PB Liki.fdb. V baz ustvarite tabelo Tocka2D(x:n, y:n, barva:char(10)). Podatka x in y predstavljata koordinati točke v ravnini, barva je barva točke. V prvi inačici, naj bo tabela brez primarnega ključa.
•	Napišite proceduro, ki v tabelo doda novo točko. Koordinati točke sta naključni celi števili iz intervala [1..100], barva je naključna barva iz množice: {bela, modra, rdeca, zelena, rumena}.
•	Napišite proceduro, ki v tabelo doda n novih točk. Podatek n je parameter procedure.
•	Napišite proceduro, ki izpiše vsebino tabele Tocke2D po kvadrantih. Oblika izpisa:
Tocke v ravnini
====
Prvi kvadrant
	T(3,5,bela)
	T(18,6,modra)
	...
Skupaj: n točk
Drugi kvadrant
	T(3,-6,bela)
	T(1,-20,zelena)
	...
Skupaj: k točk
Tretji kvadrant
...
•	Naredite novo tabelo Tocka2D_v1, pri kateri bosta primarni ključ tabele sestavljali koordinati x in y. Prilagodite proceduro za vnos podatkov v tabelo novi strukturi – procedura naj vrne izpis, koliko točk je bilo dejansko dodanih v tabelo.

SET TERM !! ;
CREATE PROCEDURE vpis(n INT) AS
DECLARE VARIABLE y INT;
DECLARE VARIABLE x INT;
DECLARE VARIABLE r INT;
DECLARE VARIABLE s INT;
DECLARE VARIABLE barva VARCHAR(20);
BEGIN
  s = 0;
  WHILE(s < n) DO BEGIN
    x = CAST(RAND()*100 +1 AS INT);
    y = CAST(RAND()*100 +1 AS INT);
    r = CAST(RAND()*4 AS INT);
    IF(r = 0) THEN
      barva = 'bela';
    IF(r = 1) THEN
      barva = 'modra';
    IF(r = 2) THEN
      barva = 'rdeca';
    IF(r = 3) THEN
      barva = 'zelena';
    IF(r = 4) THEN
      barva = 'rumena';
    INSERT INTO Tocka2D VALUES(:x,:y,:barva);
    s = s + 1;
  END
END !!
SET TERM ; !!


Opomba: V poročilo vaje prekopirate kodo procedur in zaslonske slike izvedbe (klica) procedur. 
V glavo poročila vaje vpišite svoje razred, ime in priimek.
Poročilo oddate najkasneje 14 dni po zaključeni vaji v nabiralnik ekm.

