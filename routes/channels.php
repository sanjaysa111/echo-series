<?php

use App\Models\Project;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('tasks.{project}', function ($user, Project $project) {

    return $project->participants->contains($user);

    // $canAccess = [];

    // if($user->email === 'sanjay@echo.com') {
    //     $canAccess = [1, 3];
    // }

    // if($user->email === 'parvti@echo.com') {
    //     $canAccess = [2, 4];
    // }

    // return in_array($projectId, $canAccess);
});
