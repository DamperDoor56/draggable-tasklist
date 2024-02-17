<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskService {
    // Retrieves a list of tasks based on the project id
    public function list(int $projectId) {
        return Task::with('project')->where('project_id', $projectId) 
            ->orderBy('priority')->get();   // Order by priority
        }
    // Retrieves a task by it's id
    public function getById(int $id){
        return Task::where('id', $id)->with('project')->first();
    }
    // Creates a new task, calculates the priority
    // based on the number of existing tasks for the same project.
    public function store($data): void{
        $count = Task::where('project_id', $data['project_id'])->count();
        $data['priority'] = $count + 1;

        Task::create($data);
    }
    // Update an existing task
    public function update(int $id, array $data): void {
        $task = $this->getById($id);
        if(!$task) {return;}
        // If the task exists, updates its attributes with the provided data.
        $task->update($data);
    }
    // Deletes a task by its id
    public function delete(int $id): void {
        $task = $this->getById($id);

        if(!$task) { return; }
        // If the task exists, deletes it.
        $task->delete();

        // Retrieves other tasks with higher priority in the same project. 
        $tasks = Task::where('project_id', $task->project_id)
        ->where('priority', '>', $task->priority)->get();

        // If there are no other tasks with higher priority, return early
        if($tasks->isEmpty()) {
            return;
        }

        // Initialize variables for constructing SQL statements
        $when_then = "";
        $where_in = "";
        // Iterate through tasks with higher priority
        foreach($tasks as $task) {
            // Construct SQL for each task to be updated
            $when_then .= "WHEN ".$task->id
            ." THEN ".($task->priority - 1)." ";
            $where_in .= $task->id.".";
        }
        // Get the table name for the Task model
        $table_name = (new Task())->getTable();

        // Construct the bulk update SQL query
        $bulk_update_query = "UPDATE `".$table_name
        ."` SET `priority` = (CASE `id` ".$when_then."END)"
        ." WHERE `id` IN(".substr($where_in, 0, -1).");";

        // Execute the bulk update query using Laravel's DB facade
        DB::update($bulk_update_query);
        // No risk of SQL injection in this case because all 
        // the values used in constructing the query are not provided by the user
    }
    // Changes the priorities of tasks within a project
    public function reorder(int $project_id, int $start, int $end ): void {
        // Retrieve tasks for the project, ordered by priority
        $items = Task::where('project_id', $project_id)
        ->orderBy('priority')->pluck('priority', 'id')->toArray();
        
        // If the provided start or end values are out of bounds, return early
        if($start > count($items) || $end > count($items)){
            return;
        }

        // Extract task IDs and priorities into separate arrays
        $ids = [];
        $priorities = [];

        foreach ($items as $id => $priority) {
            $ids[] = $id;
            $priorities[] = $priority;
        }
        // Move the priority of the selected task to a new position
        $out_priority = array_splice($priorities, $start - 1, 1);
        array_splice($priorities, $end - 1, 0, $out_priority);

        // Initialize variables for constructing SQL querys
        $when_then = "";
        $where_in = "";
        
      // Extract task IDs and priorities into separate arrays
        $ids = array_keys($items); // Get the task IDs from the $items array
        $priorities = array_values($items); // Get the priorities from the $items array

        // Move the priority of the selected task to a new position
        $out_priority = array_splice($priorities, $start - 1, 1);
        array_splice($priorities, $end - 1, 0, $out_priority);

        // Iterate through the updated priorities
        foreach ($priorities as $out_k => $out_v) {
            // Get the task ID based on the array key
            $id = $ids[$out_k];
            // Construct SQL statements for each task to be updated
            $when_then .= "WHEN " . $id . " THEN " . ($out_k + 1) . " ";
            $where_in .= $id . ",";
        }

            // Get the table name for the Task model
            $table_name = (new Task())->getTable();
            // Construct the bulk update SQL query
            $bulk_update_query = "UPDATE `".$table_name
                ."` SET `priority` = (CASE `id` ".$when_then."END)"
                ." WHERE `id` IN(".substr($where_in, 0, -1).")"
                ." AND `deleted_at` IS NULL;"; // soft delete
                
        // Execute the bulk update query using Laravel's DB facade
            DB::update($bulk_update_query);
        }
}