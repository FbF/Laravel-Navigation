<?php

return array(

	/**
	 * Model title
	 *
	 * @type string
	 */
	'title' => 'Navigation',

	/**
	 * The singular name of your model
	 *
	 * @type string
	 */
	'single' => 'navigation item',

	/**
	 * The class name of the Eloquent model that this config represents
	 *
	 * @type string
	 */
	'model' => 'Fbf\LaravelNavigation\NavItem',

	/**
	 * The columns array
	 *
	 * @type array
	 */
	'columns' => array(
	    'path' => array(
	        'title' => 'Path',
	    ),
	    'url' => array(
	        'title' => 'URL',
	    ),
	),

	/**
	 * The edit fields array
	 *
	 * @type array
	 */
	'edit_fields' => array(
	    'parent' => array(
	        'title' => 'Parent',
	        'type' => 'relationship',
	        'name_field' => 'path',
	        'options_sort_field' => 'lft',
	    ),
	    'title' => array(
	    	'title' => 'Title',
	    ),
	    'url' => array(
	    	'title' => 'URL',
	    ),
	    'class' => array(
	    	'title' => 'Class',
	    ),
	),

	/**
	 * The filter fields
	 *
	 * @type array
	 */
	'filters' => array(
	    'title' => array(
	        'title' => 'Title',
	    ),
	    'url' => array(
	    	'title' => 'URL',
	    ),
	),

	/**
	 * The query filter option lets you modify the query parameters before Administrator begins to construct the query. For example, if you want
	 * to have one page show only deleted items and another page show all of the items that aren't deleted, you can use the query filter to do
	 * that.
	 *
	 * @type closure
	 */
	'query_filter'=> function($query)
	{
	    $query->whereNotNull('parent_id');
	},

	/**
	 * The validation rules for the form, based on the Laravel validation class
	 *
	 * @type array
	 */
	'rules' => array(
	    'parent_id' => 'required|integer|min:1',
	    'title' => 'required',
	    'url' => 'required',
	),

	/**
	 * The sort options for a model
	 *
	 * @type array
	 */
	'sort' => array(
	    'field' => 'lft',
	    'direction' => 'asc',
	),

	/**
	 * If provided, this is run to construct the front-end link for your model
	 *
	 * @type function
	 */
	'link' => function($model)
	{
	    return $model->url;
	},
);