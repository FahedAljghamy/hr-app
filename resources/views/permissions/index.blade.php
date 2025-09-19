{{-- 
Author: Eng.Fahed
Permissions Index View - HR System
عرض قائمة الصل{{ trans('messages.Permissions') }} مع إمكان{{ trans('messages.Search') }}�
--}}

@extends('layouts.app')

@section('title', trans('messages.Permissions') . '')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Permissions') }}</h1>
    <div>
        @can('permissions.create')
        <a href="{{ route('permissions.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{ trans('messages.Add New Permission') }}
        </a>
        <button class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" onclick="openBulkCreateModal()">
            <i class="fas fa-layer-group fa-sm text-white-50"></i> إنشاء متعدد
        </button>
        @endcan
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('info'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="fas fa-info-circle"></i>
    {{ session('info') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- Content Row -->
<div class="row">
    <!-- Permissions Overview Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            إجمالي الصل{{ trans('messages.Permissions') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $permissions->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-key fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            المجموعات</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $groupedPermissions->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            الأدو{{ trans('messages.roles') }} المرتبطة</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \Spatie\Permission\Models\Role::count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            المستخدمون</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\User::count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grouped Permissions Cards -->
@if($groupedPermissions->count() > 0)
<div class="row">
    @foreach($groupedPermissions as $groupName => $groupPermissions)
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-layer-group mr-2"></i>
                        {{ getGroupDisplayName($groupName) }}
                    </h6>
                <span class="badge badge-primary badge-pill">{{ $groupPermissions->count() }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($groupPermissions->take(6) as $permission)
                    <div class="col-md-6 mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-key fa-sm text-success mr-2"></i>
                            <span class="text-sm">{{ getPermissionDisplayName($permission->name) }}</span>
                            <span class="badge badge-secondary badge-sm ml-auto">{{ $permission->roles->count() }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($groupPermissions->count() > 6)
                <div class="text-center mt-2">
                    <small class="text-muted">و {{ $groupPermissions->count() - 6 }} صل{{ trans('messages.Permissions') }} أخرى...</small>
                </div>
                @endif

                <div class="mt-3">
                    <button class="btn btn-outline-primary btn-sm" onclick="viewGroupPermissions('{{ $groupName }}')">
                        <i class="fas fa-eye"></i> عرض الكل
                    </button>
                    @can('permissions.create')
                    <button class="btn btn-outline-success btn-sm" onclick="createGroupPermissions('{{ $groupName }}')">
                        <i class="fas fa-plus"></i> �{{ trans('messages.Permissions') }}
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">قائمة الصل{{ trans('messages.Permissions') }} التفصيلية</h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                @can('permissions.create')
                <a class="dropdown-item" href="{{ route('permissions.create') }}">
                    <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Add New Permission') }}
                </a>
                <a class="dropdown-item" href="#" onclick="openBulkCreateModal()">
                    <i class="fas fa-layer-group fa-sm fa-fw mr-2 text-gray-400"></i>
                    إنشاء متعدد
                </a>
                @endcan
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="window.print()">
                    <i class="fas fa-print fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Print') }}
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($permissions->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>اسم الصلاحية</th>
                        <th>النوع</th>
                        <th>الأدو{{ trans('messages.roles') }} المرتبطة</th>
                        <th>ت{{ trans('messages.roles') }}يخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <div class="icon-circle bg-success">
                                        <i class="fas fa-key text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-weight-bold">{{ $permission->name }}</div>
                                    <div class="text-muted small">صلاحية نظام</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $groupName = explode('.', $permission->name)[0];
                            @endphp
                            <span class="badge badge-info">
                                {{ getGroupDisplayName($groupName) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap">
                                @forelse($permission->roles->take(3) as $role)
                                    <span class="badge badge-primary mr-1 mb-1">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted small">غير مسند</span>
                                @endforelse
                                
                                @if($permission->roles->count() > 3)
                                    <span class="badge badge-secondary">+{{ $permission->roles->count() - 3 }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">{{ $permission->created_at->format('Y/m/d') }}</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('permissions.view')
                                <a href="{{ route('permissions.show', $permission) }}" 
                                   class="btn btn-info btn-sm" title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('permissions.edit')
                                <a href="{{ route('permissions.edit', $permission) }}" 
                                   class="btn btn-warning btn-sm" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('permissions.delete')
                                <form method="POST" action="{{ route('permissions.destroy', $permission) }}" 
                                      class="d-inline"
                                      onsubmit="return confirm('هل أنت م{{ trans('messages.Confirm Password') }}د من حذف هذه الصلاحية؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($permissions->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $permissions->firstItem() }} to {{ $permissions->lastItem() }} of {{ $permissions->total() }} results
                </div>
                <div>
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-4">
            <div class="mb-3">
                <i class="fas fa-key fa-3x text-gray-300"></i>
            </div>
            <h5 class="text-gray-600">�{{ trans('messages.Permissions') }}</h5>
            <p class="text-muted">{{ trans('messages.Start by creating new permissions for the system') }}</p>
            @can('permissions.create')
            <a href="{{ route('permissions.create') }}" class="btn btn-primary mr-2">
                <i class="fas fa-plus"></i> {{ trans('messages.Add New Permission') }}
            </a>
            <button class="btn btn-success" onclick="openBulkCreateModal()">
                <i class="fas fa-layer-group"></i> إنشاء متعدد
            </button>
            @endcan
        </div>
        @endif
    </div>
</div>

{{-- Bulk Create Modal --}}
@can('permissions.create')
<div class="modal fade" id="bulkCreateModal" tabindex="-1" role="dialog" aria-labelledby="bulkCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkCreateModalLabel">إنشاء صل{{ trans('messages.Permissions') }} متعددة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('permissions.bulk-create') }}" id="bulkCreateForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bulk_type">نوع الصل{{ trans('messages.Permissions') }}</label>
                        <select name="type" id="bulk_type" class="form-control" required>
                            <option value="">اختر النوع</option>
                            <option value="users">{{ trans('messages.User Management') }}</option>
                            <option value="employees">�{{ trans('messages.Employees') }}</option>
                            <option value="attendance">الحضور والانصراف</option>
                            <option value="leaves">�{{ trans('messages.Leaves') }}</option>
                            <option value="payroll">�{{ trans('messages.Payroll') }}</option>
                            <option value="reports">التق{{ trans('messages.roles') }}ير</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>الأعمال المطلوبة</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="view" id="action_view">
                                    <label class="form-check-label" for="action_view">عرض</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="create" id="action_create">
                                    <label class="form-check-label" for="action_create">إنشاء</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="edit" id="action_edit">
                                    <label class="form-check-label" for="action_edit">تعديل</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="actions[]" value="delete" id="action_delete">
                                    <label class="form-check-label" for="action_delete">حذف</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">إنشاء الصل{{ trans('messages.Permissions') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
// Author: Eng.Fahed
// JavaScript for permissions management

// فتح modal للإنشاء المتعدد
function openBulkCreateModal() {
    $('#bulkCreateModal').modal('show');
}

// عرض صل{{ trans('messages.Permissions') }} مجموعة معينة
function viewGroupPermissions(groupName) {
    // يمكن تطوير هذه الوظيفة لعرض modal أو صفحة منفصلة
    alert(`عرض صل{{ trans('messages.Permissions') }} مجموعة: ${groupName}`);
}

// إنشاء صل{{ trans('messages.Permissions') }} لمجموعة معينة
function createGroupPermissions(groupName) {
    $('#bulk_type').val(groupName);
    openBulkCreateModal();
}

// Auto-hide success alerts after 5 seconds
$(document).ready(function() {
    setTimeout(function() {
        $('.alert-success, .alert-info').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

