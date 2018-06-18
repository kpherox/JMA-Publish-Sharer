@extends ('layouts.default')

@section ('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8 p-3">
            <header class="d-flex dropdown">
                <h5 class="d-block mt-2 mb-4">Entries</h5>
                <button class="btn page-link text-dark d-block align-self-start dropdown-toggle ml-auto" type="button" data-toggle="dropdown">{{ $queries->get('kind') ?: 'Select Kind' }}</button>

                <div class="dropdown-menu dropdown-menu-right" style="height: auto;max-height: 200px;overflow-x: hidden;">
                    <a class="dropdown-item" href="{{ route('index', $queries->forget('kind')->all()) }}">Select Kind</a>
                    @foreach ($kindList as $kind)
                    <a class="dropdown-item" href="{{ route('index', $queries->merge(['kind' => $kind['kind']])->all()) }}">{{ $kind['kind'] }} ({{ $kind['count'] }})</a>
                    @endforeach
                </div>
            </header>
            {{ $paginateLinks }}
            @foreach ($entries as $entry)
            <div class="card">
                <h5 class="card-header bg-transparent d-flex">
                    <span class="text-truncate">
                        {{ $entry->children_kinds->implode(' / ') }}
                    </span>
                </h5>

                <div class="card-body">
                    <h5 class="card-title">{{ $entry->parsed_headline['title'] }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">分類種別: @lang('feedtypes.'.$entry->feed->type)</h6>
                    <h6 class="card-subtitle mb-2 text-muted">発信時刻: @datetime($entry->updated)</h6>
                    <h6 class="card-subtitle mb-2 text-muted">発表機関: {{ $entry->observatory_name }}</h6>
                    @if (!empty($entry->parsed_headline['headline']))
                    <p class="card-text px-1">{{ $entry->parsed_headline['headline'] }}</p>
                    @endif
                </div>

                <div class="card-footer bg-transparent d-flex border-light">
                    <span class="mr-auto"></span>
                    @if ($entry->entryDetail->count() === 1)
                    <a class="card-link text-nowrap" href="{{ route('entry', ['uuid' => $entry->entryDetail->first()->uuid]) }}">More detail</a>
                    @else
                    <div class="dropdown">
                        <a class="card-link text-nowrap dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                            More details
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @foreach ($entry->entryDetail->sortByKind() as $detail)
                            <a class="dropdown-item" href="{{ route('entry', ['uuid' => $detail->uuid]) }}">
                                {{ $detail->kind_of_info }}
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
