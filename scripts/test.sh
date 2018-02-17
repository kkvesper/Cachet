#!/bin/bash

set -o errexit
set -o nounset

composer install

./vendor/bin/phpunit
