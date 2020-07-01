<script data-exec-on-popstate>

    $(function () {
        Dcat.intervalIds = [];
        Dcat.addIntervalId = function (intervalId, persist) {
            this.intervalIds.push({id:intervalId, persist:persist});
        };

        Dcat.clearIntervalId = function (intervalId) {
            for (var id in this.intervalIds) {
                if (intervalId == this.intervalIds[id].id && !this.intervalIds[id].persist) {
                    clearInterval(intervalId);
                    this.intervalIds.splice(id, 1);
                }
            }
        };

        Dcat.cleanIntervalId = function () {
            for (var id in this.intervalIds) {
                if (!this.intervalIds[id].persist) {
                    clearInterval(this.intervalIds[id].id);
                    this.intervalIds.splice(id, 1);
                }
            }
        };

        $(document).on('pjax:complete', function(xhr) {
            Dcat.cleanIntervalId();
        });

        $('.log-refresh').on('click', function() {
            $.pjax.reload('#pjax-container');
        });

        var pos = {{ $end }};

        function changePos(offset){
            pos = offset;
        }

        function fetch() {
            $.ajax({
                url:'{{ $tailPath }}',
                method: 'get',
                data : {offset : pos},
                success:function(data) {
                    for (var i in data.logs) {
                        $('table > tbody > tr:first').before(data.logs[i]);
                    }
                    changePos(data.pos);
                }
            });
        }

        var refreshIntervalId = null;

        $('.log-live').click(function() {
            $("i", this).toggleClass("fa-play fa-pause");

            if (refreshIntervalId) {
                Dcat.clearIntervalId(refreshIntervalId);
                refreshIntervalId = null;
            } else {
                refreshIntervalId = setInterval(function() {
                    fetch();
                }, 2000);
                Dcat.addIntervalId(refreshIntervalId, false);
            }
        });
    });


</script>
<div class="row">

    <!-- /.col -->
    <div class="col-md-10">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div>

                    <button type="button" class="btn btn-primary btn-sm log-refresh"><i class="fa fa-refresh"></i> {{ trans('admin.refresh') }}</button>
                    <button type="button" class="btn btn-default btn-sm log-live"><i class="fa fa-play"></i> </button>
                </div>
                <div class="pull-right">
                    <div class="btn-group">
                        @if ($prevUrl)
                            <a href="{{ $prevUrl }}" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></a>
                        @endif
                        @if ($nextUrl)
                            <a href="{{ $nextUrl }}" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></a>
                        @endif
                    </div>
                    <!-- /.btn-group -->
                </div>
                <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped">

                        <thead>
                        <tr>
                            <th>错误级别</th>
                            <th class="text-center">环境</th>
                            <th class="text-center">时间</th>
                            <th>详情</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($logs as $index => $log)

                            <tr>
                                <td style="width: 100px"><span class="label bg-{{Dcat\Admin\Extension\LogViewer\LogViewer::$levelColors[$log['level']]}}">{{ $log['level'] }}</span></td>
                                <td style="width: 100px" class="text-center"><strong>{{ $log['env'] }}</strong></td>
                                <td style="width:180px;" class="text-center">{{ $log['time'] }}</td>
                                <td><code style="word-break: break-all;">{{ $log['info'] }}</code></td>
                                <td>
                                    @if(!empty($log['trace']))
                                        <div class="btn btn-primary btn-sm" data-toggle="collapse" data-target=".trace-{{$index}}" style="white-space:nowrap;">查看详情</div>
                                    @endif
                                </td>
                            </tr>

                            @if (!empty($log['trace']))
                                <tr class="collapse trace-{{$index}}">
                                    <td colspan="5"><div style="white-space: pre-wrap;background: #333;color: #fff; padding: 10px;">{{ $log['trace'] }}</div></td>
                                </tr>
                            @endif

                        @endforeach

                        </tbody>
                    </table>
                    <!-- /.table -->
                </div>
                <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /. box -->
    </div>

    <div class="col-md-2">

        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">文件列表</h3>
            </div>
            <div class="box-body no-padding">
                <table class="table table-border">
                    @foreach($logFiles as $logFile)
                        <tr @if($logFile == $fileName)class="active"@endif>
                            <td>

                                <a href="{{ route('log-viewer-file', ['file' => $logFile]) }}"><i class="fa fa-{{ ($logFile == $fileName) ? 'folder-open' : 'folder' }}"></i>{{ $logFile }}</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <!-- /.box-body -->
        </div>

        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">信息</h3>
            </div>
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                    <li class="margin: 10px;">
                        <a>大小: {{ $size }}</a>
                    </li>
                    <li class="margin: 10px;">
                        <a>更新时间: {{ date('Y-m-d H:i:s', filectime($filePath)) }}</a>
                    </li>
                </ul>
            </div>
            <!-- /.box-body -->
        </div>

        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
