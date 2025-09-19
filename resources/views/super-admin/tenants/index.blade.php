@extends('super-admin.layout')

@section('title', __('Tenants Management'))

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('Tenants Management') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Tenants') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('All Tenants') }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> {{ __('Add New Tenant') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tenantsTable">
                                <thead>
                                    <tr>
                                        <th>{{ __('ID') }}</th>
                                        <th>{{ __('Company Name') }}</th>
                                        <th>{{ __('Domain') }}</th>
                                        <th>{{ __('Contact Email') }}</th>
                                        <th>{{ __('Plan') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Employees') }}</th>
                                        <th>{{ __('Subscription End') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tenants as $tenant)
                                    <tr>
                                        <td>{{ $tenant->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($tenant->logo)
                                                    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->company_name }}" class="img-circle img-size-32 mr-2">
                                                @else
                                                    <div class="img-circle img-size-32 bg-primary d-flex align-items-center justify-content-center mr-2">
                                                        <i class="fas fa-building text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $tenant->company_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $tenant->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="http://{{ $tenant->domain }}" target="_blank" class="text-primary">
                                                {{ $tenant->domain }}
                                            </a>
                                        </td>
                                        <td>{{ $tenant->contact_email }}</td>
                                        <td>
                                            <span class="badge badge-{{ $tenant->subscription_plan == 'enterprise' ? 'success' : ($tenant->subscription_plan == 'premium' ? 'warning' : 'info') }}">
                                                {{ ucfirst($tenant->subscription_plan) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $tenant->status == 'active' ? 'success' : ($tenant->status == 'inactive' ? 'secondary' : 'danger') }}">
                                                {{ ucfirst($tenant->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $tenant->users_count ?? 0 }}/{{ $tenant->max_employees }}</span>
                                        </td>
                                        <td>
                                            @if($tenant->subscription_end_date < now())
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> {{ __('Expired') }}
                                                </span>
                                            @elseif($tenant->subscription_end_date < now()->addDays(7))
                                                <span class="text-warning">
                                                    <i class="fas fa-clock"></i> {{ $tenant->subscription_end_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-success">{{ $tenant->subscription_end_date->format('M d, Y') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-info btn-sm" title="{{ __('View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-warning btn-sm" title="{{ __('Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('super-admin.tenants.destroy', $tenant) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this tenant?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="{{ __('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-building fa-3x mb-3"></i>
                                            <br>
                                            {{ __('No tenants found') }}
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($tenants->hasPages())
                    <div class="card-footer">
                        {{ $tenants->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#tenantsTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[0, "desc"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
        }
    });
});
</script>
@endsection

