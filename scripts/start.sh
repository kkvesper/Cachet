#!/bin/bash

set -o errexit
set -o nounset

composer install

rm -rf ./bootstrap/cache/*
rm -rf ./bootstrap/cachet/*

php artisan app:update
php artisan serve --host=0.0.0.0 --port=8000
