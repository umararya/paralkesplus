<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\UserLoginLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    protected array $allowedPerPage = [5, 10, 25, 50];

    // ══════════════════════════════════════════
    // USER LOGIN (Manajemen Akun)
    // ══════════════════════════════════════════

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
        $user = User::create($validated);

        // Catat activity log
        ActivityLog::record(
            module:   'User Login',
            action:   'create',
            subject:  $user->name . ' (@' . $user->username . ')',
            newValue: ['name' => $user->name, 'username' => $user->username, 'role' => $user->role],
            pageUrl:  'owner/user-login'
        );

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

        // Ambil data lama sebelum update
        $oldData = ['name' => $user->name, 'username' => $user->username, 'role' => $user->role];

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        // Catat activity log
        $newData = ['name' => $user->name, 'username' => $user->username, 'role' => $user->role];
        ActivityLog::record(
            module:   'User Login',
            action:   'update',
            subject:  $user->name . ' (@' . $user->username . ')',
            oldValue: $oldData,
            newValue: $newData,
            pageUrl:  'owner/user-login'
        );

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

        // Catat activity log sebelum hapus
        ActivityLog::record(
            module:   'User Login',
            action:   'delete',
            subject:  $user->name . ' (@' . $user->username . ')',
            oldValue: ['name' => $user->name, 'username' => $user->username, 'role' => $user->role],
            pageUrl:  'owner/user-login'
        );

        $user->delete();

        $search  = $request->input('_search', '');
        $perPage = $request->input('_per_page', 10);

        return redirect()
            ->route('owner.user-login', array_filter(['search' => $search, 'per_page' => $perPage]))
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    // ══════════════════════════════════════════
    // MONITORING USER (Halaman Baru)
    // ══════════════════════════════════════════

    public function monitoringUser(Request $request)
    {
        return view('owner.monitoring-user');
    }

    // ══════════════════════════════════════════
    // MONITOR AKTIVITAS PENGGUNA
    // ══════════════════════════════════════════

    public function monitor(Request $request)
    {
        $search    = $request->input('search', '');
        $roleFilter = $request->input('role', '');
        $dateFrom  = $request->input('date_from', Carbon::today()->toDateString());
        $dateTo    = $request->input('date_to', Carbon::today()->toDateString());
        $tab       = $request->input('tab', 'sesi'); // sesi | aktivitas
        $perPage   = in_array($request->input('per_page'), $this->allowedPerPage)
                     ? (int) $request->input('per_page') : 10;

        // ── Summary Stats ──
        $today = Carbon::today();
        $totalOnline      = UserLoginLog::whereNull('logout_at')
                                ->whereDate('login_at', $today)->count();
        $totalLoginHariIni = UserLoginLog::whereDate('login_at', $today)->count();
        $totalAktivitasHariIni = ActivityLog::whereDate('created_at', $today)->count();
        $userTidakAktif   = User::where('role', '!=', 'owner')
                                ->where(function ($q) {
                                    $q->whereNull('last_login_at')
                                      ->orWhere('last_login_at', '<', Carbon::now()->subDays(7));
                                })->count();

        // ── Tabel Sesi Login/Logout ──
        $loginLogs = UserLoginLog::with('user')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter, function ($q) use ($roleFilter) {
                $q->whereHas('user', fn($uq) => $uq->where('role', $roleFilter));
            })
            ->when($dateFrom, fn($q) => $q->whereDate('login_at', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('login_at', '<=', $dateTo))
            ->orderByDesc('login_at')
            ->paginate($perPage, ['*'], 'sesi_page')
            ->withQueryString();

        // ── Tabel Activity Log ──
        $activityLogs = ActivityLog::with('user')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('username', 'like', "%{$search}%");
                })->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            })
            ->when($roleFilter, function ($q) use ($roleFilter) {
                $q->whereHas('user', fn($uq) => $uq->where('role', $roleFilter));
            })
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'aktv_page')
            ->withQueryString();

        return view('owner.monitor', compact(
            'loginLogs', 'activityLogs',
            'search', 'roleFilter', 'dateFrom', 'dateTo', 'tab', 'perPage',
            'totalOnline', 'totalLoginHariIni', 'totalAktivitasHariIni', 'userTidakAktif'
        ))->with('allowedPerPage', $this->allowedPerPage);
    }

    /**
     * Detail timeline aktivitas satu user
     */
    public function monitorDetail(Request $request, int $userId)
    {
        $user = User::findOrFail($userId);

        $dateFrom = $request->input('date_from', Carbon::today()->toDateString());
        $dateTo   = $request->input('date_to', Carbon::today()->toDateString());
        $perPage  = in_array($request->input('per_page'), $this->allowedPerPage)
                    ? (int) $request->input('per_page') : 25;

        // Sesi login pada rentang tanggal
        $sesiList = UserLoginLog::where('user_id', $userId)
            ->when($dateFrom, fn($q) => $q->whereDate('login_at', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('login_at', '<=', $dateTo))
            ->orderByDesc('login_at')
            ->get();

        // Activity logs pada rentang tanggal
        $activities = ActivityLog::where('user_id', $userId)
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return view('owner.monitor-detail', compact(
            'user', 'sesiList', 'activities',
            'dateFrom', 'dateTo', 'perPage'
        ))->with('allowedPerPage', $this->allowedPerPage);
    }
}