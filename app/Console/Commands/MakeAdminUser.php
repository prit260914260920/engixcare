<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdminUser extends Command
{
    protected $signature = 'admin:create {email? : The email of the user to promote or create}';
    protected $description = 'Create a new admin user or promote an existing user to admin';

    public function handle(): void
    {
        $email = $this->argument('email') ?? $this->ask('Enter admin email');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['is_admin' => true]);
            $this->info("User [{$email}] has been promoted to admin.");
            return;
        }

        $name     = $this->ask('Enter admin name');
        $password = $this->secret('Enter admin password');

        User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        $this->info("Admin user [{$email}] created successfully.");
    }
}
