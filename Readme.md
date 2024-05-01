# Project setup / inditas

```
docker-compose up
```

# Eleresek

## Api
```
http://localhost:8200
```

## Phpmyadmin
```
http://localhost:8201
```

# PHP hibakezeles

## alkalmazas hibak - auth, hibas parameter
Az `app/Exception` -ben van egy `Http` ososztaly amit lehet szarzmastatni sajat hibauzenetekre.
Ha ilyen tipusu exception-t dobunk el barhol a kodban, akkor mindig json-kent kimegy a response-ba.

Pl Unauthorized error ha nem vagy bejelentkezve:
```
http://localhost:8200/sample/content/list
```
igy mar van 'login':
```
http://localhost:8200/sample/content/list?logged
```

## rendszer hibak, bugok
A php hibauzenetek ki van vezetve a consolera json-ben ott olvashato.


Ha valami megis elakadna itt lathato a logolas mukodese:
`` app/Application.php:14 ``


# Mysql patchek
Kezzel nem valtoztatunk a db-n, minden patchben kell hogy legyen!
## mukodese
api indulasaktor (`docker-compose up`) lefut a `phinx` ami a `mysql-migration` mappaban levo sqleket futtatja le

## uj patch letrehozas
`docker-compose up` -nak futnia kell aztan:
```
docker-compose exec api phinx create
```
ezutan letrejon egy uj fajl a `mysql-migration`-be

## patch futtatasa
ujra kell inditani az `api` servicet, hogy ujra lefutosson a `phinx`
```
docker-compose up
```
indulaskor a konzolban latszodni, hogy lefutott az uj patch, vagy esetleg hibat dobott

## hiba eseten
Ha inditasnal hibat dob az uj sql patchre a `phinx` attolmeg a db-ben lehet hogy par query befutott. MySql nem kezeli tranzakcioban pl a tabla letrehozast, szoval ha a tabla letrehozas lefut es a hiba utana  mondjuk egy insert-nel jon elo akkor a tabla mar letrejott a db-ben.

Ilyenkor nem tudjuk ujrafuttatni a patchet javitas utan, mert hibat fog dobni, hogy mar letezi a tabla, vagy a patch mar bekerult `phinxlog` tablaba "kesz"-kent ezert nem is futattaja.

Ezert eloszor revert-elni kell:
```
docker-compose exec api phinx rollback
```
Van, hogy ez is hibat dob, mert az eredeti query hibas volt.

Mindig csekkoljuk a db-t, hogy tenyleg kiszedte-e a modositasokat mielott ujra futtatjuk a javitott patch-et.


# Segedletetek, Pelda kodok

```
app/Controller/Sample/ContentController
app/Model/Sample/Content
```
