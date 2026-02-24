<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'memberships');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
