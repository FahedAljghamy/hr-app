{{-- 
Author: Eng.Fahed
Roles Permissions Map View - HR System
خريطة شاملة لربط الأدو{{ trans('messages.roles') }} بالصل{{ trans('messages.Permissions') }}
--}}

@extends('layouts.app')

@section('title', 'Roles & Permissions Map')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Roles & Permissions Map</h1>
        <p class="text-muted">Complete overview of roles and their assigned permissions</p>
    </div>
    <div>
        <a href="{{ route('roles.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Roles
        </a>
        <button class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" onclick="exportMap()">
            <i class="fas fa-download fa-sm text-white-50"></i> Export Map
        </button>
    </div>
</div>

<!-- Roles Overview Cards -->
<div class="row mb-4">
    @php
        $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
        $totalPermissions = \Spatie\Permission\Models\Permission::count();
    @endphp
    
    @foreach($roles as $index => $role)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-{{ ['primary', 'success', 'info', 'warning', 'danger'][$index % 5] }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ ['primary', 'success', 'info', 'warning', 'danger'][$index % 5] }} text-uppercase mb-1">
                            {{ $role->name }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $role->permissions->count() }}</div>
                        <div class="text-xs text-muted">permissions assigned</div>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar bg-{{ ['primary', 'success', 'info', 'warning', 'danger'][$index % 5] }}" 
                                 role="progressbar" 
                                 style="width: {{ $totalPermissions > 0 ? ($role->permissions->count() / $totalPermissions) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Permissions Matrix -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>Permissions Matrix
        </h6>
    </div>
    <div class="card-body">
        @php
            $permissions = \Spatie\Permission\Models\Permission::all();
            $groupedPermissions = $permissions->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });
        @endphp
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="permissionsMatrix">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="width: 200px;">Permission</th>
                        @foreach($roles as $role)
                            <th class="text-center" style="min-width: 120px;">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-user-shield mb-1"></i>
                                    <span class="font-weight-bold">{{ $role->name }}</span>
                                    <small class="text-muted">{{ $role->permissions->count() }} perms</small>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedPermissions as $groupName => $groupPermissions)
                        <!-- Group Header -->
                        <tr class="bg-light">
                            <td colspan="{{ $roles->count() + 1 }}" class="font-weight-bold text-primary">
                                <i class="fas fa-folder mr-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $groupName)) }} Module
                            </td>
                        </tr>
                        
                        <!-- Permissions in Group -->
                        @foreach($groupPermissions as $permission)
                        <tr>
                            <td class="font-weight-medium">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-key text-success mr-2"></i>
                                    <div>
                                        <div class="font-weight-bold">{{ $permission->name }}</div>
                                        <small class="text-muted">{{ ucfirst(str_replace(['.', '_'], [' - ', ' '], $permission->name)) }}</small>
                                    </div>
                                </div>
                            </td>
                            @foreach($roles as $role)
                                <td class="text-center">
                                    @if($role->hasPermissionTo($permission->name))
                                        <i class="fas fa-check-circle text-success fa-lg" title="Has Permission"></i>
                                        <div class="text-xs text-success mt-1">Granted</div>
                                    @else
                                        <i class="fas fa-times-circle text-danger fa-lg" title="No Permission"></i>
                                        <div class="text-xs text-danger mt-1">Denied</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Role Details Cards -->
<div class="row">
    @foreach($roles as $index => $role)
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-{{ ['primary', 'success', 'info', 'warning', 'danger'][$index % 5] }}">
                    <i class="fas fa-user-shield mr-2"></i>{{ $role->name }}
                </h6>
                <div>
                    <span class="badge badge-{{ ['primary', 'success', 'info', 'warning', 'danger'][$index % 5] }} badge-pill">
                        {{ $role->permissions->count() }} permissions
                    </span>
                    <span class="badge badge-secondary badge-pill ml-1">
                        {{ $role->users()->count() }} users
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $roleGroupedPermissions = $role->permissions->groupBy(function ($permission) {
                            return explode('.', $permission->name)[0];
                        });
                    @endphp
                    
                    @foreach($roleGroupedPermissions as $groupName => $groupPermissions)
                    <div class="col-md-6 mb-3">
                        <h6 class="text-{{ ['primary', 'success', 'info', 'warning', 'danger'][$index % 5] }} mb-2">
                            <i class="fas fa-folder-open mr-1"></i>{{ ucfirst($groupName) }}
                        </h6>
                        @foreach($groupPermissions as $permission)
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-check text-success mr-2"></i>
                            <span class="text-sm">{{ explode('.', $permission->name)[1] }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between">
                        @can('roles.edit')
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-{{ ['primary', 'success', 'info', 'warning', 'danger'][$index % 5] }} btn-sm">
                            <i class="fas fa-edit mr-1"></i>Edit Role
                        </a>
                        @endcan
                        
                        @can('roles.view')
                        <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eye mr-1"></i>View Details
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Permission Groups Summary -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-layer-group mr-2"></i>Permission Groups Summary
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($groupedPermissions as $groupName => $groupPermissions)
            <div class="col-md-4 mb-3">
                <div class="card border-left-info h-100">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="font-weight-bold text-info mb-1">{{ ucfirst($groupName) }}</h6>
                                <p class="text-muted mb-0 small">{{ $groupPermissions->count() }} permissions</p>
                            </div>
                            <div class="text-right">
                                @php
                                    $groupUsageCount = 0;
                                    foreach($roles as $role) {
                                        foreach($groupPermissions as $perm) {
                                            if($role->hasPermissionTo($perm->name)) {
                                                $groupUsageCount++;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $groupUsageCount }}</div>
                                <small class="text-muted">roles using</small>
                            </div>
                        </div>
                        
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $roles->count() > 0 ? ($groupUsageCount / $roles->count()) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Author: Eng.Fahed
// JavaScript for permissions matrix

$(document).ready(function() {
    // Initialize DataTable for the matrix
    $('#permissionsMatrix').DataTable({
        "paging": false,
        "searching": true,
        "ordering": false,
        "info": false,
        "scrollX": true,
        "fixedColumns": {
            leftColumns: 1
        }
    });
    
    // Add hover effects to matrix cells
    $('#permissionsMatrix tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );
});

// Export map function
function exportMap() {
    // Create a simple export of the current permissions map
    const mapData = {
        roles: [],
        permissions: [],
        matrix: {}
    };
    
    // Collect roles data
    @foreach($roles as $role)
    mapData.roles.push({
        name: '{{ $role->name }}',
        permissions_count: {{ $role->permissions->count() }},
        users_count: {{ $role->users()->count() }}
    });
    @endforeach
    
    // Collect permissions data
    @foreach($permissions as $permission)
    mapData.permissions.push('{{ $permission->name }}');
    @endforeach
    
    // Create matrix
    @foreach($roles as $role)
    mapData.matrix['{{ $role->name }}'] = [
        @foreach($permissions as $permission)
        {{ $role->hasPermissionTo($permission->name) ? 'true' : 'false' }},
        @endforeach
    ];
    @endforeach
    
    // Download as JSON
    const dataStr = JSON.stringify(mapData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'roles-permissions-map.json';
    link.click();
}
</script>
@endpush

@push('styles')
<style>
/* Author: Eng.Fahed */
.progress-sm {
    height: 0.5rem;
}

.table th {
    border-top: none;
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}

#permissionsMatrix {
    font-size: 0.875rem;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

/* Fixed columns styling */
.dataTables_scrollBody {
    border-left: 1px solid #dee2e6;
}

/* Hover effects */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}

.table-active {
    background-color: rgba(0,123,255,.1) !important;
}
</style>
@endpush
