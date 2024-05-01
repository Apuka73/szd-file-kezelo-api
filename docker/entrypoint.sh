#!/bin/sh
composer install
phinx migrate
apache2-foreground
