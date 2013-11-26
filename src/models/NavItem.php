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
            $oldNavItem = self::where('id','=',$navItem->id)->first();
            $oldParent = $oldNavItem->parent;
            $oldParentId = $oldParent->id;
            $navItem->oldParentId = $oldParentId;
        });

        static::updated(function ($navItem) {
            if ($navItem->oldParentId != $navItem->parent->id)
            {
                $newParent = $navItem->parent;
                $navItem->makeChildOf($newParent);
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