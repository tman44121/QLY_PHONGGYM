<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('gym_packages')
            ->where('name', 'Gói 12 tháng')
            ->where('price', 10000000)
            ->update([
                'description' => 'Tập luyện không giới hạn trong 12 tháng tại The Gym. Tiết kiệm 399.000 VNĐ so với mua 12 gói tháng.',
                'price' => 4389000,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('gym_packages')
            ->where('name', 'Gói 12 tháng')
            ->where('price', 4389000)
            ->update([
                'description' => 'Tập luyện không giới hạn trong 12 tháng tại The Gym.',
                'price' => 10000000,
                'updated_at' => now(),
            ]);
    }
};
