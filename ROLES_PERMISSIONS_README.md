# نظام الأدوار والصلاحيات الديناميكي - HR System
**Author: Eng.Fahed**

## نظرة عامة

تم تطوير نظام شامل للأدوار والصلاحيات الديناميكي لنظام الموارد البشرية باستخدام Laravel 10 و spatie/laravel-permission. يوفر النظام تحكمًا كاملاً بصلاحيات المستخدمين مع واجهات مستخدم حديثة وسهلة الاستخدام.

## المميزات الرئيسية

### 🔐 إدارة الأدوار (Roles Management)
- **CRUD كامل للأدوار**: إنشاء، عرض، تعديل، وحذف الأدوار
- **إسناد الصلاحيات للأدوار**: واجهة ديناميكية لربط الصلاحيات بالأدوار
- **عرض تفصيلي للأدوار**: مع إحصائيات وتفاصيل الصلاحيات
- **أدوار افتراضية**: Admin, Manager, Employee

### 🛡️ إدارة الصلاحيات (Permissions Management)
- **CRUD كامل للصلاحيات**: إدارة شاملة للصلاحيات
- **إنشاء متعدد للصلاحيات**: إنشاء مجموعة صلاحيات دفعة واحدة
- **تجميع الصلاحيات**: عرض الصلاحيات مجمعة حسب النوع
- **صلاحيات شاملة**: تغطي جميع جوانب نظام الموارد البشرية

### 👥 إدارة المستخدمين (User Management)
- **إدارة شاملة للمستخدمين**: إنشاء وتعديل المستخدمين
- **إسناد الأدوار**: ربط المستخدمين بالأدوار المناسبة
- **صلاحيات مباشرة**: إمكانية إعطاء صلاحيات مباشرة للمستخدمين
- **فلاتر متقدمة**: البحث والفلترة حسب الدور ونوع المستخدم

## الهيكل التقني

### 📋 الجداول المُنشأة
```sql
- roles                    # جدول الأدوار
- permissions             # جدول الصلاحيات  
- model_has_roles         # ربط المستخدمين بالأدوار
- model_has_permissions   # ربط المستخدمين بالصلاحيات المباشرة
- role_has_permissions    # ربط الأدوار بالصلاحيات
```

### 🎮 الكنترولرز المُطورة
1. **RoleController**: إدارة الأدوار مع API endpoints
2. **PermissionController**: إدارة الصلاحيات مع الإنشاء المتعدد
3. **UserManagementController**: إدارة المستخدمين والأدوار

### 🛡️ الحماية والأمان
- **Middleware متقدم**: للتحكم بالوصول حسب الصلاحيات
- **Validation شامل**: للتأكد من صحة البيانات
- **CSRF Protection**: حماية من هجمات CSRF
- **Authorization**: تحكم دقيق بالوصول للوظائف

## الصلاحيات المتاحة

### 👤 إدارة المستخدمين
- `users.view` - عرض المستخدمين
- `users.create` - إنشاء مستخدمين جدد
- `users.edit` - تعديل المستخدمين
- `users.delete` - حذف المستخدمين

### 🔐 إدارة الأدوار
- `roles.view` - عرض الأدوار
- `roles.create` - إنشاء أدوار جديدة
- `roles.edit` - تعديل الأدوار
- `roles.delete` - حذف الأدوار
- `roles.assign` - إسناد الأدوار

### 🛡️ إدارة الصلاحيات
- `permissions.view` - عرض الصلاحيات
- `permissions.create` - إنشاء صلاحيات جديدة
- `permissions.edit` - تعديل الصلاحيات
- `permissions.delete` - حذف الصلاحيات
- `permissions.assign` - إسناد الصلاحيات

### 💼 إدارة الموظفين
- `employees.view` - عرض الموظفين
- `employees.create` - إضافة موظفين جدد
- `employees.edit` - تعديل بيانات الموظفين
- `employees.delete` - حذف الموظفين

### 📅 الحضور والانصراف
- `attendance.view` - عرض سجلات الحضور
- `attendance.create` - تسجيل الحضور
- `attendance.edit` - تعديل سجلات الحضور
- `attendance.delete` - حذف سجلات الحضور

