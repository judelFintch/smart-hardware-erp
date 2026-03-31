<?php

namespace App\Livewire\System;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class Health extends Component
{
    public function render()
    {
        $dbOk = true;
        $dbError = null;

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbOk = false;
            $dbError = $e->getMessage();
        }

        $cacheOk = true;
        $cacheError = null;
        try {
            Cache::put('health_check', 'ok', 10);
            $cacheOk = Cache::get('health_check') === 'ok';
        } catch (\Throwable $e) {
            $cacheOk = false;
            $cacheError = $e->getMessage();
        }

        $storageOk = File::isWritable(storage_path());
        $publicOk = File::isWritable(public_path());
        $logsPath = storage_path('logs');
        $latestLogFile = collect(File::exists($logsPath) ? File::files($logsPath) : [])
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->first();

        $data = [
            'db' => [
                'ok' => $dbOk,
                'error' => $dbError,
                'connection' => config('database.default'),
                'database' => DB::connection()->getDatabaseName(),
            ],
            'cache' => [
                'ok' => $cacheOk,
                'error' => $cacheError,
                'driver' => config('cache.default'),
            ],
            'storage' => [
                'ok' => $storageOk,
                'free_space' => $this->formatBytes(disk_free_space(storage_path()) ?: 0),
            ],
            'public' => ['ok' => $publicOk],
            'queue' => ['driver' => config('queue.default')],
            'session' => ['driver' => config('session.driver')],
            'mail' => [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
            ],
            'logs' => [
                'writable' => File::isWritable($logsPath),
                'latest_file' => $latestLogFile?->getFilename(),
                'latest_size' => $latestLogFile ? $this->formatBytes($latestLogFile->getSize()) : '0 B',
            ],
            'app' => [
                'env' => config('app.env'),
                'debug' => config('app.debug') ? 'on' : 'off',
                'timezone' => config('app.timezone'),
                'url' => config('app.url'),
                'php' => PHP_VERSION,
                'laravel' => App::version(),
            ],
        ];

        return view('livewire.system.health', compact('data'))
            ->layout('layouts.app');
    }

    private function formatBytes(int|float $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }
}
