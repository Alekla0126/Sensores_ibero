<?php

    namespace Database\Seeders;
    use Illuminate\Database\Seeder;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;


    class DatabaseSeeder extends Seeder
    {
        /**
         * Run the database seeders.
         *
         * @return void
         */
        public function run()
        {
            for ($index = 100; $index <= 120; $index++)
            {
                DB::table('device_states')->insert([
                    'device_id' => 'A-'.$index,
                    'value' => rand(10,100),
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
            }
            for ($index = 200; $index <= 220; $index++)
            {
                DB::table('device_states')->insert([
                    'device_id' => 'A-'.$index,
                    'value' => rand(10,100),
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
            }
            DB::table('users')->insert([
                'name' => 'Alejandro',
                'email' => 'alekla0126@gmail.com',
                'password' => Hash::make('chispaS14-/'),
                'remember_token' => '14/',
            ]);
        }
    }
