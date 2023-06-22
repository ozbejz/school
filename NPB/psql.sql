NPB-VPRAŠANJA-PSQL (za bazo najemvozil)

1.  Napišite shranjeno proceduro, ki bo izpisala imena, priimke strank in številko šasije vozil za vse tiste stranke, ki so si v letu 2012 izposodile vsaj eno vozilo izbrane znamke (ta je podana kot argument procedure). Izpis naj bo oblikovan na naslednji način:

1. Stranka: Andreja, Turbovšek; Izposojeno vozilo: številka_šasije1   
2. Stranka: Andreja, Turbovšek; Izposojeno vozilo: številka_šasije2
....
1. Stranka: Miha, Čevljar; Izposojeno vozilo: številka_šasije1   
2. Stranka: Miha, ČEvlajr; Izposojeno vozilo: številka_šasije2
....
....

set term !! ;
create procedure nal1(z VARCHAR(20)) RETURNS(niz VARCHAR(250)) as
declare variable ime varchar(20);
declare variable priimek varchar(30);
declare variable s varchar(20);
declare variable x int default 1;
declare variable prev int -1;
declare variable cur int -1;
begin
  for select s. EMSO, s.Ime, s.Priimek, v.Serstev from Stranka s 
  inner join najem n on n.EMSO = s.EMSO
  inner join vozilo v on v.Serstev = n.Serstev and v.proizvajalecid = n.proizvajalecid
  inner join proizvajalec p on p.proizvajalecid = v.proizvajalecid
  where extract(year from datum_najema) = 2012 and p.znamka = :z 
order by ime
into :cur, :ime, :priimek, :s begin
    if(cur <> prev) then begin
      prev = cur;
      x = 0;
     end
    niz = x || '. Stranka: ' || ime || ', '||priimek||'; Izposojeno vozilo: '|| s;
    suspend;
    x = x+1;
  end
end !!
set term ; !!

2. Proceduro pod točko 1. spremenite tako, da bo ta imela dodan še en parameter, ki predstavlja še drugo znamko vozila. Procedura naj izpiše imena, priimke strank in številko šasije vozil za vse tiste stranke, ki so si v letu 2012 izposojale vozila, katerih znamka ustreza obema parametroma hkrati. Izpis naj bo oblikovan na naslednji način:
1. Stranka: A. Turbovšek; Izposojeno vozilo: številka_šasije1; Mesec: april   
2. Stranka: A., Turbovšek; Izposojeno vozilo: številka_šasije2: Mesec: Maj
....
1. Stranka: Miha, Čevljar; Izposojeno vozilo: številka_šasije1; Mesec: junij   
2. Stranka: Miha, ČEvlajr; Izposojeno vozilo: številka_šasije2; Mesec: september
....

set term !! ;
create procedure nal1(z VARCHAR(20), z1 VARCHAR(20)) RETURNS(niz VARCHAR(250)) as
declare variable ime varchar(20);
declare variable priimek varchar(30);
declare variable s varchar(20);
declare variable x int default 1;
declare variable prev int -1;
declare variable cur int -1;
begin
  for select s. EMSO, s.Ime, s.Priimek, v.Serstev from Stranka s 
  inner join najem n on n.EMSO = s.EMSO
  inner join vozilo v on v.Serstev = n.Serstev and v.proizvajalecid = n.proizvajalecid
  inner join proizvajalec p on p.proizvajalecid = v.proizvajalecid
  where extract(year from datum_najema) = 2012 and p.znamka = :z and s.EMSO in(
    select n.EMSO from najem n 
    inner join vozilo v on v.Serstev = n.Serstev and v.proizvajalecid = n.proizvajalecid
    inner join proizvajalec p on p.proizvajalecid = v.proizvajalecid
    where p.znamka = :z1
  ) 
  order by ime, datum_najema
  into :cur, :ime, :priimek, :s begin
    if(cur <> prev) then begin
      prev = cur;
      x = 0;
    end
    niz = x || '. Stranka: ' || substring(ime from 1 for 1) || '. '||priimek||'; Izposojeno vozilo: '|| s;
    suspend;
    niz = 'Mesec: ' || extract(month from datum_najema);
    suspend;
    x = x+1;
  end
