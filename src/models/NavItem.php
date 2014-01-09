<?php namespace Fbf\LaravelNavigation;

use \Baum\Node;

class NavItem extends Node {

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'fbf_nav_items';

    /**
     * Stores the old parent id before editing
     * @var integer
     */
    protected $oldParentId = null;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot() {

        parent::boot();

	    static::updating(function ($navItem) {
		    $dirty = $navItem->getDirty();
            $oldNavItem = self::where('id','=',$navItem->id)->first();
            $oldParent = $oldNavItem->parent;
            $oldParentId = $oldParent->id;
		    if ( isset($dirty[$navItem->getParentColumnName()]) && $dirty[$navItem->getParentColumnName()] == $oldParentId )
		    {
			    unset($navItem->{$navItem->getParentColumnName()});
			    static::$moveToNewParentId = FALSE;
		    }
	    });

    }

    public function getPathAttribute($value)
    {
        $ancestors = $this->getAncestors();
        $return = array();
        foreach($ancestors as $ancestor) {
            $return[] = $ancestor->title;
        }
        $return[] = $this->title;
        return implode(' > ', $return);
    }

}