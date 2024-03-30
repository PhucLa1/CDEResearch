<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->type ? $request->type : '';
        $email = $request->email ? $request->email : '';
        $user_id = User::where('email',$email)->first();

        $activities = Activities::where('project_id',$request->project_id)
        ->where('type','like','%'.$type.'%')->whereIn('user_id',)->with('user')->get();
    }

    /**
     * Show the form for creating a new resource.
     */

}
