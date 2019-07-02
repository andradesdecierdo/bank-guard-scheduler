<form method="POST" name="delete" action="{{ route('guard-delete') }}">
    @method('delete')
    {{ csrf_field() }}
    <h1>Delete Security Guard</h1>
    @if ($errors->delete->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->delete->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session()->has('delete_success'))
        <div class="alert alert-success">
            {{ session()->get('delete_success') }}
        </div>
    @endif
    <strong>Guard:</strong>
    <select name="guard_id" class="form-control form-control-lg">
        @foreach ($guards as $guard)
            <option value="{{ $guard['id'] }}" {{ ($errors->delete->any() && old('guard_id') == $id) ? 'selected' : '' }}>
                {{ $guard['name'] }}
            </option>
        @endforeach
    </select>
    <div class="mt-3">
        <button type="submit" class="btn btn-danger col-md-3">Delete</button>
    </div>
</form>
