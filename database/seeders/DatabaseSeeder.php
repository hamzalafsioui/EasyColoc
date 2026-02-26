<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Category;
use App\Models\Membership;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@easycoloc.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        // Create some regular Users
        $users = User::factory(4)->create();
        $allUsers = $users->push($admin);

        // Create a Colocation
        $colocation = Colocation::create([
            'name' => 'The Awesome Crib',
            'description' => 'Our cozy shared apartment in the city center.',
            'status' => 'active',
        ]);

        // Create Memberships
        foreach ($allUsers as $user) {
            Membership::create([
                'user_id' => $user->id,
                'colocation_id' => $colocation->id,
                'role' => $user->id === $admin->id ? 'owner' : 'member',
                'joined_at' => now(),
            ]);
        }

        // Create Categories
        $this->call(CategorySeeder::class);
        $categories = Category::where('colocation_id', $colocation->id)->get()->keyBy('name');

        // Create some Expenses
        // Admin paid for Rent
        Expense::create([
            'colocation_id' => $colocation->id,
            'paid_by' => $admin->id,
            'category_id' => $categories['Rent']->id,
            'title' => 'Monthly Rent',
            'amount' => 1500.00,
            'date' => now()->startOfMonth(),
            'description' => 'May rent payment',
        ]);

        // User 2 paid for Food
        Expense::create([
            'colocation_id' => $colocation->id,
            'paid_by' => $users[0]->id,
            'category_id' => $categories['Food']->id,
            'title' => 'Grocery Shopping',
            'amount' => 200.00,
            'date' => now()->subDays(2),
            'description' => 'Weekly groceries',
        ]);

        // User 3 paid for Utilities
        Expense::create([
            'colocation_id' => $colocation->id,
            'paid_by' => $users[1]->id,
            'category_id' => $categories['Utilities']->id,
            'title' => 'Electricity Bill',
            'amount' => 120.00,
            'date' => now()->subDays(5),
            'description' => 'Summer electricity',
        ]);

        // Create some Payments 
        // User 4 pays 100 to Admin
        Payment::create([
            'colocation_id' => $colocation->id,
            'from_user_id' => $users[2]->id,
            'to_user_id' => $admin->id,
            'amount' => 100.00,
            'paid_at' => now(),
        ]);
    }
}
