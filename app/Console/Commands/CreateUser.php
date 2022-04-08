<?php

namespace App\Console\Commands;

use Str;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Exception\RuntimeException;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            {email : An email of the user}
                            {role : A role of the user}
                            {--merchant_id= : Id of a merchant (for users of merchants)}
                            {--name : A name of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new user to the system.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $roles = Role::all()->pluck('name');

        $validator = Validator::make($this->arguments(), [
            'email' => 'email|unique:users,email',
            'role' => 'in:' . $roles->join(',')
        ], [
            'email.email' => 'Wrong email format.',
            'email.unique' => sprintf('A user with Email %s already exists', $this->argument('email')),
            'role.in' => 'Role should be one of the followed: ' . $roles->join(', '),
        ]);

        if ($validator->fails()) {
            throw new RuntimeException($validator->errors()->first());
        }

        $password = Str::random(10);

        $user = User::create([
            'name' => $this->option('name') ?:  ('user_' . Str::random(10)),
            'email' => $this->argument('email'),
            'password' => Hash::make($password),
            'merchant_id' =>  $this->option('merchant_id'),
        ]);

        $user->markEmailAsVerified();
        $user->assignRole($this->argument('role'));

        $this->info('A new user has been created.');
        $this->table(
            ['ID', 'Name', 'Email', 'Password'],
            [[$user->id, $user->name, $user->email, $password]]
        );
    }
}
