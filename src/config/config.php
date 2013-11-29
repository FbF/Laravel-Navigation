<?php namespace Fbf\LaravelNavigation;

return array(

	/**
	 * The types of navigation in your site, e.g. main, header, footer
	 */
	'types' => array(
		// This "type" has "subtypes", meaning that this branch of the hierarchy actually ends up in multiple menus on the site
		'Main' => array(
			// This might be used for a single level, horizontal menu spanning the width of the site, that appears on every page
			'Primary' => array(
				// max_depth of 1 means only go down 1 level from this Navigation type's root, i.e. 1 level from the root node called "Main"
				'max_depth' => 1,
				// NavigationComposer::SHOW_ALWAYS means generate this menu on every page
				'show' => NavigationComposer::SHOW_ALWAYS,
			),
			// This might be used for a hierarchical / nested menu that might appear in the sidebar, that only shows the pages under the current selected primary navigation item
			'Section' => array(
				// This means we're going to start from pages under the current selected primary navigation item
				'from_depth' => 2,
				// NavigationComposer::SHOW_IF_CURRENT_ROUTE_IN_SECTION means that we should only generate this menu if the current route is under the current selected primary navigation item
				'show' => NavigationComposer::SHOW_IF_CURRENT_ROUTE_IN_SECTION,
				// NavigationComposer::SHOW_CHILDREN_IF_PARENT_OF_CURRENT_ROUTE means that we should only include children nodes under a particular node if the current node is for the current route
				'show_children' => NavigationComposer::SHOW_CHILDREN_IF_PARENT_OF_CURRENT_ROUTE,
			),
			// This might be used in a sitemap-style, footer bar across the bottom of every page that lists links to the main content pages in a section. You probably don't want all primary nav items
			// in this area, so rather than make 1 sub type that goes from depth 1 to depth 2, and include things like the homepage etc, it may be better to generate multiple sitemap-style footer
			// sections and output them manually.
			'Section1' => array(
				// This means we're going to start from pages that are at depth 2
				'from_depth' => 2,
				// NavigationComposer::SHOW_ALWAYS means generate this menu on every page
				'show' => NavigationComposer::SHOW_ALWAYS,
				// This identifies the root node of a section that we want to generate the sitemap-style, footer for.
				'from_item_id' => 4,
				// This means we're going to end at pages that are at depth 2
				'max_depth' => 2,
			),
			// This might be used in a sitemap-style, footer bar across the bottom of every page that lists links to the main content pages in a section
			'Section2' => array(
				// This means we're going to start from pages that are at depth 2
				'from_depth' => 2,
				// NavigationComposer::SHOW_ALWAYS means generate this menu on every page
				'show' => NavigationComposer::SHOW_ALWAYS,
				'from_item_id' => 5,
				// This means we're going to end at pages that are at depth 2
				'max_depth' => 2,
			),
		),
		// This might be used for things like the links to "About Us", "FAQs" etc, that appears on every page
		'Header' => array(
			'max_depth' => 1,
			// NavigationComposer::SHOW_ALWAYS means generate this menu on every page
			'show' => NavigationComposer::SHOW_ALWAYS,
		),
		// This might be used for things like the links to "Privacy policy", "Terms & Conditions" etc, that appears on every page
		'Footer' => array(
			'max_depth' => 1,
			'show' => NavigationComposer::SHOW_ALWAYS, // NavigationComposer::SHOW_ALWAYS means generate this menu on every page
		),
	),

);