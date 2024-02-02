<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project_ids = Project::all()->pluck("id")->toArray();

        $now = now();
        $tasks = [];
        foreach ($project_ids as $project_id) {
            $project_priorities[$project_id] = 0;
        }

        for( $i = 0; $i < 10; $i++ ) {
            $project_id = $project_ids[array_rand($project_ids)];
            $project_priorities[$project_id]++;

            $tasks[] = [
                'project_id' => $project_id,
                'priority' => $project_priorities[$project_id],
                'title' => "Task " . $project_priorities[$project_id],
                "description"=> "Description for Task". $project_priorities[$project_id],

                'created_at' => $now,   
                'updated_at'=> $now,

            ];

            Task::insert($tasks);
        }
    }
}
