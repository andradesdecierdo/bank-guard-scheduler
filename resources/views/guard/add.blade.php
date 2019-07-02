<form method="POST" name="add" action="{{ route('guard-add') }}">
    {{ csrf_field() }}
    <h1>Add Security Guard</h1>
    @if ($errors->add->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->add->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session()->has('save_success'))
        <div class="alert alert-success">
            {{ session()->get('save_success') }}
        </div>
    @endif
    <div class="row">
        <div style="position: relative" class="col col-sm-9">
            <strong>Name:</strong>
            <input name="name" value="{{ ($errors->add->any()) ? old('name') : '' }}" class="form-control" type="text">
        </div>
        <div style="position: relative" class="col col-sm-2">
            <strong>Color:</strong>
            <input name="color_indicator" value="{{ ($errors->add->any()) ? old('color_indicator') : '' }}" class="form-control" type="color">
        </div>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn btn-primary col-md-3">Submit</button>
    </div>
</form>
