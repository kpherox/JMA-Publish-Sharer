@extends ('entry')

@section ('moredetails')
<div class="card">
    <div class="card-body">
        @formatIppanho (data_get($entry, 'Body.Comment.Text', ''))
    </div>
</div>
@endsection

