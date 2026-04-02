<?php

namespace Database\Seeders;

use App\Enums\UserStatuses;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class UsersSeeder extends Seeder {

    public function run(): void {

        $currentDateTime = now()->format('Y-m-d H:i:s');
        $count = 200;

        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $this->command->info("Truncated users table. Starting seeding...");

        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        User::withoutEvents(function () use ($count, $bar, $currentDateTime) {
            $chunks = array_chunk(range(1, $count), 100);

            foreach ($chunks as $chunk) {
                $data = User::factory()
                    ->count(count($chunk))
                    ->make()
                    ->makeVisible(['password', 'remember_token'])
                    ->map(function ($user) use ($currentDateTime) {
                        $array = $user->toArray();

                        if (isset($array['email_verified_at'])) {
                            $array['email_verified_at'] = $user->email_verified_at->format('Y-m-d H:i:s');
                        }

                        $array['status'] = Arr::random([
                            UserStatuses::ACTIVE->value,
                            UserStatuses::INACTIVE->value,
                            UserStatuses::DISABLED->value
                        ]);

                        $array['created_at'] = $currentDateTime;
                        $array['updated_at'] = $currentDateTime;

                        return $array;
                    })
                    ->toArray();

                User::insert($data);
                $bar->advance(count($chunk));
            }
        });

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Successfully created $count truly random users!");
    }
}
