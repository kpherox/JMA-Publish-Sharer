@extends ('layouts.default')

@section ('content')
<div class="container">
    <div class="row justify-content-center">
        <header class="d-sm-flex col-lg-9 col-xl-8 px-3 mb-3">
            <h5 class="text-truncate d-block my-2 mr-auto px-2">@yield('title', 'Entries')</h5>

            <div class="dropdown align-self-start">
                <button class="btn page-link text-dark dropdown-toggle" type="button" data-toggle="dropdown">{{ $typeOrKind }}</button>

                <div class="dropdown-menu dropdown-menu-right" style="width:280px;max-width:80vw;height:auto;max-height:500px;overflow-x:auto">
                    <a class="dropdown-item" href="{{ $routeUrl }}">All Type/Kind</a>

                    <div class="dropdown-divider"></div>

                    <transition-group tag="div" class="feed-list" style="display:none" v-show="true">
                        <a v-for="(feed, index) in feeds" :key="feed"
                           class="dropdown-item d-flex justify-content-between align-items-center"
                           :class="{'active': feed.type === selected}"
                           :href="route+'?type='+feed.type">
                            <span class="text-nowrap text-truncate mr-1">@{{ feed.transed_type }}</span>
                            <span class="badge badge-pill"
                                  :class="feed.type === selected ? 'badge-light' : 'badge-primary'">
                                @{{ feed.entries_count }}
                            </span>
                        </a>
                    </transition-group>

                    <div class="dropdown-divider"></div>

                    <input class="dropdown-item" style="z-index:2" v-model="kindName" placeholder="Search kind">
                    <transition-group tag="div" class="kind-list" style="display:none" v-show="true">
                        <a v-for="(kind, index) in kinds" :key="kind"
                           v-if="kind.kind_of_info.match(new RegExp(kindName))"
                           class="dropdown-item d-flex justify-content-between align-items-center"
                           :class="{'active': index === parseInt(selected)}"
                           :href="route+'?kind='+kind.kind_of_info">
                            <span class="text-nowrap text-truncate mr-1">@{{ kind.kind_of_info }}</span>
                            <span class="badge badge-pill"
                                  :class="index === parseInt(selected) ? 'badge-light' : 'badge-primary'">
                                @{{ kind.count }}
                            </span>
                        </a>
                    </transition-group>
                </div>
            </div>
        </header>

        <main class="col-lg-9 col-xl-8">
            {{ $entries->links('components.index-pagination') }}

            @foreach ($entries as $entry)
            <div class="card">
                <h5 class="card-header bg-transparent d-flex">
                    <span class="text-truncate">
                        {{ $entry->children_kinds->implode(' / ') }}
                    </span>
                </h5>

                <div class="card-body">
                    <h5 class="card-title">{{ $entry->parsed_headline['title'] }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">@lang('feedtypes.name'): {{ $entry->feed->transed_type }}</h6>
                    <h6 class="card-subtitle mb-2 text-muted">発信時刻: @datetime($entry->updated)</h6>
                    <h6 class="card-subtitle mb-2 text-muted">
                        発表機関:
                            @foreach (preg_split( "/( |　)/", $entry->observatory_name) as $observatoryName)
                                @if ($loop->index > 0) > @endif
                                @if ($__env->yieldContent('observatory') !== $observatoryName)
                                    <a href="{{ route('observatory', ['observatory' => $observatoryName]) }}">{{ $observatoryName }}</a>
                                @else
                                    {{ $observatoryName }}
                                @endif
                            @endforeach
                    </h6>
                    @if ($entry->event_id)
                    <h6 class="card-subtitle mb-2 text-muted">Event ID: <a href="{{ route('event', ['id' => $entry->event_id]) }}">{{ $entry->event_id }}</a></h6>
                    @endif

                    @if ($entry->parsed_headline->has('headline'))
                        @formatToHTML ($entry->parsed_headline['headline'])
                    @endif
                </div>

                <div class="card-footer bg-transparent d-flex border-light">
                    <span class="mr-auto"></span>
                    @if ($entry->entryDetails()->count() === 1)
                    <a class="card-link text-nowrap" href="{{ $entry->entryDetails()->first()->entry_page_url }}">More detail</a>
                    @else
                    <div class="dropdown">
                        <a class="card-link text-nowrap dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                            More details
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @foreach ($entry->entryDetails->sortByKind() as $detail)
                            <a class="dropdown-item" href="{{ $detail->entry_page_url }}">
                                {{ $detail->kind_of_info }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach

            {{ $entries->links('components.index-pagination') }}
        </main>

        @yield ('sidebar')
    </div>
</div>

<script>
Object.assign(mix.data, {
    route: '{{ $routeUrl }}',
    feeds: @json ($feeds),
    kinds: @json ($kindList),
    selected: '{{ $selected }}',
    kindName: '',
});
</script>
@endsection
