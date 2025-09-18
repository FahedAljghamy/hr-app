<x-card :title="$title" :icon="$icon" :color="$color">
    <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tfoot>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </tfoot>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
</x-card>

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "language": {
            @if(app()->getLocale() == 'ar')
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
            @endif
        }
    });
});
</script>
@endpush