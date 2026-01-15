<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class PreserveUtmParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ áp dụng cho frontend, không áp dụng cho dashboard/backend
        if (!$this->isBackendRoute($request)) {
            // Lấy UTM parameters từ query string
            $utmParams = [];
            $utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
            
            foreach ($utmKeys as $key) {
                if ($request->has($key)) {
                    $utmParams[$key] = $request->input($key);
                }
            }
            
            // Nếu có UTM parameters trong request, lưu vào session VÀ cookie
            if (!empty($utmParams)) {
                // Lưu vào session
                session(['utm_parameters' => $utmParams]);
                session()->save();
                
                // Lưu vào cookie để đảm bảo persist và dễ debug (30 ngày)
                $response = $next($request);
                foreach ($utmParams as $key => $value) {
                    $response->cookie($key, $value, 60 * 24 * 30); // 30 ngày
                }
                
                Log::info('UTM Middleware - Saved UTM to session and cookie:', $utmParams);
                return $response;
            }
            
            // Nếu không có UTM trong request, thử lấy từ cookie và lưu vào session
            $cookieUtmParams = [];
            foreach ($utmKeys as $key) {
                if ($request->cookie($key)) {
                    $cookieUtmParams[$key] = $request->cookie($key);
                }
            }
            
            if (!empty($cookieUtmParams)) {
                session(['utm_parameters' => $cookieUtmParams]);
                Log::info('UTM Middleware - Loaded UTM from cookie to session:', $cookieUtmParams);
            }
            
            // Debug: Log session hiện tại
            $currentUtm = session('utm_parameters', []);
            if (!empty($currentUtm)) {
                Log::info('UTM Middleware - Current session UTM:', $currentUtm);
            }
            
            // Nếu không có UTM trong request nhưng có trong session, giữ lại
            // (để có thể sử dụng trong helper function)
        }
        
        return $next($request);
    }
    
    /**
     * Kiểm tra xem route có phải là backend/dashboard không
     */
    private function isBackendRoute(Request $request): bool
    {
        $path = $request->path();
        
        // Kiểm tra các prefix backend phổ biến
        $backendPrefixes = [
            'admin',
            'dashboard',
            'backend',
            'api',
        ];
        
        foreach ($backendPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }
        
        // Kiểm tra route name nếu có
        $route = $request->route();
        if ($route && $route->getName()) {
            $routeName = $route->getName();
            if (str_starts_with($routeName, 'admin.') || 
                str_starts_with($routeName, 'backend.') ||
                str_starts_with($routeName, 'dashboard.')) {
                return true;
            }
        }
        
        return false;
    }
}
