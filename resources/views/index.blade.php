@extends ('layouts.default')

@section ('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8 p-3">
            <h5>Entries</h5>
            {{ $paginateLinks }}
            @foreach ($entries as $entry)
            <div class="card">
                <h5 class="card-header bg-transparent d-flex">
                    <span class="text-truncate">
                        {{ $entry->kind_of_info->implode(' / ') }}
                    </span>
                </h5>

                <div class="card-body">
                    <h5 class="card-title">{{ $entry->headline['title'] }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $entry->updated }} / {{ $entry->observatory_name }}</h6>
                    @if (!empty($entry->headline['headline']))
                    <p class="card-text px-1">{{ $entry->headline['headline'] }}</p>
                    @endif
                </div>

                <div class="card-footer bg-transparent d-flex border-light">
                    <span class="mr-auto"></span>
                    @if ($entry->uuid->count() === 1)
                    <a class="card-link text-nowrap" href="{{ route('entry', ['uuid' => $entry->uuid->first()]) }}" data-original-href="{{ $entry->url->first() }}">More detail</a>
                    @else
                    <div class="dropdown">
                        <a class="card-link text-nowrap dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                            More details
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @foreach ($entry->kind_of_info as $kind)
                            <a class="dropdown-item" href="{{ route('entry', ['uuid' => $entry->uuid->first()]) }}" data-original-href="{{ $entry->url }}">
                                {{ $kind }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
            {{ $paginateLinks }}
        </div>
    </div>
</div>
@endsection
