<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            DB::select('select 1');
        } catch (Throwable) {
            return response()->json(['status' => 'degraded'], 503);
        }

        return response()->json(['status' => 'ok']);
    }

    public function ready(): JsonResponse
    {
        $checks = [
            'database' => false,
            'migrations_table' => false,
            'pending_migrations' => false,
        ];

        try {
            DB::select('select 1');
            $checks['database'] = true;
            $checks['migrations_table'] = Schema::hasTable('migrations');

            if ($checks['migrations_table']) {
                $migrationFiles = collect(glob(database_path('migrations/*.php')) ?: [])
                    ->map(fn (string $path) => basename($path, '.php'));

                $ran = DB::table('migrations')->pluck('migration');
                $checks['pending_migrations'] = $migrationFiles->diff($ran)->isEmpty();
            }
        } catch (Throwable) {
            return response()->json(['status' => 'degraded', 'checks' => $checks], 503);
        }

        $ready = ! in_array(false, $checks, true);

        return response()->json([
            'status' => $ready ? 'ready' : 'degraded',
            'checks' => $checks,
        ], $ready ? 200 : 503);
    }
}
