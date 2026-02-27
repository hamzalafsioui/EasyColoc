<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with stats and users list.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_colocations' => Colocation::count(),
            'total_expenses_count' => Expense::count(),
            'total_expenses_amount' => Expense::sum('amount'),
            'total_banned_users' => User::where('is_banned', true)->count(),
        ];

        $users = User::latest()->paginate(20);

        return view('admin.dashboard', compact('stats', 'users'));
    }

    /**
     * Ban a user.
     */
    public function ban(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot ban yourself. (-_-)');
        }

        $user->update(['is_banned' => true]);

        return redirect()->back()->with('success', "User {$user->name} has been banned. -)");
    }

    /**
     * Unban a user.
     */
    public function unban(User $user)
    {
        $user->update(['is_banned' => false]);

        return redirect()->back()->with('success', "User {$user->name} has been unbanned. -)");
    }
}
