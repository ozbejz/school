NPB - 8d. vaja

Vsebina: Osnovni sprožilci, prioriteta sprožilcev

Prenesite PB GlasbenaZbirka_test3.fdb. 

1.	naloga
 V PB je tabela Avtor(AvtorID, Ime, Priimek). 
a)	S sprožilci zagotovite, da so vsi priimki avtorjev vpisani z velikimi črkami. Pri tem je potrebno Namig: potrebno je obravnavati dogodka insert in update. V primeru, da uporabnik vnese priimek avtorja le z veliko začetnico, sprožite izjemo, ki vrne sporočilo 'Priimek avtorja vpiši z velikimi črkami'.
Testirajte delovanje sprožilca. Rezultate testiranja zabeležite v poročilo vaje.
Dekativirajte sprožilec (ALTER)

CREATE exception narobe 'Priimek avtorja vpiši z velikimi črkami';

set term !! ;
create trigger nal1 for Avtor before insert or update as begin
  if(SUBSTRING(new.Priimek from 1 for 1) != UPPER(SUBSTRING(new.Priimek from 1 for 1)))
  then
    exception narobe;
  end!!
set term ; !!

ALTER trigger nal1 INACTIVE;

 
b)	Sprožilec pod točko a spremenite tako, da bodo vsi priimki avtorjev vpisani z veliko začetnico, 
pri čemer upoštevamo, da ima avtor lahko dva priimka. Namig: potrebno je obravnavati dogodka insert in update. 
V primeru, da uporabnik vnese enega od priimkov avtorja z malo začetnico, naj sprožilec samodejno popravi začetnico v veliko črko.
 Testirajte delovanje sprožilca. Rezultate testiranja zabeležite v poročilo vaje.

set term !! ;
create trigger nal1 for Avtor before insert or update as 
declare variable p int;
begin
  p = POSITION(' ' in new.Priimek);

  if(p != 0) then begin
    new.Priimek = SUBSTRING(new.Priimek from 1 for p) || UPPER(SUBSTRING(new.Priimek from p-1 for 1)) || SUBSTRING(new.Priimek from p+2);
  end

  if(SUBSTRING(new.Priimek from 1 for 1) != UPPER(SUBSTRING(new.Priimek from 1 for 1)))
  then begin
    new.Priimek = UPPER(SUBSTRING(new.Priimek from 1 for 1)) || SUBSTRING(new.Priimek from 2);
  end
  end!!
set term ; !!

 
2.	naloga
V PB je tabela Vsebina(CDIDCD,PIDPosnetek). 
a)	S sprožilci zagotovite, da je na posameznem CD zapisanih največ 10 posnetkov.  Namig: potrebno je obravnavati dogodka insert in update. 
V primeru, da uporabnik vnese 11. posnetek, sprožite izjemo, ki vrne sporočilo 'CD je poln (10 posnetkov) '.
Testirajte delovanje sprožilca. Rezultate testiranja zabeležite v poročilo vaje.

create exception prevec 'CD je poln (10 posnetkov)';

set term !! ;
create trigger nal2 for Vsebina before insert or update as 
  declare variable s int;
begin
  select count(PID) from Vsebina where CDID = new.CDID into :s;
  if(s > 10) then
    exception prevec;
  end!!
set term ; !!