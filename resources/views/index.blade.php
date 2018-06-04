@extends ('layouts.default')

@section ('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8 p-3">
            <h5>Entries</h5>
            {{ $paginateLinks }}
            @foreach ($entries as $entry)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $entry->headline['title'] }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $entry->updated }} / {{ $entry->observatory_name }}</h6>
                    <p class="card-text">{{ $entry->headline['headline'] }}</p>
                    <a class="card-link" href="{{ route('entry', ['uuid' => $entry->uuid]) }}" data-original-href="{{ $entry->url }}">More detail</a>
                </div>
            </div>
            @endforeach
            {{ $paginateLinks }}
        </div>
    </div>
</div>
@endsection
