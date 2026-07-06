<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Line;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $users = User::with(['role', 'lines'])->latest()->get();
        $roles = Role::all();
        $lines = Line::orderBy('nama_line')->get();
        return view('users.index', compact('users', 'roles', 'lines'));
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

        $data = $request->except(['avatar', 'password', 'password_confirmation', 'lines']);
        $data['password'] = Hash::make($request->password);
        $data = $this->applyOperatorRules($data, $request);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->storeAvatar($request->file('avatar'));
        }

        $user = User::create($data);
        $this->syncLines($user, $request);
        $user->load(['role', 'lines']);

        return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan!', 'data' => $user]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['role', 'lines']);
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

        $data = $request->except(['avatar', 'password', 'password_confirmation', 'lines']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $data = $this->applyOperatorRules($data, $request);

        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($user->avatar);
            $data['avatar'] = $this->storeAvatar($request->file('avatar'));
        }

        $user->update($data);
        $this->syncLines($user, $request);
        $user->load(['role', 'lines']);

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
            $user->lines()->detach();
            $user->delete();

            return response()->json(['success' => true, 'message' => 'User berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus user!'], 500);
        }
    }

    // ==================== HELPERS ====================

    private function rules(Request $request, ?int $ignoreId = null): array
    {
        $usernameUnique = $ignoreId
            ? 'unique:users,username,' . $ignoreId
            : 'unique:users,username';

        $isOperator = (int) $request->role_id === self::OPERATOR_ROLE_ID;

        return [
            'username'   => "required|string|max:255|{$usernameUnique}",
            'password'   => $ignoreId ? 'nullable|string|min:6|confirmed' : 'required|string|min:6|confirmed',
            'role_id'    => 'required|exists:roles,id',
            'status'     => 'required|in:aktif,nonaktif',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Jabatan & Line hanya wajib/valid kalau role_id = operator
            'jabatan'    => $isOperator
                ? 'required|in:' . implode(',', self::JABATAN_OPTIONS)
                : 'nullable',
            'lines'      => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) use ($request, $isOperator) {
                    if (!$isOperator) return;
                    $max = User::maxLinesForJabatan($request->jabatan);
                    if ($max !== null && count($value ?? []) > $max) {
                        $fail("Jabatan {$request->jabatan} maksimal {$max} line.");
                    }
                },
            ],
            'lines.*'    => 'exists:lines,id',
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

    /**
     * Sync relasi lines. Kalau bukan operator, lines otomatis dikosongkan.
     */
    private function syncLines(User $user, Request $request): void
    {
        if ((int) $request->role_id !== self::OPERATOR_ROLE_ID) {
            $user->lines()->sync([]);
            return;
        }

        $user->lines()->sync($request->lines ?? []);
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