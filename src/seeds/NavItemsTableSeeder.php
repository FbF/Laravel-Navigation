<?php namespace Fbf\LaravelNavigation;

class NavItemsTableSeeder extends \Seeder {

    public function run()
    {
        \DB::table('fbf_nav_items')->delete();
        $types = \Config::get('laravel-navigation::types');
        foreach ($types as $type)
        $item = NavItem::create(array(
        	'title' => $type,
        ));
    }

}