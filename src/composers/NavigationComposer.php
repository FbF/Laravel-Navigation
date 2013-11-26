<?php namespace Fbf\LaravelNavigation;

// use NavItem;

class NavigationComposer {

 	public function compose($view)
	{
		$types = \Config::get('laravel-navigation::types');
		foreach ($types as $type)
		{
			$items = NavItem::whereNull('parent_id')->where('title','=',$type)->first()->getImmediateDescendants();
			$navigation = \Menu::handler($type, array('class' => \Str::slug($type . ' Navigation')));
			$list = $this->buildLevel($items);
			$navigation->attach($list);
			$view->with($type.'Navigation', $navigation);
			return;
		}
	}

	protected function buildLevel($items)
	{
		$list = \Menu::items();
		foreach ($items as $item) {
			$children = $item->getImmediateDescendants();
			if (!$children->isEmpty())
			{
				$list->add($item->url, $item->title, $this->buildLevel($children));
			}
			else
			{
				$list->add($item->url, $item->title);
			}
		}
		return $list;
	}

}