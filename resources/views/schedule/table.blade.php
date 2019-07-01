<h1>Bank Security Guard Schedule</h1>
<div style="overflow-x: scroll; min-height: 100px">
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th></th>
                @foreach ($dates as $key => $day)
                    <th colspan={{ $dailyTimeFrameCount }} class="text-center">
                        {{ $day }}
                        {{-- Display an alert text if there is no guard scheduled --}}
                        @if (!$dateSecurityChecker[$key])
                            <p class="text-danger">The Bank is Unsecured! Please assign a security guard!</p>
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                @foreach ($totalTimeFrames as $timeFrame)
                    <td>{{ $timeFrame }}</td>
                @endforeach
            </tr>
            @foreach ($guardSchedules as $guardSchedule)
                <tr>
                    <td style="white-space: nowrap">
                        <a href="/schedule/{{$guardSchedule['id']}}">{{ $guardSchedule['name'] }}</a>
                    </td>
                    @foreach ($guardSchedule['schedules'] as $schedule)
                        {{-- Color the time frame if it is between the guard schedule --}}
                        <td @if ($schedule === true) style="background-color: {{ $guardSchedule['color_indicator'] }}" @endif></td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
