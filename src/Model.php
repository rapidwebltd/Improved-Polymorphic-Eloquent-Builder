<?php

namespace RapidWeb\ImprovedPolymorphicEloquentBuilder;

use Illuminate\Database\Eloquent\Model as OriginalModel;

abstract class Model extends OriginalModel
{
    /**
     * Overrides the default Eloquent query builder
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new \RapidWeb\ImprovedPolymorphicEloquentBuilder\Builder($query);
    }
}