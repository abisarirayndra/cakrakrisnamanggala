<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(false)->after('role_id');
            });
        }

        if (! Schema::hasTable('admin_markas')) {
            Schema::create('admin_markas', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('markas_id');
                $table->timestamps();
                $table->unique(['user_id', 'markas_id']);
            });
        }

        DB::table('users')
            ->where('role_id', 2)
            ->update(['is_super_admin' => true]);

        $now = now();

        DB::table('users')
            ->where('role_id', 7)
            ->pluck('id')
            ->each(function ($userId) use ($now) {
                $markasId = DB::table('adm_pendidik')
                    ->where('pendidik_id', $userId)
                    ->value('markas_id');

                if ($markasId !== null) {
                    DB::table('admin_markas')->insertOrIgnore([
                        'user_id' => $userId,
                        'markas_id' => $markasId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });

        DB::table('users')
            ->where('role_id', 7)
            ->update([
                'role_id' => 2,
                'is_super_admin' => false,
            ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_markas');

        if (Schema::hasColumn('users', 'is_super_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_super_admin');
            });
        }
    }
};
