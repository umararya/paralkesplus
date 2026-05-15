<?php
// app/Http/Controllers/OwnerController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    protected array $allowedPerPage = [5, 10, 25, 50];

    public function userLogin(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = in_array($request->input('per_page'), $this->allowedPerPage)
                   ? (int) $request->input('per_page')
                   : 10;

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('created_at', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('owner.user-login', compact('users', 'search', 'perPage'))
            ->with('allowedPerPage', $this->allowedPerPage);
    }

    public function userLoginStore(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:owner,admin,manager,staff',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        $search  = $request->input('_search', '');
        $perPage = $request->input('_per_page', 10);

        return redirect()
            ->route('owner.user-login', array_filter(['search' => $search, 'per_page' => $perPage]))
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function userLoginUpdate(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => ['required','string','max:50', Rule::unique('users','username')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:owner,admin,manager,staff',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        $search  = $request->input('_search', '');
        $perPage = $request->input('_per_page', 10);

        return redirect()
            ->route('owner.user-login', array_filter(['search' => $search, 'per_page' => $perPage]))
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function userLoginDestroy(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('owner.user-login')
                ->with('error', 'Kamu tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        $search  = $request->input('_search', '');
        $perPage = $request->input('_per_page', 10);

        return redirect()
            ->route('owner.user-login', array_filter(['search' => $search, 'per_page' => $perPage]))
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}