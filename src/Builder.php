<?php

namespace RapidWeb\ImprovedPolymorphicEloquentBuilder;

use Illuminate\Database\Eloquent\Builder as OriginalBuilder;

class Builder extends OriginalBuilder
{
    /**
     * Get the "has relation" base query instance.
     *
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    protected function getHasRelationQuery($relationName)
    {
        return Relation::noConstraints(function() use ($relationName)
        {
            $relation =  $this->getModel()->$relationName();

            if( get_class($relation) == 'Illuminate\Database\Eloquent\Relations\MorphTo'){
                $parent = $relation->getParent();
                $look_ahead = $parent::first();
                $relation = $look_ahead->{$relationName}();
            };

            return $relation;
        });
    }
}