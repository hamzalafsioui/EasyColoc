<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InvitationController extends Controller
{
    /**
     * List all invitations for a colocation (Owner only).
     */
    public function index(Colocation $colocation)
    {
        $membership = $colocation->memberships()->where('user_id', auth()->id())->first();

        if (!$membership || $membership->role !== 'owner') {
            abort(403);
        }

        $invitations = $colocation->invitations()->latest()->get();

        return view('invitations.index', compact('colocation', 'invitations'));
    }

    /**
     * Display the invitation landing page.
     */
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->with('colocation.memberships.user')->firstOrFail();

        return view('invitations.show', compact('invitation'));
    }

    /**
     * Create an invitation for the colocation.
     */
    public function create(Request $request, Colocation $colocation)
    {
        $membership = $colocation->memberships()->where('user_id', auth()->id())->first();

        if (!$membership || $membership->role !== 'owner') {
            abort(403, 'Only owners can create invitations.');
        }

        $request->validate([
            'email' => 'required|email'
        ]);

        $existingInvitation = $colocation->invitations()
            ->where('email', $request->email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            return redirect()->back()->with('error', 'A pending invitation already exists for this email. (-_-)');
        }

        $token = Str::random(32);

        $invitation = $colocation->invitations()->create([
            'invited_by' => auth()->id(),
            'email' => $request->email,
            'token' => $token,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\InvitationMail($invitation));

        return redirect()->route('invitations.index', $colocation)
            ->with('success', "Invitation generated for {$request->email}. Link: " . route('invitations.show', $token));
    }

    /**
     * Cancel an invitation (Owner only).
     */
    public function destroy(Invitation $invitation)
    {
        $membership = $invitation->colocation->memberships()->where('user_id', auth()->id())->first();

        if (!$membership || $membership->role !== 'owner') {
            abort(403);
        }

        if ($invitation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending invitations can be cancelled.');
        }

        $invitation->delete();

        return redirect()->back()->with('success', 'Invitation cancelled successfully.');
    }

    /**
     * Accept a colocation invitation.
     */
    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        // Check if token is expired or already used
        if ($invitation->status !== 'pending' || $invitation->expires_at < now()) {
            return redirect()->route('dashboard')->with('error', 'The invitation is invalid or has expired. (-_-)');
        }

        // Check if user has active colocation
        if (auth()->user()->hasActiveColocation()) {
            return redirect()->route('dashboard')->with('error', 'You already have an active colocation. (-_-)');
        }

        $colocation = $invitation->colocation;

        DB::transaction(function () use ($colocation, $invitation) {
            $colocation->memberships()->create([
                'user_id' => auth()->id(),
                'role' => 'member',
                'joined_at' => now(),
            ]);

            $invitation->update(['status' => 'accepted']);
        });

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'You have successfully joined the colocation. -)');
    }

    /**
     * Refuse a colocation invitation.
     */
    public function refuse(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->status !== 'pending' || $invitation->expires_at < now()) {
            return redirect()->route('dashboard')->with('error', 'The invitation is invalid or has expired. (-_-)');
        }

        $invitation->update(['status' => 'refused']);

        return redirect()->route('dashboard')
            ->with('success', 'You have refused the colocation invitation. -)');
    }
}
