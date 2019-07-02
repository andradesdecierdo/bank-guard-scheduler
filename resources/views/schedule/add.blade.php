<form method="POST" name="add" action="{{ route('schedule-add') }}">
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
    @if (session()->has('save_success'))
        <div class="alert alert-success">
            {{ session()->get('save_success') }}
        </div>
    @endif
    <div style="position: relative">
        <strong>Guard:</strong>
        <select name="guard_id" class="form-control form-control-lg">
            @foreach ($guards as $id => $name)
                <option value="{{ $id }}" {{ ($errors->add->any() && old('guard_id') == $id) ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>
    <div style="position: relative">
        <strong>Date:</strong>
        <input name="date" value="{{ ($errors->add->any()) ? old('date') : '' }}" class="date form-control" type="text">
    </div>
    <div class="row">
        <div style="position: relative" class="col">
            <strong>Start Time:</strong>
            <input name="start_time" value="{{ ($errors->add->any()) ? old('start_time') : '' }}" class="timepicker form-control" type="text">
        </div>
        <div style="position: relative" class="col">
            <strong>End Time:</strong>
            <input name="end_time" value="{{ ($errors->add->any()) ? old('end_time') : '' }}" class="timepicker form-control" type="text">
        </div>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn btn-primary col-md-3">Submit</button>
    </div>
</form>
