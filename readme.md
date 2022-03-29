# MTAA api

## Autori
- Norbert Vígh
- Thomas Višvader
## Poziadavky
`PHP>=8.0`

`composer`
https://getcomposer.org/doc/00-intro.md

`symfony` (optional)
https://symfony.com/

## Konfiguracia aplikacie
1. Skopirovat subor `.env.local.dist` na `.env.local`.
1. Vyplnit v subore `.env.local` vsetky parametre na pripojenie k DB.
1. Nainstalovanie dodatocnych suborov pomocou prikazu `composer install`.
1. Spustenie aplikacie na localhost:
   - prikazom `php -S localhost:8000 -t public/`
   - alebo prikazom `symfony server:start`
1. Aplikacia je dostupna na `localhost:8000`

## Migracie
Migracia sa manualne spusta prikazom `php bin/console doctrine:migrations:migrate`. 

Prikaz vykona vsetky migracie ktore neboli este vykonane. 
Prehlad aktualneho stavu migracii je dostupny cez prikaz `php bin/console doctrine:migrations:status`. 