end !!
set term ; !!

3. Proceduro pod točko 2. dopolnite tako, da bo ta na kocu še izpisala vse podatke tiste stranke, ki si je med vsemi izpisanimi strankami izposodila največ vozil.

set term !! ;
create procedure nal1(z VARCHAR(20), z1 VARCHAR(20)) RETURNS(niz VARCHAR(250)) as
declare variable ime varchar(20);
declare variable priimek varchar(30);
declare variable s varchar(20);
declare variable x int default 1;
declare variable prev int default -1;
declare variable cur int default -1;
declare variable naj int default 0;
declare variable leto int;
begin
  for select s. EMSO, s.Ime, s.Priimek, v.Serstev from Stranka s 
  inner join najem n on n.EMSO = s.EMSO
  inner join vozilo v on v.Serstev = n.Serstev and v.proizvajalecid = n.proizvajalecid
  inner join proizvajalec p on p.proizvajalecid = v.proizvajalecid
  where extract(year from datum_najema) = 2012 and p.znamka = :z and s.EMSO in(
    select n.EMSO from najem n 
    inner join vozilo v on v.Serstev = n.Serstev and v.proizvajalecid = n.proizvajalecid
    inner join proizvajalec p on p.proizvajalecid = v.proizvajalecid
    where p.znamka = :z1
  ) 
  order by ime, datum_najema
  into :cur, :ime, :priimek, :s begin
    if(cur <> prev) then begin
      prev = cur;
      x = 0;
    end
    if(x > naj) then
      naj = emso;
    niz = x || '. Stranka: ' || substring(ime from 1 for 1) || '. '||priimek||'; Izposojeno vozilo: '|| s;
    suspend;
    niz = 'Mesec: ' || extract(month from datum_najema);
    suspend;
    x = x+1;
  end
  select * from Stranka where emso = :naj into :x, :ime, :priimek, :leto
  niz = 'Naj stranka: ' ||x||' '||ime||' '||priimek||' '||leto;
  suspend;
end !!
set term ; !!

4. Napišite shranjeno izvršno proceduro, ki v tabelo Stranka in hkrati v tabelo Voznisko_dovoljenje doda nov zapis. Vhodni parametri procedure so: EMSO, ime, priimek, letnik, datum_izdaje, datum ter datum veljavnosti. Številka vozniškega dovoljenja naj se generira naključno, tako da bo ta pozitivna in vsaj 6-mestna. Procedura naj še preverja, koliko časa ima stranka vozniško dovoljenje. Če ima ta vozniško dovoljenje manj kot 5 let, naj procedura vrne izjemo: »Vnos zavrnjen, stranka mora imeti vozniško dovoljenje vsaj 5 let ali več!«.   Procedura naj obravnava izjemo za podvojeni ključ in splošne izjeme.

create exception premalo 'Vnos zavrnjen, stranka mora imeti vozniško dovoljenje vsaj 5 let ali več!';

set term !! ;
create procedure nal4(EMSO int, ime varchar(20), priimek varchar(30), letnik int, datum_izdaje DATE, datum_veljavnosti DATE)
RETURNS(niz VARCHAR(20)) as
declare variable st int;
begin
if(extract(year from current_date) - extract(year from datum_izdaje) < 5) then
  exception premalo;
st = srand()*1000000;
insert into stranka values(:EMSO, :ime, :priimek, :letnik);
insert into voznisko_dovoljenje values(:st, :EMSO, :datum_izdaje, 0);
when SQLCODE -803 do begin
  niz = 'podvojeni kluč';
end
when any do begin
  niz = 'napaka v dodajanju';
end
end!!
set term ; !!