### 🏖️ إدارة الإجازات
- `leaves.view` - عرض الإجازات
- `leaves.create` - طلب إجازات جديدة
- `leaves.edit` - تعديل طلبات الإجازات
- `leaves.delete` - حذف طلبات الإجازات
- `leaves.approve` - الموافقة على الإجازات

### 💰 إدارة الرواتب
- `payroll.view` - عرض الرواتب
- `payroll.create` - إنشاء سجلات رواتب
- `payroll.edit` - تعديل الرواتب
- `payroll.delete` - حذف سجلات الرواتب

### 📊 التقارير
- `reports.view` - عرض التقارير
- `reports.export` - تصدير التقارير

### 🏠 لوحة التحكم
- `dashboard.view` - الوصول للوحة التحكم
- `dashboard.admin` - لوحة تحكم المدير

## Routes المُضافة

### Web Routes
```php
// إدارة الأدوار والصلاحيات
Route::resource('roles', RoleController::class);
Route::resource('permissions', PermissionController::class);
Route::resource('user-management', UserManagementController::class);

// API Routes
Route::prefix('api')->group(function () {
    Route::get('roles', [RoleController::class, 'apiIndex']);
    Route::post('roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions']);
    Route::get('permissions', [PermissionController::class, 'apiIndex']);
    
    // إدارة أدوار وصلاحيات المستخدمين
    Route::post('users/{user}/assign-role', [UserManagementController::class, 'assignRole']);
    Route::post('users/{user}/revoke-role', [UserManagementController::class, 'revokeRole']);
    Route::post('users/{user}/assign-permission', [UserManagementController::class, 'assignPermission']);
    Route::post('users/{user}/revoke-permission', [UserManagementController::class, 'revokePermission']);
});

// الإنشاء المتعدد للصلاحيات
Route::post('permissions/bulk-create', [PermissionController::class, 'bulkCreate']);
```

## الواجهات المُطورة

### 🎨 تصميم حديث ومتجاوب
- **Tailwind CSS**: تصميم حديث وأنيق
- **Dark Mode Support**: دعم الوضع المظلم
- **Mobile Responsive**: متجاوب مع جميع الأجهزة
- **Arabic RTL**: دعم كامل للغة العربية

### 📱 Blade Views المُنشأة

#### الأدوار (Roles)
- `roles/index.blade.php` - قائمة الأدوار مع البحث والفلترة
- `roles/create.blade.php` - إنشاء دور جديد مع اختيار الصلاحيات
- `roles/show.blade.php` - عرض تفاصيل الدور والإحصائيات
- `roles/edit.blade.php` - تعديل الدور مع ملخص التغييرات

#### الصلاحيات (Permissions)
- `permissions/index.blade.php` - قائمة الصلاحيات مع التجميع والإحصائيات
- `permissions/create.blade.php` - إنشاء صلاحية جديدة مع مولد الأسماء

#### إدارة المستخدمين (User Management)
- `user-management/index.blade.php` - قائمة المستخدمين مع الفلاتر المتقدمة

### ⚡ JavaScript المُطور
- **تفاعلية متقدمة**: تحديد الصلاحيات بالمجموعات
- **التحقق من النماذج**: validation في الوقت الفعلي
- **تأكيد العمليات**: رسائل تأكيد للعمليات الحساسة
- **تحسين UX**: تأثيرات بصرية وتنبيهات تلقائية

## الاختبارات

### 🧪 Feature Tests
تم إنشاء مجموعة شاملة من الاختبارات في `RolePermissionTest`:

```php
- test_default_roles_and_permissions_are_created()
- test_user_can_be_assigned_roles()
- test_user_has_permissions_through_roles()
- test_user_can_have_direct_permissions()
- test_super_admin_has_all_permissions()
- test_can_create_role_with_permissions()
- test_can_remove_roles_and_permissions()
- test_permission_middleware_works()
- test_can_update_role_permissions()
- test_cannot_delete_role_with_users()
```

### ▶️ تشغيل الاختبارات
```bash
php artisan test --filter=RolePermissionTest
```

## API Documentation

### 📋 Postman Collection
تم إنشاء مجموعة شاملة من endpoints في `postman_collection.json`:

#### Authentication
- `POST /login` - تسجيل الدخول

