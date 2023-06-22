NPB	8e. vaja

Vsebina: FB sprožilci, izjeme, beleženje dogodkov

Prenesite PB Trgovina.fdb.
1.	naloga
a)	Napišite sprožilec, ki bo zagotavljal, da v posamezni kategoriji imamo največ 4 izdelke. V primeru kršitve tega pravila, 
naj se sproži izjema prevec_izdelkov s sporočilom 'V tej kategoriji je kvota izdelkov zapolnjena.'
b)	Trigger testirajte 2x:
•	V tabelo Izdelek vpišite izdelek 8,'Makaroni',1.80,100,3. Ali se je sprožila izjema prevec_izdelkov?
•	V tabelo Izdelek vpišite izdelek 9,'Jušni reznaci',2.30,100,3. Ali se je sprožila izjema prevec_izdelkov?

create exception prevec_izdelkov 'V tej kategoriji je kvote izdelkov zapolnjena';

set term !! ;
create trigger nal1 for Izdelek before insert as
declare variable s int default 0;
begin
select count(*) from Izdelek where KID = new.KID into :s;
if(s > 4) then
  exception prevec_izdelkov; 
end!!
set term ; !!

insert into Izdelek values(8,'Makaroni', 1.80,100,3);
insert into Izdelek values(9,'Jušni reznaci',2.30,100,3);

2.	naloga

a)	Denimo, da so cene vseh izdelkov, ki sodijo v kategorijo z DDV 8% zamrznjene. To pomeni, da se ti izdelki ne smejo podražiti, lahko pa se pocenijo. 
Napišite sprožilec, ki bo realiziral to poslovno pravilo. V primeru kršitve pravila, naj se sproži izjema zamrznjena_cena z besedilom 'Cena izdelka je zamrznjena.'
b)	Sprožilec testirajte 3x:
•	Izdelku Union Grand povečajte ceno za 0.5. Ali se je sprožila izjema zamrznjena_cena?
•	Izdelku Malinov sirup povečajte ceno za 0.8. Ali se je sprožila izjema zamrznjena_cena?
•	Izdelku Domači rezanci zmanjšajte ceno za 0.20. Ali se je sprožila izjema zamrznjena_cena?

create exception zamrznjena_cena 'Cena izdelka je zamrznjena';

set term !! ;
create trigger nal2 for Izdelek before update as
declare variable ddv float;
begin
select DISTINCT(k.DDV) from Kategorija k INNER JOIN Izdelek i ON i.KID = k.KID where i.KID = new.KID into :DDV;
if(DDV > 0.07 and DDV<0.2 and new.cena <> old.cena) then
  exception zamrznjena_cena;
end!!
set term ; !!

update Izdelek set cena = cena-0.5 where ime_izdelka = 'Union Grand';
update Izdelek set cena = cena+0.8 where ime_izdelka = 'Malinov sirup';
update Izdelek set cena = cena+0.20 where ime_izdelka = 'Domači rezanci';

3.	naloga
a)	Naredite tabelo LogIzdelkov(Uporabnik:A10,Datum:D,Cas:T, IID:N).Napišite sprožilec, ki bo ob dodajanju novih izdelkov v tabelo Izdelek, 
samodejno v tabeli LogIzdelkov beležil kdo je dodal izdelek, kdaj (datum in čas) in IID izdelka, ki je bil dodan. 
// Uporabniško ime dobite iz sistemske spremenljivke current_user, datum dobite iz sistemske spremenljivke current_date in čas dobite iz sistemske spremenljivke current_time. 
b)	Sprožilec testirajte 2x in potem izpišite vsebino tabele LogIzdelkov. V tabelo izdelek dodajte zapisa:
•	V tabelo Izdelek vpišite izdelek 9,'Cocta',1.80,300,1.
•	V tabelo Izdelek vpišite izdelek 10,'Srebrna Radgonska Penina',8.40,300,2.
c)	Naredite novega uporabnika. Uporabniško ime=Piki, geslo=Piki, ime in priimek sta poljubna. Uporabniku Piki dovolite vnos podatkov v tabelo Izdelek.
d)	Povežite se s PB Trgovina kot uporabnik Piki in dodajte zapis: 11,'Traminec',7.80,300,2.
e)	Povežite se s PB Trgovina kot uporabnik SYSDBA in izpišite vsebino tabel Izdelek in LogIzdelkov.

create table LogIzdelkov(
  Uporabnik VARCHAR(10),
  Datum date,
  Cas time,
  IID int,
  primary key(Uporabnik, Datum, Cas)
);

set term !! ;
create trigger nal3 for Izdelek after insert as
begin
insert into LogIzdelkov values(current_user, current_date, current_time, new.IID);
end!!
set term ; !!

insert into Izdelek values(9,'Cocta',1.80,300,1);
insert into Izdelek values(12,'Srebrna Penina',8.40,300,2);

4.	naloga
a)	Napišite sprožilec, ki prepreči brisanje izdelkov dobaviteljev iz Ljubljane. 
Ob poskusu nedovoljenega brisanja naj se sproži izjema prepovedano_brisanje z besedilom 'Brisanje izdelkov dobaviteljev iz Ljubljane ni dovoljeno.' 
b)	Sprožilec testirajte 2x:
•	Izbrišite izdelek 'Srebrna Radgonska Penina'. Ali se je sprožila izjema prepovedano_brisanje?
•	Izbrišite izdelek 'Domači rezanci'. Ali se je sprožila izjema prepovedano_brisanje?
c)	Izpišite seznam vseh sprožilcev v PB.

create exception prepovedano_brisanje 'Brisanje izdelkov dobaviteljev iz Ljubljane ni dovoljeno';

set term !! ;
create trigger nal4 for Izdelek before delete as
declare variable dob int;
begin
select d.pst from dobavitelj d INNER JOIN Izdelek i ON i.DID = d.DID where i.IID = old.IID into :dob;
if(dob = 1000) then
  exception prepovedano_brisanje;
end!!
set term ; !!

delete from izdelek where ime_izdelka = 'Srebrna Penina';
delete from izdelek where ime_izdelka = 'Domači rezanci';

SELECT * FROM RDB$TRIGGERS;

