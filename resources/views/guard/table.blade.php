<h1>List of Security Guards</h1>
<table class="table table-bordered">
    <thead class="thead-light">
        <tr>
            <th>Name</th>
            <th>Color Indicator</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($guards as $guard)
            <tr>
                <td><a href="/schedule/{{$guard['id']}}">{{ $guard['name'] }}</a></td>
                <td style="background-color: {{ $guard['color_indicator'] }}"></td>
            </tr>
        @endforeach
    </tbody>
</table>
