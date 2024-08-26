<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taks = QueryBuilder::for(Task::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'name',
        )
        ->allowedFilters([
            'name', 
            AllowedFilter::scope('attribute')
        ])
        ->allowedIncludes([
            'categories',
        ]);

        $paginate = request()->has('paginate') ? request()->paginate : true; 
        $per_page = request()->has('per_page') ? request()->per_page : 15; 

        /**
         * Check if pagination is not disabled 
         */
        if(!in_array($paginate, [false, 'false', 0, '0'], true))
        {
            /** 
             * Ensure per_page is integer and >= 1 
             */
            if(!is_numeric($per_page)) $per_page = 15;
            else {
                $per_page = intval($per_page);
                $per_page = $per_page >=1? $per_page : 15 ;
            } 

            $taks = $taks->paginate($per_page)
            ->appends(request()->query());

        }
        
        else $taks = $taks->get();

        $task_resource =  TaskResource::collection($taks);

        $task_resource->with['status'] = "OK";
        $task_resource->with['message'] = 'Tasks retrived successfully';

        return $task_resource;

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
