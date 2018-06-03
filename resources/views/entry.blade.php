@extends ('layouts.default')

@section ('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8 p-3">
            <h5>Entry</h5>
            <div class="card">
                <h5 class="card-header bg-transparent">{{ $entry['Head']['InfoKind'] }}</h5>
                <div class="card-body">
                    <h5 class="card-title">{{ $entry['Head']['Title'] }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">@datetime($entry['Head']['ReportDateTime']) / {{ $entry['Control']['PublishingOffice'] }}</h6>
                    @if (!empty($entry['Head']['Headline']['Text']))
                        <p class="card-text px-1">{{ $entry['Head']['Headline']['Text'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
