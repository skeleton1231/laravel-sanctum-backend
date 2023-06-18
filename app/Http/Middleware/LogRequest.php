<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    public function handle($request, Closure $next)
    {
        Log::info('Request method: ' . $request->method());
        Log::info('Request URL: ' . $request->fullUrl());
        Log::info('Request data: ', $request->all());

        try {
            $response = $next($request);

            if (!$response instanceof Response) {
                return $response;
            }

            Log::info('Response status: ' . $response->status());

            if ($response->isSuccessful()) {
                Log::info('Response data: ', [$response->getContent()]);
            } else {
                Log::error('Error response: ', [$response->getContent()]);
                // Add additional error handling here, if required
            }

            return $response;
        } catch (Exception $e) {
            Log::error('System Error: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Handle the exception (e.g., by returning a custom error response)
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }
}
