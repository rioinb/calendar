<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventInvitationController extends Controller
{
    public function store(Project $project, ProjectInvitationRequest $request)
    {
        $user = User::whereEmail(request('email'))->first();

        $project->invite($user);

        return redirect($project->path());
    }
}
