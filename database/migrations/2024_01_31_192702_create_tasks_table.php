<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // drop existing foreign keys
        Schema::table('tasks', function(Blueprint $table){
            if(Schema::hasColumn('tasks','project_id')){
                $table->dropForeign(['project_id']);
            }
        });
        Schema::dropIfExists('tasks');
    }
};
