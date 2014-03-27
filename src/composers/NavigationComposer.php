<?php namespace Fbf\LaravelNavigation;

class NavigationComposer {

	const SHOW_IF_CURRENT_ROUTE_IN_SECTION = 1;
	const SHOW_ALWAYS = 2;
	const SHOW_CHILDREN_ALWAYS = 3;
	const SHOW_CHILDREN_IF_PARENT_OF_CURRENT_ROUTE = 4;

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
		'empty_item_value' => 'Please select',
	);

	protected $options = array();

	protected $type = null;
	protected $subtype = null;

	protected $effectiveRouteNode = null;
	protected $currentRouteNode = null;
	protected $currentRouteAncestorNode = null;

	protected static $done = array();

	public function compose($view)
	{
		$this->currentRouteNode = $this->effectiveRouteNode = $this->getNodeForCurrentRoute();
		if (!$this->currentRouteNode)
		{
			$this->currentRouteAncestorNode = $this->effectiveRouteNode = $this->getAncestorNodeForCurrentRoute();
		}
		$types = \Config::get('laravel-navigation::types');
		foreach ($types as $type => $subtypes)
		{
			if (!is_array(current($subtypes)))
			{
				$subtypes = array($type => $subtypes);
			}
			foreach ($subtypes as $subtype => $options)
			{
				// Skip this subtype if we've done it already. Could have happened if we've attached the composer to
				// more than one view file, e.g. a partial and a view and a layout.
				if (in_array($subtype, self::$done))
				{
					continue;
				}
				$this->options = array_merge($this->defaultOptions, $options);
				$this->type = $type;
				$this->subtype = $subtype;
				if ($this->options['show'] == self::SHOW_ALWAYS || ($this->options['show'] == self::SHOW_IF_CURRENT_ROUTE_IN_SECTION && $this->currentRouteInSection()))
				{
					$navigation = $this->getNavigation();
					self::$done[] = $subtype;
					$view->with($subtype.'Navigation', $navigation);
				}
			}
		}
	}

	protected function getNodeForCurrentRoute()
	{
		$currentUri = \Request::path();
		if ($navItem = NavItem::where('uri','=',$currentUri)->orWhere('uri','=','/'.$currentUri)->first())
		{
			return $navItem;
		}
		return null;
	}

	protected function getAncestorNodeForCurrentRoute()
	{
		$currentRoute = \Route::current()->getUri();
		$currentRoute = preg_replace('/^(get|post|put|delete) /i', '', $currentRoute);
		if ($navItem = NavItem::where('descendants_routes','LIKE','%'.$currentRoute.'%')->first())
		{
			return $navItem;
		}
		return null;
	}

	protected function currentRouteInSection()
	{
		if (is_null($this->effectiveRouteNode))
		{
			return false;
		}
		$rootNavItem = NavItem::whereNull('parent_id')->where('title','=',$this->type)->first();
		if (!$this->effectiveRouteNode->isDescendantOf($rootNavItem))
		{
			return false;
		}
		$depth = $this->effectiveRouteNode->getLevel();
		if (!is_null($this->options['max_depth']) && $depth > $this->options['max_depth'])
		{
			return false;
		}
		return true;
	}

	protected function getNavigation()
	{
		if (!is_null($this->options['from_item_id']))
		{
			$navigationRootNode = NavItem::where('id','=',$this->options['from_item_id'])->first();
		}
		elseif ($this->options['from_depth'] > 1 && !is_null($this->effectiveRouteNode))
		{
			$navigationRootNode = $this->effectiveRouteNode->ancestorsAndSelf()->where('depth','=',($this->options['from_depth']-1))->first();
		}
		else
		{
			$navigationRootNode = NavItem::roots()->where('title','=',$this->type)->first();
		}
		$firstLevelNavItems = $navigationRootNode->getImmediateDescendants();
		$navigation = $this->makeNavigation($firstLevelNavItems);
		return $navigation;
	}

	protected function makeNavigation($navItems, $level = 0)
	{
		if ($navItems->isEmpty())
		{
			return;
		}
		$depth = current(current($navItems))->getLevel();
		if ($depth < $this->options['from_depth']
			|| (!is_null($this->options['max_depth'])
				&& $depth > $this->options['max_depth']))
		{
			return;
		}
		$navigation = $this->openList($level);
		$numItemsInLevel = count($navItems);
		for ($i = 1; $i <= $numItemsInLevel; $i++) {
			$navItem = $navItems[$i-1];
			$navigation .= $this->openItem($navItem, $i, $numItemsInLevel, $level);
			if ((is_null($this->options['max_depth'])
					|| $depth < $this->options['max_depth'])
				&& ($this->options['show_children'] == self::SHOW_CHILDREN_ALWAYS
					|| ($this->options['show_children'] == self::SHOW_CHILDREN_IF_PARENT_OF_CURRENT_ROUTE
						&& !is_null($this->effectiveRouteNode)
						&& ($navItem->isSelfOrAncestorOf($this->effectiveRouteNode)))))
			{
				$childrenNavItems = $navItem->getImmediateDescendants();
				if (!$childrenNavItems->isEmpty())
				{
					$navigation .= $this->makeNavigation($childrenNavItems, $level+1);
				}
			}
			$navigation .= $this->closeItem();
		}
		$navigation .= $this->closeList($level);
		return $navigation;
	}

	protected function openList($level)
	{
		if ($this->options['list_element'] == 'select' && $level > 0)
		{
			return;
		}
		$return = '';
		if ($level == 0 && !empty($this->options['root_element']))
		{
			$return .= '<' . $this->options['root_element'];
			$classes = array();
			if (!empty($this->options['root_element_class']))
			{
				$classes[] = $this->options['root_element_class'];
			}
			$classes[] = $this->options['root_element_type_class_prefix'] . \Str::slug($this->subtype);
			$return .= ' class="' . implode(' ', $classes) . '">';
		}
		$return .= '<' . $this->options['list_element'];
		$classes = array();
		if ($level == 0)
		{
			if (!empty($this->options['root_list_class']))
			{
				$classes[] = $this->options['root_list_class'];
			}
			if (empty($this->options['root_element']))
			{
				if (!empty($this->options['root_element_class']))
				{
					$classes[] = $this->options['root_element_class'];
				}
				$classes[] = $this->options['root_element_type_class_prefix'] . \Str::slug($this->subtype);
			}
		}
		if (!empty($classes))
		{
			$return .= ' class="' . implode(' ', $classes) . '"';
		}
		$return .= '>';
		if ($this->options['list_element'] == 'select' && $level == 0 && !empty($this->options['empty_item_value']))
		{
			$return .= '<option>'.$this->options['empty_item_value'].'</option>';
		}
		return $return;
	}

	protected function closeList($level)
	{
		if ($this->options['list_element'] == 'select' && $level > 0)
		{
			return;
		}
		$return = '</' . $this->options['list_element'] . '>';
		if ($level == 0 && !empty($this->options['root_element']))
		{
			$return .= '</' . $this->options['root_element'] . '>';
		}
		return $return;
	}

	/**
	 * Returns the opening tag for an item, with relevant classes:
	 *
	 * 'active_child_class'
	 * 'active_class'
	 * 'first_class'
	 * 'last_class'
	 * 'has_children_class'
	 *
	 * @param $navItem
	 * @param $pos
	 * @param $last
	 * @param $level
	 * @internal param $level
	 * @return string
	 */
	protected function openItem($navItem, $pos, $last, $level)
	{
		$return = '<' . $this->options['item_element'];
		if ($this->options['item_element'] == 'option')
		{
			$return .= ' value="' . $this->itemUri($navItem) . '"';
			if ($this->isCurrent($navItem))
			{
				$return .= ' selected="selected"';
			}
		}
		else
		{
			$classes = array();
			if ($pos == 1 && !empty($this->options['first_class']))
			{
				$classes[] = $this->options['first_class'];
			}
			if ($pos == $last && !empty($this->options['last_class']))
			{
				$classes[] = $this->options['last_class'];
			}
			if (!is_null($this->effectiveRouteNode))
			{
				if (!is_null($this->currentRouteNode) && !empty($this->options['active_class']) && $navItem->equals($this->currentRouteNode))
				{
					$classes[] = $this->options['active_class'];
				}
				elseif (!is_null($this->currentRouteNode) && !empty($this->options['active_child_class']) && $navItem->isAncestorOf($this->currentRouteNode))
				{
					$classes[] = $this->options['active_child_class'];
				}
				elseif (!is_null($this->currentRouteAncestorNode) && !empty($this->options['active_child_class']) && ($navItem->isAncestorOf($this->currentRouteAncestorNode) || $navItem->equals($this->currentRouteAncestorNode)))
				{
					$classes[] = $this->options['active_child_class'];
				}
			}
			if (!empty($this->options['has_children_class']) && !$navItem->isLeaf())
			{
				$classes[] = $this->options['has_children_class'];
			}
			if (!empty($classes))
			{
				$return .= ' class="' . implode(' ', $classes) . '"';
			}
		}
		$return .= '>';
		$return .= $this->itemContent($navItem, $level);
		if ($this->options['item_element'] == 'option')
		{
			$return .= '</' . $this->options['item_element'] . '>';
		}
		return $return;
	}

	protected function closeItem()
	{
		if ($this->options['item_element'] == 'option')
		{
			return;
		}
		$return = '</' . $this->options['item_element'] . '>';
		return $return;
	}

	protected function itemContent($navItem, $level)
	{
		if ($this->options['item_element'] == 'option')
		{
			return $this->itemContentOption($navItem, $level);
		}
		return $this->itemContentLink($navItem);
	}

	protected function itemUri($navItem)
	{
		$uri = $navItem->uri;
		if (substr($uri, 0, 1) != '/' && !filter_var($uri, FILTER_VALIDATE_URL))
		{
			$uri = '/' . $uri;
		}
		return $uri;
	}

	protected function isCurrent($navItem)
	{
		return !is_null($this->currentRouteNode) && $navItem->equals($this->currentRouteNode);
	}

	protected function itemContentLink($navItem)
	{
		if ($this->isCurrent($navItem))
		{
			return '<' . $this->options['current_item_content_element'] . '>' . $navItem->title . '</' . $this->options['current_item_content_element'] . '>';
		}
		return '<a href="' . $this->itemUri($navItem) . '" title="' . $navItem->title . '">' . $navItem->title . '</a>';
	}

	protected function itemContentOption($navItem, $level)
	{
		$title = $navItem->title;
		if (!empty($this->options['item_content_depth_prefix']))
		{
			$title = str_repeat($this->options['item_content_depth_prefix'], $level) . $title;
		}
		return $title;
	}

}