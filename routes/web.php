<?php

use App\Models\Task;
use App\Models\Project;
use App\Events\TaskCreatedEvent;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

class Order {
    public function __construct(public int $id, public int $amount) {}
}

Route::get('/', function () {
//    OrderStatusUpdated::dispatch(); //event(new OrderStatusUpdated());
   OrderStatusUpdated::dispatch(new Order(1, 599)); //event(new OrderStatusUpdated(new Order(1, 599)));

    return view('welcome');
});

Route::get('/update', function() {
    OrderStatusUpdated::dispatch(new Order(1, 599)); //event(new OrderStatusUpdated());
});

Route::get('/tasks', function() {

    $tasks = Task::latest()->pluck('body');
    
    return view('task', compact('tasks'));
});

Route::post('/tasks', function() {
    $task = Task::forceCreate(request(['body']));
    
    event(
        (new TaskCreatedEvent($task->body))->dontBroadcastToCurrentUser()
    );
    
    return response()->json($task->body);
})->name('task.create');

Route::get('/projects/{project}', function(Project $project) {
    $project->load('tasks');

    return view('project', compact('project'));
});

Route::post('/projects/{project}', function(Project $project) {
    $task = $project->tasks()->create(request(['body']));
    
    event( new TaskCreatedEvent($task) );

    return response()->json($task->body);
})->name('project.task.create');
