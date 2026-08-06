<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class DeactivateInactiveUsers extends Command
{
    protected $signature = 'users:deactivate-inactive';
    protected $description = 'Nonaktifkan user yang tidak login selama lebih dari 7 hari, kecuali superadmin';


    public function handle()
    {
        $thresholdDate = Carbon::now()->subDays(7);


        $users = User::where('role', '!=', 'superadmin')
                    ->where('is_active', true)
                    ->where(function ($query) use ($thresholdDate) {
                        $query->whereNull('last_login')
                            ->orWhere('last_login', '<', $thresholdDate);
                    })->get();


        foreach ($users as $user) {
            $user->is_active = false;
            $user->save();


            Log::info("User {$user->name} dinonaktifkan otomatis karena tidak login sejak {$user->last_login}");
        }


        $this->info(count($users) . ' user berhasil dinonaktifkan.');
    }
}
