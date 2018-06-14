@extends ('layouts.default')

@section ('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8 p-3">
            <header class="d-flex dropdown">
                <h5 class="d-block mt-2 mb-4">Entries</h5>
                <button class="btn page-link text-dark d-block align-self-start dropdown-toggle ml-auto" type="button" data-toggle="dropdown">{{ request()->query('kind') ?: 'Select Kind' }}</button>

                <div class="dropdown-menu dropdown-menu-right" style="height: auto;max-height: 200px;overflow-x: hidden;">
                    @foreach ($kindList as $kind)
                    <a class="dropdown-item" href="{{ route('index', array_merge(request()->query(), ['kind' => $kind['kind']])) }}">{{ $kind['kind'] }} ({{ $kind['count'] }})</a>
                    @endforeach
                </div>
            </header>
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
                    <a class="card-link text-nowrap" href="{{ route('entry', ['uuid' => $entry->uuid->first()]) }}">More detail</a>
                    @else
                    <div class="dropdown">
                        <a class="card-link text-nowrap dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                            More details
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @foreach ($entry->kind_of_info as $index => $kind)
                            <a class="dropdown-item" href="{{ route('entry', ['uuid' => $entry->uuid[$index]]) }}">
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
