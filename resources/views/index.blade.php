@extends ('layouts.default')

@section ('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8 p-3">
            <h5>Entries</h5>
            {{ $entries->links() }}
            @foreach ($entries as $entry)
            <div class="card">
                <div class="card-body">
                    @php
                        preg_match('/【(.*)】(.*)/', $entry->headline, $headline);
                    @endphp
                    <h5 class="card-title">{{ $headline[1] }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $entry->updated }} / {{ $entry->observatory_name }}</h6>
                    <p class="card-text">{{ $headline[2] }}</p>
                    <a class="card-link text-right" href="{{ $entry->url }}">More detail</a>
                </div>
            </div>
            @endforeach
            {{ $entries->links() }}
        </div>
    </div>
</div>
@endsection
