<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Line;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private const OPERATOR_ROLE_ID = 4;
    private const JABATAN_OPTIONS  = ['Leader', 'Asst Leader'];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['role'])->latest()->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules($request));
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->except(['avatar']);
        $data = $this->applyOperatorRules($data, $request);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->storeAvatar($request->file('avatar'));
        }

        $user = User::create($data);
        $user->load(['role']);

        return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan!', 'data' => $user]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['role']);
        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), $this->rules($request, $user->id));
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->except(['avatar']);
        $data = $this->applyOperatorRules($data, $request);

        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($user->avatar);
            $data['avatar'] = $this->storeAvatar($request->file('avatar'));
        }

        $user->update($data);
        $user->load(['role']);

        return response()->json(['success' => true, 'message' => 'User berhasil diupdate!', 'data' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri!'], 403);
            }

            if ($user->isSuperAdmin()) {
                return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus akun superadmin!'], 403);
            }

            $this->deleteAvatar($user->avatar);
            $user->delete();

            return response()->json(['success' => true, 'message' => 'User berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus user!'], 500);
        }
    }

    // ==================== HELPERS ====================

    private function rules(Request $request, ?int $ignoreId = null): array
    {
        $nikUnique = $ignoreId
            ? 'unique:users,nik,' . $ignoreId
            : 'unique:users,nik';

        $isOperator = (int) $request->role_id === self::OPERATOR_ROLE_ID;

        return [
            'nama'       => 'required|string|max:255',
            'nik'        => "required|string|max:10|{$nikUnique}",
            'role_id'    => 'required|exists:roles,id',
            'status'     => 'required|in:aktif,nonaktif',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Jabatan hanya wajib/valid kalau role_id = operator
            'jabatan'    => $isOperator
                ? 'required|in:' . implode(',', self::JABATAN_OPTIONS)
                : 'nullable',
        ];
    }

    /**
     * Auto-clear jabatan kalau role_id BUKAN operator.
     */
    private function applyOperatorRules(array $data, Request $request): array
    {
        if ((int) $request->role_id !== self::OPERATOR_ROLE_ID) {
            $data['jabatan'] = null;
        }
        return $data;
    }

    private function storeAvatar($image): string
    {
        $imageName = time() . '_' . $image->getClientOriginalName();
        $path      = public_path('storage/users');
        if (!file_exists($path)) mkdir($path, 0777, true);
        $image->move($path, $imageName);
        return $imageName;
    }

    private function deleteAvatar(?string $filename): void
    {
        if (!$filename) return;
        $full = public_path('storage/users/' . $filename);
        if (file_exists($full)) unlink($full);
    }
}