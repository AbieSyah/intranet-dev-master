<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RemoveUpgradeInsecureHeaders
{
    /**
     * Hapus tag meta Content-Security-Policy "upgrade-insecure-requests"
     * dari seluruh respons HTML.
     *
     * Tag ini memaksa browser meng-upgrade semua request http:// menjadi
     * https://. Karena server dev hanya melayani HTTP, semua aset (JS/CSS/
     * gambar) gagal di-load dengan error ERR_CONNECTION_CLOSED.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (method_exists($response, 'getContent')) {
            $content = $response->getContent();

            $content = preg_replace(
                '/<meta\s+http-equiv=["\']Content-Security-Policy["\']\s+content=["\'][^"\']*upgrade-insecure-requests[^"\']*["\']\s*\/?>\s*/i',
                '',
                $content
            );

            $response->setContent($content);
        }

        return $response;
    }
}
