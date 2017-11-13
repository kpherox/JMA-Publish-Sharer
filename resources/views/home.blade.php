@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>

            @php
                $social = auth()->user()->accounts();
            @endphp
            @if ($social->exists())
                <div class="panel panel-default">
                    <div class="panel-heading">Social Accounts</div>

                    <div class="panel-body">
                        <dl class="dl-horizontal">
                            @foreach ($social->get() as $socialAccount)
                                <dt>{{ $socialAccount->provider_name }}</dt>
                                <dd>
                                    <img class="align-middle" src="{{ $socialAccount->account_avatar }}" alt="{{ $socialAccount->provider_name }} Icon" />
                                    {{ $socialAccount->account_name }}
                                </dd>
                            @endforeach
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
