Laravel Navigation
==================

A Laravel 4 package for adding multiple, database driven, hierarchical menus to a site

## Features

Comes with:

* Migration for the 'nav_items' table (includes fields for adding custom CSS class to an item, and 'descendant routes',
which allow you to add route patterns which trigger things like the active_child class if the current route is
effectively be a child of the current item, but there is no node in the database for the current route, e.g. you have a
nav item called 'Blog' (which is routed through to your BlogController@index method) but not one for each individual
Blog Post. Adding the route for the BlogPostController@view method to this field, e.g. 'blog/{slug}' will ensure the
Blog menu item has the active child class, when you are viewing the Blog Post)
* NavItem Model (that extends Baum/Node)
* View Composer for generating all the menus
* Sample FrozenNode/Administrator config file for managing the nav_items

Menus can be traditional nested list style (e.g. multiple nested `ul` and `li` tags), or drop downs (`select` and
`option` tags, where the value in child node `option` tags are prefixed with a string e.g. spaces or '..' repeated
according to the node's depth).

Once you've set up the hierarchy in the database, you can configure multiple menus to be generated from that single
hierarchy. Different menus can include the same nodes, but used in different pages of your site, or different places on
the same pages.

For each menu, you can control

* The minimum and maximum (or no maximum) depth of items within that branch of the hierarchy to include in the menu
* Whether to show children:
** always (useful for always expanded, or fly-out/dynamic/JavaScript menus for example, consider using in combination with the `max_depth` option)
** or only when the current item is in that branch of the hierarchy, or it has children (useful for sidebar, section based navigation)

Menu items automatically include useful CSS classes to achieve common styling enhancements, such as:

* First item in the level
* Last item in the level
* Currently active item
* A child of this item is currently active
* Item has children

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

Ensure the navigation `types` are set correctly in the config file. See the config file for comprehensive examples

Run the seed (this will create root nodes for each of your navigation `types`)

	php artisan db:seed --class="Fbf\LaravelNavigation\NavItemsTableSeeder"

Build your menus in the database, or if you are using FrozenNode's Laravel Administrator, see the info below

## Usage

The package comes with a View Composer which you can attach to any view in your app. E.g.

	// app/routes.php
	View::composer('layouts.master', 'Fbf\LaravelNavigation\NavigationComposer');

This is responsible for generating your menu data.

Now to render the menus, you just need to do the following in your view:

	{{ $MainNavigation }}

This will render the 'Main' menu. If you had configured another menu called 'Footer', you would render this by adding the following to your view:

	{{ $FooterNavigation }}

Basically, whatever `types` you set up in the config file, that type's menu is in a view variable called "< type >Navigation".

If you need to a output a menu in a view file but you've attached the composer to a layout, e.g. you want to render a sidebar
menu inside the pages.view view file, the $SidebarNavigation variable won't be available since the composer won't have executed
yet, it runs when the master layout is rendered, which happens after your view. In this case, just attach the composer to the
pages.view view as well as the layout. The view composer won't create them again. E.g.

```php
View::composer(array(
	'layouts.master',
	'laravel-pages::page',
), 'Fbf\LaravelNavigation\NavigationComposer');
```

## Configuration

A sample config file is supplied which you can amend, after publishing into your app, to suit your app's navigation requirements.

In addition to the options listed there, you can also pass in any of the following, for each menu:

```php
	protected $defaultOptions = array(
		'from_depth' => 1,
		'from_item_id' => null,
		'max_depth' => null,
		'root_element' => 'div',
		'root_element_class' => 'menu',
		'root_element_type_class_prefix' => 'menu--',
		'list_element' => 'ul',
		'root_list_class' => '',
		'current_item_content_element' => 'span',
		'item_content_depth_prefix' => '..',
		'item_element' => 'li',
		'item_class' => '',
		'active_child_class' => 'menu--item__selected-child',
		'active_class' => 'menu--item__selected',
		'first_class' => 'menu--item__first',
		'last_class' => 'menu--item__last',
		'has_children_class' => 'menu--item__has-children',
	);
```

## Administrator

You can use the excellent Laravel Administrator package by frozennode to administer your pages.

http://administrator.frozennode.com/docs/installation

A ready-to-use model config file for the `NavItem` model (`navigation.php`), including custom actions to reorder nodes in
the hierarchy, is provided in the `src/config/administrator` directory of the package, which you can copy into the
`app/config/administrator` directory (or whatever you set as the `model_config_path` in the administrator config file).
