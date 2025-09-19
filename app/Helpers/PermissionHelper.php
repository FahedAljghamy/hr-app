<?php

/**
 * Author: Eng.Fahed
 * Permission Helper Class for HR System
 * مساعد مركزي للدوال المشتركة في نظام الصلاحيات
 */

namespace App\Helpers;

class PermissionHelper
{
    /**
     * الحصول على اسم المجموعة المعروض
     */
    public static function getGroupDisplayName($groupName)
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

    /**
     * الحصول على اسم الصلاحية المعروض
     */
    public static function getPermissionDisplayName($permissionName)
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

    /**
     * الحصول على اسم نوع المستخدم المعروض
     */
    public static function getUserTypeDisplayName($type)
    {
        $displayNames = [
            'super_admin' => 'مدير عام',
            'tenant_admin' => 'مدير مؤسسة',
            'employee' => 'موظف'
        ];
        
        return $displayNames[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /**
     * الحصول على لون badge حسب نوع المستخدم
     */
    public static function getUserTypeBadgeClass($type)
    {
        $classes = [
            'super_admin' => 'badge-danger',
            'tenant_admin' => 'badge-warning', 
            'employee' => 'badge-success'
        ];
        
        return $classes[$type] ?? 'badge-secondary';
    }

    /**
     * الحصول على أيقونة حسب نوع الصلاحية
     */
    public static function getPermissionIcon($permissionName)
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
