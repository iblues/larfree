<?php


namespace Larfree\Models\Traits;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * $users = User::orderByRelationship('user_type', function($q){
 *   return $q->orderBy('name', 'desc');
 *  })
 *  $users = User::orderByRelationship('user.name','desc')
 * Trait OrderByRelationship
 * @package Larfree\Models\Traits
 */
trait OrderByRelationship
{

    /**
     * @param $query
     * @param string $relationship = user.name
     * @param string $sort = desc
     * @throws \Exception
     * @author Blues
     *
     */
    public function scopeOrderByRelationship($query, $relationship, $key, $sort = 'desc')
    {

        if ($query->getModel()->relationLoaded($relationship)) {

            $model = $this->$relationship();
            if ($model instanceof HasOne) {
                $parent_column = $model->getLocalKeyName();
                $child_column = $model->getForeignKeyName();
            } elseif ($model instanceof BelongsTo) {
                $parent_column = $model->getForeignKeyName();
                $child_column = $model->getOwnerKeyName();
            } else {
                throw new \Exception('Relationship must be HasOne or BelongsTo');
            }

            $query->orderBy(function ($query) use ($model, $key, $parent_column, $child_column) {
                $query->select($key)
                    ->from($model->getModel()->getTable())
                    ->whereColumn($this->qualifyColumn($parent_column), $model->qualifyColumn($child_column))
                    ->limit(1);
            }, $sort);
        } else {
            throw new \Exception('Relationship not found');
        }
    }

}