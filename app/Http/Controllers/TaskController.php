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
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taks = QueryBuilder::for(Task::class)
        ->defaultSort('created_at')
        ->allowedSorts(
            'commment',
        )
        ->allowedFilters([
            'comment', 
            AllowedFilter::exact('store_id'),
            AllowedFilter::exact('assignee_user_id'),
            AllowedFilter::exact('assigner_user_id'),
            AllowedFilter::exact('status'),
            AllowedFilter::scope('attribute')
        ])
        ->allowedIncludes([
            'assignee',
            'assigner'
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

            $taks = $taks->paginate($per_page);
            // ->appends(request()->query());

        }
        
        else $taks = $taks->get();

        $task_resource =  TaskResource::collection($taks);

        $task_resource->with['status'] = "OK";
        $task_resource->with['message'] = 'Tasks retrived successfully';

        return $task_resource;

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();
        $validated['assigner_user_id'] = auth()->user()->id;
        $task = Task::create($validated);


        $task_resource = new TaskResource($task);
        $task_resource->with['message'] = 'Task created successfully';

        return $task_resource;
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if(request()->has('include'))
        {
            foreach (explode(',', request()->include) as $key => $include) 
            {
               try {
                $task->load($include);
               } catch (\Throwable $th) {
                //throw $th;
               }
            }
        }

        if(request()->has('append'))
        {
            foreach (explode(',', request()->append) as $key => $attrs) 
            {
                if(method_exists($task, $attrs) || array_key_exists($attrs, $task->getAttributes()))
                {
                    $task->append($attrs);
                }
            }
        }

        $task_resource = new TaskResource($task);
        $task_resource->with['message'] = 'Task retrieved successfully';

        return  $task_resource;

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $validated = $request->validated();

        // streamline assigee to only update task status
        if($task->assignee_user_id == auth()->user()->id ?? null )
        {
          $validated = array_key_exists('status', $validated) ? [
                'status'=> $validated['status']
            ] : [];
        }

        $task->update($validated);
        $task_resource = new TaskResource($task);
        $task_resource->with['message'] = 'Task updated successfully';

        return $task_resource;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        $task_resource = new TaskResource(null);
        $task_resource->with['message'] = 'Task deleted successfully';

        return $task_resource;
    }
}
