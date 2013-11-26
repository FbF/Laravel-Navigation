Laravel Navigation
==================

A Laravel 4 package for adding multiple, database driven, menus to a site

## Installation

Add the following to you composer.json file

    "fbf/laravel-navigation": "dev-master"

Run

    composer update

Add the following to app/config/app.php

    'Fbf\LaravelNavigation\LaravelNavigationServiceProvider'

Publish the config

    php artisan config:publish fbf/laravel-navigation

Run the migration

    php artisan migrate --package="fbf/laravel-navigation"

Ensure the navigation `types` are set correctly in the config file

Run the seed (this will create root nodes for each of your navigation `types`)

	php artisan db:seed --class=Fbf\\LaravelNavigation\\NavItemsTableSeeder

Build your menus in the database, or if you are using FrozenNode's Laravel  Administrator, see the info below

## Usage

The package comes with a View Composer which you can attach to any view in your app. E.g.

	// app/routes.php
	View::composer('layouts.master', 'Fbf\LaravelNavigation\NavigationComposer');

## Administrator

You can use the excellent Laravel Administrator package by frozennode to administer your pages.

http://administrator.frozennode.com/docs/installation

A ready-to-use model config file for the NavItem model (navigation.php) is provided in the src/config/administrator directory of the package, which you can copy into the app/config/administrator directory (or whatever you set as the model_config_path in the administrator config file).