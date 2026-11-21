<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenantVerificationController extends Controller
{
    public function index(): View
    {
        $pending = User::where('role', UserRole::Tenant)->where('status', UserStatus::Pending)->oldest()->get();
        $others = User::where('role', UserRole::Tenant)->where('status', '!=', UserStatus::Pending)->latest()->limit(50)->get();

        return view('admin.tenants.index', compact('pending', 'others'));
    }

    public function approve(User $user): RedirectResponse
    {
        return $this->setStatus($user, UserStatus::Approved, 'approved');
    }

    public function reject(User $user): RedirectResponse
    {
        return $this->setStatus($user, UserStatus::Rejected, 'rejected');
    }

    private function setStatus(User $user, UserStatus $status, string $label): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);
        $user->update(['status' => $status]);

        return back()->with('success', "Registration of {$user->name} {$label}.");
    }
}