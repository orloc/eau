#! /bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

$DIR/console doctrine:database:drop --force --env=test &&
$DIR/console doctrine:database:create --env=test &&
$DIR/console doctrine:schema:update --force --env=test &&
$DIR/console doctrine:fixtures:load --fixtures=src/AppBundle/DataFixtures/Test/LoadUserData.php --env=test &&
    phpunit -c $DIR/app/phpunit.xml.dist 
