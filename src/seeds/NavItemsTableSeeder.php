<?php namespace Fbf\LaravelNavigation;

class NavItemsTableSeeder extends \Seeder {

    public function run()
    {
        \DB::table('fbf_nav_items')->delete();
        $types = \Config::get('laravel-navigation::types');
        $roots = array_keys($types);
        foreach ($roots as $root)
        {
	        NavItem::create(array(
	            'title' => $root,
	        ));
        }
    }

}