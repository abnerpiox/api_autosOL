<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        return User::all();
    }

    public function show(User $user){
        return $user;
    }

    public function update(Request $request, User $user){
        $user->update($request->all());

        return response()->json($user, 201);
    }

    public function delete(User $user){
        $user->delete();

        return response()->json(null, 204);
    }
}
