<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 執行請求，獲取回應
        $response = $next($request);

        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
            'status_code' => $response->getStatusCode(), // 回應狀態碼
            'error_message' => $response->getContent(), // 回應訊息內容（用於查看錯誤詳細資訊）
        ];

        // 儲存到 Redis (使用列表結構)
        $key = 'api_requests_log';
        Redis::lpush($key, json_encode($logData));

        // 按 status_code 統計請求數量
        $statusCodeKey = 'api_requests_status_code'; // 用於統計的 Hash 鍵
        $statusCodeLabel = (string) $response->getStatusCode(); // 將 status_code 作為標籤
        Redis::hincrby($statusCodeKey, $statusCodeLabel, 1); // 將該狀態碼計數加 1
      

        // 返回回應
        return $response;
    }
}
