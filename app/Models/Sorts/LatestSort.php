<?php
namespace App\Models\Sorts;

use Illuminate\Database\Eloquent\Builder;

class LatestSort implements \Spatie\QueryBuilder\Sorts\Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'ASC' : 'DESC';

        $query->orderBy("created_at", 'DESC');
    }
}