@extends ('layout.main')
@section ('content')
    <div class="col-sm-6">
        @include('schedule.add')
    </div>
    <div class="col-sm-6">
        @include('schedule.delete')
    </div>
    <div class="col-sm-12 mt-5">
        @include('schedule.table')
    </div>
    <script type="text/javascript">
        $('.timepicker').datetimepicker({
            format: 'HH:mm',
            stepping: 30,
        });

        let dateNow = new Date();
        dateNow.setDate(dateNow.getDate());
        $('.date').datepicker({
            todayHighlight: 'TRUE',
            format: 'yyyy-mm-dd',
            startDate: dateNow
        });
    </script>
@endsection
