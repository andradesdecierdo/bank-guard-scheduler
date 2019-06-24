<h1>Bank Security Guard Schedule</h1>
<div class="container" style="overflow-x: scroll; min-height: 100px">
    <table class="table-bordered">
        <tr>
            <td></td>
            @foreach ($dates as $key => $day)
                <td colspan={{ $dailyTimeFrameCount }} class="text-center">
                    {{ $day }}
                    {{-- Display an alert text if there is no guard scheduled --}}
                    @if (!$dateSecurityChecker[$key])
                        <p class="text-danger">The Bank is Unsecured! Please assign a security!</p>
                    @endif
                </td>
            @endforeach
        </tr>
        <tr>
            <td></td>
            @foreach ($totalTimeFrames as $timeFrame)
                <td>{{ $timeFrame }}</td>
            @endforeach
        </tr>
        @foreach ($guardSchedules as $guardSchedule)
            <tr>
                <td style="white-space: nowrap">{{ $guardSchedule['name'] }}</td>
                @foreach ($guardSchedule['schedules'] as $schedule)
                    {{-- Color the time frame if it is between the guard schedule --}}
                    <td @if ($schedule === true) style="background-color: {{ $guardSchedule['color_indicator'] }}" @endif></td>
                @endforeach
            </tr>
        @endforeach
    </table>
</div>
