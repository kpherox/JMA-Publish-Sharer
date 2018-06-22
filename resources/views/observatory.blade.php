@extends ('index')
@section ('title', $observatory)
@section ('route', 'observatory')
@section ('observatory', $observatory)

@section ('sidebar')
<sidebar class="col-lg-3 col-xl-4 p-3">
    <div class="card">
        <h5 class="card-header bg-transparent d-flex">
            <span class="text-truncate">
                発表機関一覧（最終更新順）
            </span>
        </h5>

        <div class="list-group list-group-flush">
            @foreach ($observatories as $obs)
                @if ($observatory !== $obs->observatory_name)
                    <a class="list-group-item" href="{{ route('observatory', ['observatory' => $obs->observatory_name]) }}">{{ $obs->observatory_name }} ({{ $obs->count }})</a>
                @else
                    <a class="list-group-item active" href="{{ route('observatory', ['observatory' => $obs->observatory_name]) }}">{{ $obs->observatory_name }} ({{ $obs->count }})</a>
                @endif
            @endforeach
        </div>
    </div>
</sidebar>
@endsection

