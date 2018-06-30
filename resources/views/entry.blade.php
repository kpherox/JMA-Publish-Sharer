@extends ('layouts.default')
@section ('title', data_get($entry, 'Head.Title'))

@section ('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8 p-3">
            <header class="d-flex">
                <h5 class="d-block mt-2 mb-4 mr-auto">Entry</h5>
            </header>

            <div class="card">
                <h5 class="card-header bg-transparent d-flex">
                    <a class="mr-auto text-truncate text-body" href="{{ route('index', ['kind' => data_get($entry, 'Control.Title')]) }}">{{ data_get($entry, 'Control.Title') }}</a>
                    <a class="card-link text-nowrap" href="{{ route('entry.xml', ['uuid' => $entryUuid]) }}">Xml file</a>
                    <a class="card-link text-nowrap" href="{{ route('entry.json', ['uuid' => $entryUuid]) }}">Json file</a>
                </h5>

                <div class="card-body">
                    <h5 class="card-title">{{ data_get($entry, 'Head.Title') }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">分類種別: <a href="{{ route('index', ['type' => $feed->type]) }}">@lang('feedtypes.'.$feed->type)</a></h6>
                    <h6 class="card-subtitle mb-2 text-muted">発信時刻: @datetime(data_get($entry, 'Control.DateTime'))</h6>
                    <h6 class="card-subtitle mb-2 text-muted">
                        発表機関:
                        @foreach (explode('　', data_get($entry, 'Control.PublishingOffice', '')) as $observatoryName)
                            @if (!$loop->first) > @endif
                            <a href="{{ route('observatory', ['observatory' => $observatoryName]) }}">{{ $observatoryName }}</a>
                        @endforeach
                    </h6>

                    @if (data_get($entry, 'Head.Headline.Text', null))
                    <p class="card-text px-1">{{ data_get($entry, 'Head.Headline.Text') }}</p>
                    @endif
                </div>
            </div>

            @yield ('moredetails')
        </div>
    </div>
</div>
@endsection
