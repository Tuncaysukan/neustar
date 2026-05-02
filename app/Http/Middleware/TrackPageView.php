<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackPageView
{
    // Admin, API ve asset isteklerini atla
    private array $skip = ['/admin', '/api/', '/_debugbar', '/build/', '/storage/'];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Sadece GET, frontend, başarılı istekler
        if (
            $request->isMethod('GET') &&
            $response->getStatusCode() === 200 &&
            ! $request->is('admin*', 'api/*', 'login', 'register', 'sitemap.xml') &&
            ! $request->ajax()
        ) {
            try {
                DB::table('page_views')->insert([
                    'path'        => substr($request->path(), 0, 500),
                    'ip'          => $request->ip(),
                    'viewed_date' => now()->toDateString(),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            } catch (\Throwable) {
                // Sessizce geç — tracking hatası siteyi durdurmasın
            }
        }

        return $response;
    }
}
