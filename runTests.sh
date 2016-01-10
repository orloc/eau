#! /bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

console doctrine:database:drop --force --env=test &&
console doctrine:database:create --env=test &&
console doctrine:schema:update --force --env=test &&
console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/Test/LoadUserData.php --env=test &&
    phpunit -c $DIR/app/phpunit.xml.dist 
