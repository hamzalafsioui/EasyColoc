<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $invitations = Invitation::where('email', auth()->user()->email)
            ->where('status', 'pending')
            ->with('colocation')
            ->get();

        return view('dashboard', compact('invitations'));
    }
}
