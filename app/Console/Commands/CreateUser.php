<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {--email=} {--password=} {--name=} {--update-existing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update a user with email and password';

    public function handle(): int
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name') ?? 'Admin';

        if (! $email || ! $password) {
            $this->error('Please provide --email and --password');
            return 1;
        }

        $user = User::where('email', $email)->first();

        if ($user && ! $this->option('update-existing')) {
            $this->error('User already exists. Use --update-existing to update.');
            return 1;
        }

        if (! $user) {
            $user = new User();
        }

        $user->name = $name; // maps to nama via model accessor
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();

        $this->info('User created/updated: '.$email);

        return 0;
    }
}
