<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class OwnerController extends Controller
{
    public function userLogin(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = (int) $request->input('per_page', 10);

        // Batasi pilihan per_page yang valid
        $allowedPerPage = [5, 10, 15, 25, 50];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString(); // pertahankan search & per_page di URL

        return view('owner.user-login', compact('users', 'search', 'perPage', 'allowedPerPage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:owner,admin,manager,staff',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'role.required'     => 'Role wajib dipilih.',
            'role.in'           => 'Role tidak valid.',
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->username . '@paralkes.local',
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('owner.user-login', [
            'search'   => $request->input('_search'),
            'per_page' => $request->input('_per_page', 10),
        ])->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return response()->json([
            'id'       => $user->id,
            'name'     => $user->name,
            'username' => $user->username,
            'role'     => $user->role,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:owner,admin,manager,staff',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan.',
            'password.min'      => 'Password minimal 6 karakter.',
            'role.required'     => 'Role wajib dipilih.',
            'role.in'           => 'Role tidak valid.',
        ]);

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
            'role'     => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('owner.user-login', [
            'search'   => $request->input('_search'),
            'per_page' => $request->input('_per_page', 10),
        ])->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('owner.user-login', [
                'search'   => $request->input('_search'),
                'per_page' => $request->input('_per_page', 10),
            ])->with('error', 'Tidak dapat menghapus akun yang sedang aktif.');
        }

        $user->delete();

        return redirect()->route('owner.user-login', [
            'search'   => $request->input('_search'),
            'per_page' => $request->input('_per_page', 10),
        ])->with('success', 'Pengguna berhasil dihapus.');
    }
}