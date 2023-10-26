<?php

use App\Models\Task;
use App\Models\Project;
use App\Events\TaskCreatedEvent;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

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

Route::middleware('auth')->group(function() {

    Route::get('/projects/{project}', function(Project $project) {
        $project->load('tasks');

        return view('project', compact('project'));
    });

    Route::post('/projects/{project}', function(Project $project) {
        $task = $project->tasks()->create(request(['body']));
        
        event( new TaskCreatedEvent($task) );

        return response()->json($task->body);
    })->name('project.task.create');
});