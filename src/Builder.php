<?php

namespace RapidWeb\ImprovedPolymorphicEloquentBuilder;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder as OriginalBuilder;
use Closure;

class Builder extends OriginalBuilder
{
    /**
     * Get the "has relation" base query instance.
     *
     * @param  string  $relation
     * @param  string  $morphType
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    protected function getHasRelationQuery($relationName, $morphType = null)
    {
        return Relation::noConstraints(function() use ($relationName, $morphType)
        {
            $name = $relation;
 			$relation = $this->getModel()->$relation();
 
 			if (get_class($relation) === 'Illuminate\Database\Eloquent\Relations\MorphTo') {
 
 				$lookAhead = $relation->getParent()->where($name . '_type', '=', $morphType)->first();
 				$relation = $lookAhead->{$name}();
 
 				$relation = $relation->where($relation->getParent()->getTable() . '.' . $relation->getMorphType(), '=', $morphType);
 			};
 
 			return $relation;
        });
    }

    /**
     * Add a relationship count condition to the query.
     *
     * @param  string  $relation
     * @param  string  $operator
     * @param  int     $count
     * @param  string  $boolean
     * @param  \Closure|null  $callback
     * @param  array   $morphTypes
     * @param  bool    $morphCall
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null, $morphTypes = null, $morphCall = false)
    {
        if (strpos($relation, '.') !== false) {
            return $this->hasNested($relation, $operator, $count, $boolean, $callback);
        }

        if (!is_null($morphTypes) && $morphCall === false) {
 			return $this->hasMorphed($relation, $operator, $count, $boolean, $callback, $morphTypes);
 		}
 
 		$relation = $this->getHasRelationQuery($relation, $morphTypes);

        $query = $relation->getRelationCountQuery($relation->getRelated()->newQuery(), $this);

        if ($callback) {
            call_user_func($callback, $query);
        }

        return $this->addHasWhere($query, $relation, $operator, $count, $boolean);
    }

    /**
     * New method for handling `has` on morphTo relationships.
     *
     * @param  string  $relation
     * @param  string  $operator
     * @param  int     $count
     * @param  string  $boolean
     * @param  \Closure|null  $callback
     * @param  array   $morphTypes
     * @param  bool    $morphCall
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function hasMorphed($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null, $morphTypes = null)
 	{
 		if (!is_array($morphTypes)) {
 			$morphTypes = [$morphTypes];
 		}
 
 		return $this->where(function ($query) use ($morphTypes, $relation, $operator, $count, $callback) {
 			foreach ($morphTypes as $type) {
 				$query->has($relation, $operator, $count, 'or', $callback, $type, true);
 			}
 		}, null, null, $boolean);
 	}

    /**
     * Add a relationship count condition to the query.
     *
     * @param  string  $relation
     * @param  string  $boolean
     * @param  \Closure|null  $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function doesntHave($relation, $boolean = 'and', Closure $callback = null, $morphTypes = null) 
    {
        return $this->has($relation, '<', 1, $boolean, $callback, $morphTypes);
    }

    /**
     * Add a relationship count condition to the query with where clauses.
     *
     * @param  string    $relation
     * @param  \Closure  $callback
     * @param  string    $operator
     * @param  int       $count
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function whereHas($relation, Closure $callback, $operator = '>=', $count = 1, $morphTypes = null)
    {
        return $this->has($relation, $operator, $count, 'and', $callback, $morphTypes);
    }

    /**
     * Add a relationship count condition to the query with an "or".
     *
     * @param  string  $relation
     * @param  string  $operator
     * @param  int     $count
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orHas($relation, $operator = '>=', $count = 1, $morphTypes = null)
    {
        return $this->has($relation, $operator, $count, 'or', $morphTypes);
    }

    /**
     * Add a relationship count condition to the query with where clauses and an "or".
     *
     * @param  string    $relation
     * @param  \Closure  $callback
     * @param  string    $operator
     * @param  int       $count
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orWhereHas($relation, Closure $callback, $operator = '>=', $count = 1, $morphTypes = null)
    {
        return $this->has($relation, $operator, $count, 'or', $callback, $morphTypes);
    }

}