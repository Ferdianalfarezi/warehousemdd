<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('role')->latest()->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:aktif,nonaktif',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['avatar', 'password', 'password_confirmation']);
        $data['password'] = Hash::make($request->password);

        // Handle avatar upload - save directly to public/storage/users
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Ensure directory exists
            $path = public_path('storage/users');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Move file to public/storage/users
            $image->move($path, $imageName);
            $data['avatar'] = $imageName;
        }

        $user = User::create($data);
        $user->load('role');

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan!',
            'data' => $user
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role');
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:aktif,nonaktif',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except(['avatar', 'password', 'password_confirmation']);

        // Update password only if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar from public/storage/users
            if ($user->avatar) {
                $oldImagePath = public_path('storage/users/' . $user->avatar);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('avatar');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            // Ensure directory exists
            $path = public_path('storage/users');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            // Move file to public/storage/users
            $image->move($path, $imageName);
            $data['avatar'] = $imageName;
        }

        $user->update($data);
        $user->load('role');

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate!',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting own account
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa menghapus akun sendiri!'
                ], 403);
            }

            // Prevent deleting superadmin
            if ($user->isSuperAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa menghapus akun superadmin!'
                ], 403);
            }

            // Delete avatar from public/storage/users
            if ($user->avatar) {
                $imagePath = public_path('storage/users/' . $user->avatar);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user!'
            ], 500);
        }
    }
}