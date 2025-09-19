<?php

/**
 * Author: Eng.Fahed
 * Global Helper Functions for HR System
 * دوال مساعدة عامة لنظام الموارد البشرية
 */

if (!function_exists('getGroupDisplayName')) {
    /**
     * الحصول على اسم المجموعة المعروض
     */
    function getGroupDisplayName($groupName)
    {
        $displayNames = [
            'users' => 'إدارة المستخدمين',
            'roles' => 'إدارة الأدوار',
            'permissions' => 'إدارة الصلاحيات',
            'employees' => 'إدارة الموظفين',
            'attendance' => 'الحضور والانصراف',
            'leaves' => 'إدارة الإجازات',
            'payroll' => 'إدارة الرواتب',
            'reports' => 'التقارير',
            'dashboard' => 'لوحة التحكم'
        ];
        
        return $displayNames[$groupName] ?? ucfirst(str_replace('_', ' ', $groupName));
    }
}

if (!function_exists('getPermissionDisplayName')) {
    /**
     * الحصول على اسم الصلاحية المعروض
     */
    function getPermissionDisplayName($permissionName)
    {
        $displayNames = [
            'view' => 'عرض',
            'create' => 'إنشاء',
            'edit' => 'تعديل',
            'delete' => 'حذف',
            'assign' => 'إسناد',
            'approve' => 'موافقة',
            'export' => 'تصدير'
        ];
        
        $parts = explode('.', $permissionName);
        $action = end($parts);
        
        return $displayNames[$action] ?? ucfirst($action);
    }
}

if (!function_exists('getUserTypeDisplayName')) {
    /**
     * الحصول على اسم نوع المستخدم المعروض
     */
    function getUserTypeDisplayName($type)
    {
        $displayNames = [
            'super_admin' => 'مدير عام',
            'tenant_admin' => 'مدير مؤسسة',
            'employee' => 'موظف'
        ];
        
        return $displayNames[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }
}

if (!function_exists('getUserTypeBadgeClass')) {
    /**
     * الحصول على لون badge حسب نوع المستخدم
     */
    function getUserTypeBadgeClass($type)
    {
        $classes = [
            'super_admin' => 'badge-danger',
            'tenant_admin' => 'badge-warning', 
            'employee' => 'badge-success'
        ];
        
        return $classes[$type] ?? 'badge-secondary';
    }
}

if (!function_exists('getPermissionIcon')) {
    /**
     * الحصول على أيقونة حسب نوع الصلاحية
     */
    function getPermissionIcon($permissionName)
    {
        $icons = [
            'users' => 'fas fa-users',
            'roles' => 'fas fa-user-shield',
            'permissions' => 'fas fa-key',
            'employees' => 'fas fa-user-tie',
            'attendance' => 'fas fa-clock',
            'leaves' => 'fas fa-calendar-alt',
            'payroll' => 'fas fa-money-bill-wave',
            'reports' => 'fas fa-chart-bar',
            'dashboard' => 'fas fa-tachometer-alt'
        ];
        
        $parts = explode('.', $permissionName);
        $module = $parts[0];
        
        return $icons[$module] ?? 'fas fa-cog';
    }
}