#### Roles Management
- `GET /roles` - جلب جميع الأدوار
- `POST /roles` - إنشاء دور جديد
- `GET /roles/{id}` - عرض دور محدد
- `PUT /roles/{id}` - تحديث دور
- `DELETE /roles/{id}` - حذف دور
- `POST /api/roles/{role}/assign-permissions` - إسناد صلاحيات لدور

#### Permissions Management
- `GET /permissions` - جلب جميع الصلاحيات
- `POST /permissions` - إنشاء صلاحية جديدة
- `POST /permissions/bulk-create` - إنشاء متعدد للصلاحيات

#### User Management
- `GET /user-management` - جلب المستخدمين مع فلاتر
- `POST /user-management` - إنشاء مستخدم جديد
- `POST /api/users/{user}/assign-role` - إسناد دور لمستخدم
- `POST /api/users/{user}/assign-permission` - إسناد صلاحية لمستخدم

## التثبيت والإعداد

### 1. تثبيت الحزمة
```bash
composer require spatie/laravel-permission
```

### 2. نشر الملفات
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### 3. تشغيل Migrations
```bash
php artisan migrate
```

### 4. تشغيل Seeders
```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 5. إعداد User Model
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // ...
}
```

## استخدام النظام

### 🔧 إسناد الأدوار والصلاحيات

```php
// إسناد دور للمستخدم
$user->assignRole('Manager');

// إسناد صلاحية مباشرة
$user->givePermissionTo('reports.export');

// التحقق من الصلاحيات
if ($user->can('users.create')) {
    // المستخدم يمكنه إنشاء مستخدمين جدد
}

// إنشاء دور مع صلاحيات
$role = Role::create(['name' => 'HR Specialist']);
$role->givePermissionTo(['employees.view', 'leaves.approve']);
```

### 🛡️ حماية Routes
```php
// حماية بالصلاحيات
Route::middleware(['auth', 'permission:users.create'])->group(function () {
    Route::get('/users/create', [UserController::class, 'create']);
});

// حماية بالأدوار
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
});
```

### 🎯 في Blade Templates
```blade
@can('users.create')
    <a href="{{ route('users.create') }}">إضافة مستخدم جديد</a>
@endcan

@role('Admin')
    <div class="admin-panel">
        <!-- محتوى خاص بالمدير -->
    </div>
@endrole
```

## الأدوار الافتراضية

### 👑 Admin (مدير عام)
- **جميع الصلاحيات**: وصول كامل لجميع وظائف النظام
- **إدارة المستخدمين**: إنشاء وتعديل وحذف المستخدمين
- **إدارة الأدوار والصلاحيات**: تحكم كامل بنظام الصلاحيات

### 👔 Manager (مدير)
- **إدارة الموظفين**: عرض وإنشاء وتعديل الموظفين
- **الحضور والانصراف**: عرض وتعديل سجلات الحضور
- **الإجازات**: عرض والموافقة على طلبات الإجازات
- **الرواتب والتقارير**: عرض وتصدير التقارير

### 👨‍💼 Employee (موظف)
- **لوحة التحكم**: الوصول للوحة التحكم الأساسية
- **الحضور**: عرض وتسجيل الحضور الشخصي
- **الإجازات**: عرض وطلب الإجازات الشخصية

## ملاحظات مهمة

### ⚠️ اعتبارات الأمان
- جميع العمليات محمية بـ CSRF tokens
- التحقق من الصلاحيات على مستوى الكنترولر والـ middleware
- منع حذف الأدوار المرتبطة بمستخدمين
- منع المستخدم من حذف حسابه الشخصي

### 🔄 إدارة الكاش
- النظام يستخدم caching للصلاحيات لتحسين الأداء
- يتم تحديث الكاش تلقائياً عند تغيير الصلاحيات
- إعدادات الكاش قابلة للتخصيص في `config/permission.php`

### 📱 التوافق
- Laravel 10+
- PHP 8.1+
- MySQL 8.0+
- Bootstrap 5 / Tailwind CSS

## المطور
**Eng.Fahed** - مطور نظم معلومات متخصص في Laravel وأنظمة إدارة الموارد البشرية

## الدعم والصيانة
للدعم التقني أو طلب تطوير ميزات إضافية، يرجى التواصل مع المطور.

---

تم تطوير هذا النظام بأعلى معايير الجودة والأمان لضمان إدارة فعالة وآمنة لصلاحيات المستخدمين في نظام الموارد البشرية.
