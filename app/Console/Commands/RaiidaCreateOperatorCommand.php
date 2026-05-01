<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Command\Command as CommandStatus;

class RaiidaCreateOperatorCommand extends Command
{
    protected $signature = 'raiida:admin:create
                            {--name= : Admin full name}
                            {--email= : Admin email}
                            {--password= : Admin password}
                            {--force : Overwrite existing account with same email}';

    protected $aliases = [
        'raiida:operator:create',
    ];

    protected $description = 'Create or update an admin account for API login';

    public function handle(): int
    {
        $name = (string) ($this->option('name') ?: $this->ask('Name'));
        $email = (string) ($this->option('email') ?: $this->ask('Email'));
        $password = (string) ($this->option('password') ?: $this->secret('Password'));
        $role = User::ROLE_ADMIN;

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password, 'role' => $role],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email'],
                'password' => ['required', 'string', 'min:8'],
                'role' => ['required', 'string', 'in:' . User::ROLE_ADMIN],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return CommandStatus::FAILURE;
        }

        $existing = User::query()->where('email', $email)->first();
        if ($existing !== null && ! $this->option('force')) {
            $this->error('Account already exists. Use --force to update.');
            return CommandStatus::FAILURE;
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => $role,
            ]
        );

        $this->info("User ready: {$user->email} ({$user->role})");

        return CommandStatus::SUCCESS;
    }
}
