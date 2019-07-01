@extends ('layout.main')
@section ('content')
    <div class="col-sm-6">
        @include('guard.add')
    </div>
    <div class="col-sm-6">
        @include('guard.delete')
    </div>
    <div class="col-sm-12 mt-5">
        <div class="w-50">
            @include('guard.table')
        </div>
    </div>
@endsection
