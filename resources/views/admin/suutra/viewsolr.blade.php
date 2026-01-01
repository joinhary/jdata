@extends('admin/layouts/default')
@section('title')
Thống kê @parent
@stop
@section('header_styles')
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->

<style>
    #progress-container {
        width: 100%;
        max-width: 600px;
        margin: 20px auto;
    }

    .progress-bar {
        transition: width 0.6s ease;
        font-size: 16px;
        line-height: 30px;
    }
</style>
@endsection
@section('content')
<form id="sync-form" method="POST">
    @csrf
    <h3>Chọn khoảng thời gian công chứng hồ sơ cần cập nhật</h3>
    <label>Từ ngày:</label>
    <input type="date" name="from_date" required>

    <label>Đến ngày:</label>
    <input type="date" name="to_date" required>

    <button type="submit" class="btn btn-success" id="progress-button">Cập nhật theo ngày</button>
</form>
<div id="log-output" class="mt-3 p-3 bg-light border rounded" style="max-height: 600px; overflow-y: auto; font-family: monospace;">
    <!-- Log sẽ hiển thị ở đây -->
</div>

<div id="progress-container" style="display:none;">
    <div class="progress">
        <div id="progress-bar" class="progress-bar bg-success" style="width:0%">Vui lòng chờ tiến trình cập nhật hoàn tất...</div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#sync-form').on('submit', function(e) {
        e.preventDefault();

        $('#progress-container').show();
        $('#progress-bar')
            .css({
                'width': '0%',
                'background-color': '#ffa500',
                'color': 'green'
            })
            .text('Vui lòng chờ tiến trình cập nhật hoàn tất...');


        let formData = $(this).serialize();

        $.post("{{ route('updateSuutraSolrByDate') }}", formData, function(response) {
            if (response.status === 'EMPTY') {
                alert("⚠ Không có dữ liệu trong khoảng ngày đã chọn.");
                return;
            }

            if (response.status === 'OK') {
                let interval = setInterval(() => {
                    $.get("{{ url('/check-progress') }}", function(data) {
                        $('#progress-bar').css('width', data.percent + '%').text(data.percent + '%');
                        if (data.percent >= 100) {
                            clearInterval(interval);
                            $('#progress-bar').css('background-color', 'green');
                            alert("✅ Cập nhật hoàn tất!");
                        }
                    });

                    // Poll logs
                    $.get("{{ url('/get-live-logs') }}", function(logData) {
                        logData.logs.forEach(log => {
                            $('#log-output').append(`<div>${log}</div>`);
                            $('#log-output').scrollTop($('#log-output')[0].scrollHeight); // Auto scroll
                        });
                    });
                }, 1000);

            }
        });
    });
  
</script>
@endsection