<?php namespace Fbf\LaravelNavigation;

return array(

	/**
	 * The types of navigation in your site, e.g. main, header, footer
	 */
	'types' => array(
		'Main' => array(
			'Primary' => array(
				'max_depth' => 1,
				'show' => NavigationComposer::SHOW_ALWAYS,
			),
			'Section' => array(
				'from_depth' => 2,
				'show' => NavigationComposer::SHOW_IF_CURRENT_ROUTE_IN_SECTION,
				'show_children' => NavigationComposer::SHOW_CHILDREN_IF_PARENT_OF_CURRENT_ROUTE,
			),
		),
		'Header' => array(
			'max_depth' => 1,
			'show' => NavigationComposer::SHOW_ALWAYS,
		),
		'Footer' => array(
			'max_depth' => 1,
			'show' => NavigationComposer::SHOW_ALWAYS,
		),
	),

);