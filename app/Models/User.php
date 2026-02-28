<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Services\ReputationManager;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_banned',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_banned' => 'boolean',
        ];
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function colocations()
    {
        return $this->belongsToMany(Colocation::class, 'memberships');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'paid_by');
    }

    public function sentPayments()
    {
        return $this->hasMany(Payment::class, 'from_user_id');
    }

    public function receivedPayments()
    {
        return $this->hasMany(Payment::class, 'to_user_id');
    }

    public function sentInvitations()
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    /**
     * Update the user reputation score.
     *
     * @param int $points
     * @return void
     */
    public function updateReputation(int $points)
    {
        $manager = new ReputationManager();
        if ($points > 0) {
            $manager->increase($this, $points);
        } else {
            $manager->decrease($this, abs($points));
        }
    }

    /**
     * Check if the user has an active colocation membership.
     *
     * @return bool
     */
    public function hasActiveColocation(): bool
    {
        return $this->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', function ($query) {
                $query->where('status', 'active');
            })
            ->exists();
    }

    /**
     * Get all current active memberships of the user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function activeMemberships()
    {
        return $this->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', function ($query) {
                $query->where('status', 'active');
            })
            ->with('colocation')
            ->get();
    }

    /**
     * Get first current active membership of the user.
     *
     * @return Membership|null
     */
    public function activeMembership()
    {
        return $this->activeMemberships()->first();
    }
}
