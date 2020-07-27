#!/bin/sh

echo "Creating the Symfony skeleton project..."
composer create-project symfony/skeleton FunctionalTest

echo "Creating a local git repository..."
cd FunctionalTest || exit 1
git init

echo "Installing Doctrine ORM support and data fixtures..."
composer require symfony/orm-pack
composer require --dev doctrine/doctrine-fixtures-bundle

echo "Installing testing bundles..."
composer require --dev symfony/phpunit-bridge
composer require --dev symfony/browser-kit symfony/css-selector
composer require --dev hautelook/alice-bundle

echo "Installing profiler pack..."
composer require --dev symfony/profiler-pack

echo "Installing maker bundle..."
composer require --dev symfony/maker-bundle

echo "Installing monolog..."
composer require symfony/monolog-bundle
