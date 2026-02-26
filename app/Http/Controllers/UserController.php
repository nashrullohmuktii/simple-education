<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return ResponseHelper::success(UserResource::collection($users), 'Users retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $roleName = $data['role'] ?? 'user';
        unset($data['role']);

        $user = User::create($data);
        $user->assignRole(Role::findByName($roleName, 'web'));

        return ResponseHelper::success(new UserResource($user->load('roles')), 'User created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return ResponseHelper::success(new UserResource($user->load('roles')), 'User retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if (isset($data['role'])) {
            $user->syncRoles([Role::findByName($data['role'], 'web')]);
            unset($data['role']);
        }

        $user->update($data);

        return ResponseHelper::success(new UserResource($user->load('roles')), 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return ResponseHelper::success([], 'User deleted successfully');
    }
}
