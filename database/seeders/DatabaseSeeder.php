<?php

    namespace Database\Seeders;
    use Illuminate\Database\Seeder;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Str;


    class DatabaseSeeder extends Seeder
    {
        /**
         * Run the database seeders.
         *
         * @return void
         */
        public function run()
        {
            // The database migrations could be changed, in order to obtain the number
            // of devices required for each classroom.
            for ($index = 100; $index <= 120; $index++)
            {
                DB::table('device_states')->insert([
                    'device_id' => 'A-'.$index,
                    'value' => rand(10,50),
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'token' => Str::random(16),
                ]);
            }
            for ($index = 200; $index <= 220; $index++)
            {
                DB::table('device_states')->insert([
                    'device_id' => 'A-'.$index,
                    'value' => rand(10,50),
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'token' => Str::random(16),
                ]);
            }
            DB::table('users')->insert([
                'name' => 'Huber Girón Nieto',
                'email' => 'huber.giron2@iberopuebla.mx',
                'password' => Hash::make('123456huber'),
                'remember_token' => '143456huber',
            ]);
        }
    }
