<?php

namespace App\Livewire\System;

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

        $data = [
            'db' => ['ok' => $dbOk, 'error' => $dbError],
            'cache' => ['ok' => $cacheOk, 'error' => $cacheError],
            'storage' => ['ok' => $storageOk],
            'public' => ['ok' => $publicOk],
            'queue' => ['driver' => config('queue.default')],
            'mail' => [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
            ],
            'app' => [
                'env' => config('app.env'),
                'debug' => config('app.debug') ? 'on' : 'off',
                'timezone' => config('app.timezone'),
            ],
        ];

        return view('livewire.system.health', compact('data'))
            ->layout('layouts.app');
    }
}
