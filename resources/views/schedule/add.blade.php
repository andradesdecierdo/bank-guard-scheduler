<form method="POST" action="/schedule">
    {{ csrf_field() }}
    <h1>Roster Security Guard</h1>
    @if ($errors->add->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->add->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session()->has('save_success'))
        <div class="alert alert-success">
            {{ session()->get('save_success') }}
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
    <div class="container">
        <div style="position: relative">
            <strong>Start Time:</strong>
            <input name="start_time" class="timepicker form-control" type="text">
        </div>
        <div style="position: relative">
            <strong>End Time:</strong>
            <input name="end_time" class="timepicker form-control" type="text">
        </div>
    </div>
    <div class="container col-md-6">
        <button type="submit" class="btn btn-primary col-md-3">Submit</button>
    </div>
</form>
