NPB	8f. vaja

Vsebina: 	
        Osnovni in univerzalni sprožilci, testiranje
		Beleženje dogodkov (log)
		Generatorji


Prenesite PB Trgovina.fdb.
1.	Naloga
Izbrišite sprožilec za beleženje dodajanj novih izdelkov (narejen je bil za  3. nalogo prejšnje vaje). Izbrišite tabelo LogIzdelkov in naredite novo: (Uporabnik:A10,Datum:D,Cas:T, IID:N, VrstaSpremembe:A10).Napišite sprožilec, ki bo ob katerikoli spremembi podatkov tabele Izdelek, samodejno v tabeli LogIzdelkov beležil kdo je izvedel spremembo, kdaj (datum in čas), IID izdelka, ki je bil dodan in vrsto spremembe (dodan, brisan, spremenjen). // Uporabniško ime dobite iz sistemske spremenljivke current_user, datum dobite iz sistemske spremenljivke current_date in čas dobite iz sistemske spremenljivke current_time. 
a)	Sprožilec testirajte 2x in potem izpišite vsebino tabele LogIzdelkov. V tabelo izdelek dodajte zapisa:
•	V tabelo Izdelek vpišite izdelek 9,'Cocta',1.80,300,1.
•	V tabelo Izdelek vpišite izdelek 10,'Srebrna Radgonska Penina',8.40,300,2.
b)	Naredite novega uporabnika. Uporabniško ime=Piki, geslo=Piki, ime in priimek sta poljubna. Uporabniku Piki dovolite vnos, brisanje in spreminjanje podatkov tabele Izdelek.
c)	Povežite se s PB Trgovina kot uporabnik Piki in 
•	dodajte zapis: 11,'Traminec',7.80,300,2
•	izbrišite zapis 9,'Cocta', ...
•	izdelku 10 spremenite ceno na 9.20
d)	Povežite se s PB Trgovina kot uporabnik SYSDBA in izpišite vsebino tabel Izdelek in LogIzdelkov.
create table LogIzdelkov(
  Uporabnik VARCHAR(10),
  Datum DATE,
  Cas TIME,
  IID INT NOT NULL,
  VrstaSpremembe VARCHAR(10) NOT NULL,
  PRIMARY KEY(Uporabnik, Datum, Cas)
);

set term !! ;
create trigger nal1 for izdelek after update or insert or delete as
declare variable d VARCHAR(10);
declare variable iid int;
begin
if(INSERTING) then begin
  d = 'dodan';
  iid = new.IID;
end
if(UPDATING) then begin
  d = 'spremenjen';
  iid = new.IID;
end
if(DELETING) then begin
  d = 'zbrisan';
  iid = old.IID;
end
insert into LogIzdelkov VALUES(current_user, current_date, current_time, :iid, :d);
end!!
set term ; !!

drop trigger nal1;

insert into izdelek VALUES(9,'Cocta',1.80,300,1);
insert into izdelek VALUES(10,'Srebrna Radgonska Penina',8.40,300,2);
insert into izdelek VALUES(11,'Traminec',7.80,300,2);
delete from izdelek where IID = 9;
update izdelek set cena = 9.2 where IID = 10;

 
2.	naloga
a)	Kreirajte tabelo Stranka (SID:N,ImeStranke:A10,PriimekStranke:A20,PST:N)
create table Stranka(
  SID INT PRIMARY KEY,
  ImeStranke VARCHAR(10) NOT NULL,
  PriimekStranke VARCHAR(20) NOT NULL,
  PST INT NOT NULL
);

b)	Naredite generator G_Stranka in ga inicializirajte na 1.

create generator G_Stranka;
set generator G_Stranka to 1;

c)	Napišite sprožilec, ki ob dodajanju nove stranke s pomočjo generatorja G_Stranka samodejno ustvari novo številko stranke (podatek SID). 

set term !! ;
create trigger nal2 for Stranka before insert as begin
new.SID = GEN_ID(G_Stranka, 1);
end!!
set term ; !!

d)	V tabelo Stranka dodajte zapise:
•	Marko,Kos,2000
•	Peter,Roban,400
•	Vasja,Medved,1000
•	Vinko,Jager,1000

insert into Stranka(ImeStranke, PriimekStranke, PST) VALUES('Marko','Kos',2000);
insert into Stranka(ImeStranke, PriimekStranke, PST) VALUES('Peter','Roban',400);
insert into Stranka(ImeStranke, PriimekStranke, PST) VALUES('Vasja','Medved',1000);
insert into Stranka(ImeStranke, PriimekStranke, PST) VALUES('Vinko','Jager',1000);

e)	Napišite sprožilec, ki prepreči spreminjanje številke stranke. Če pride do kršitve, sprožite izjemo ex_sid z besedilom 'Številka stranke je nespremenljiva.'

create exception ex_sid 'Številka stranke je nespremenljiva';
set term !! ;
create trigger nal2e for Stranka before update as begin
if(new.SID <> old.SID) then
  exception ex_sid;
end!!
set term ; !!

f)	Preverite delovanje sprožilca s stavkom Update Stranka SET SID=8 Where SID=2;
 
g)	Naredeite tabelo LogIzdelkov(Uporabnisko imeNapišite sprožilec, ki beleži vse spremembe tabele Izdelek. Ob sleherni spremembi podatkov se v tabelo LogIzdelkov zabeležijo podatki: UporabniškoIme, DatumSpremembe, ČasSpremembe, Akcija in IID. Podatek akcija je tipa char(10) in dobi vrednost 'dodajanje', 'brisanje' ali 'spreminjanje'. Podatki UporabniškoIme, DatumSpremembe in ČasSpremembe se preberejo iz sistemskih spremenljivk. Podatek IID je šifra izdelka, na katerega se je nanašala akcija. Testirajte sprožilec tako, da najprej dodate in potem izbrišete en izdelek s poljubnimi podatki.

h)	Napišite sprožilec, ki bo zagotavljal, da je podatek PST v tabeli Stranka tuji ključ, ki kaže na tabelo Kraj. Zvrst povezave naj bo 'no action'. V primeru brisanja ali spreminjanja ključa tabele PST, naj se sproži izjema ex_fk_stranka z besedilom 'Prepovedana operacija, v tabeli Stranka obstaja zapis s to vrednostjo'.

create exception fk 'mora biti tuji ključ';
create exception ex_fk 'Prepovedana operacija, v tabeli Stranka obstaja zapis s to vrednostjo';
set term !! ;
create trigger nal2h for Stranka before update or insert or delete as
declare variable pst INT;
declare variable pst1 INT;
begin
  if(DELETING) then
    pst1 = old.pst;
  else
    pst1 = new.pst;
  select PST from Kraj where PST = :pst1 into :pst;
  if(pst is null) then
    exception fk;
  if(old.pst <> new.pst) then
    exception ex_fk;
end!!
set term ; !!