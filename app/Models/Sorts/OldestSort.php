<?php
namespace App\Models\Sorts;

use Illuminate\Database\Eloquent\Builder;

class OldestSort implements \Spatie\QueryBuilder\Sorts\Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        // $query->orderBy($property, $direction);
        $query->orderBy('created_at', 'ASC');
    }
}