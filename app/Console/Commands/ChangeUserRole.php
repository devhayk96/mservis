<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Exception\RuntimeException;

class ChangeUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:change-role
                            {user_id : ID of the user}
                            {role : A role of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            'user_id' => 'exists:users,id',
            'role' => 'in:' . $roles->join(',')
        ], [
            'user_id.exists' => 'Such user doesn\'t exist.',
            'role.required' => 'User role is required.',
            'role.in' => 'Role should be one of the followed: ' . $roles->join(', '),
        ]);

        if ($validator->fails()) {
            throw new RuntimeException($validator->errors()->first());
        }

        User::find($this->argument('user_id'))->syncRoles($this->argument('role'));
        $this->info('Role of the user has been changed.');
    }
}
