<form method="POST" action="/schedule">
    @method('delete')
    {{ csrf_field() }}
    <h1>Remove Security Guard Roster</h1>
    @if ($errors->delete->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->delete->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session()->has('delete_success'))
        <div class="alert alert-success">
            {{ session()->get('delete_success') }}
        </div>
    @endif
    <div class="container" style="position: relative">
        <strong>Guard:</strong>
        <select name="guard_id" class="form-control">
            @foreach ($guards as $guard)
                <option value="{{ $guard['id'] }}">{{ $guard['name'] }}</option>
            @endforeach
        </select>
    </div>
    <div class="container" style="position: relative">
        <strong>Date:</strong>
        <input name="date" class="date form-control" type="text">
    </div>
    <div class="container col-md-6">
        <button type="submit" class="btn btn-danger col-md-3">Delete</button>
    </div>
</form>
