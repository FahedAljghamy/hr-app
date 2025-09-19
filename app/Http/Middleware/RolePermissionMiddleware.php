<?php

/**
 * Author: Eng.Fahed
 * Role Permission Middleware for HR System
 * التحكم بالوصول حسب الأدوار والصلاحيات
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // التحقق من تسجيل الدخول
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = auth()->user();

        // إذا كان المستخدم Super Admin، السماح بكل شيء
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // التحقق من الصلاحيات المطلوبة
        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
                // إذا كانت الصلاحية تحتوي على "|" فهذا يعني أي من الصلاحيات
                if (strpos($permission, '|') !== false) {
                    $permissionArray = explode('|', $permission);
                    $hasAnyPermission = false;
                    
                    foreach ($permissionArray as $perm) {
                        if ($user->can(trim($perm))) {
                            $hasAnyPermission = true;
                            break;
                        }
                    }
                    
                    if (!$hasAnyPermission) {
                        return $this->handleUnauthorized($request);
                    }
                } else {
                    // التحقق من صلاحية واحدة
                    if (!$user->can($permission)) {
                        return $this->handleUnauthorized($request);
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * التعامل مع المستخدمين غير المخولين
     */
    private function handleUnauthorized(Request $request)
    {
        // إذا كان الطلب AJAX أو API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول إلى هذا المورد',
                'error_code' => 'UNAUTHORIZED'
            ], 403);
        }

        // إذا كان طلب عادي، إعادة توجيه مع رسالة خطأ
        return redirect()->back()->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
    }
}