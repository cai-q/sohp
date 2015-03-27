<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?=$tomonth?>|月度更新量报表</title>
    <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/sohp/statics/css/bootstrap-datetimepicker.min.css">
    <!--    <link href="/sohp/statics/css/flat-ui.css" rel="stylesheet">-->
    <style>
        #canvas-holder1 {
            width: 300px;
            margin: 20px auto;
        }
        #canvas-holder2 {
            width: 50%;
            margin: 20px 25%;
        }
        #chartjs-tooltip {
            opacity: 1;
            position: absolute;
            background: rgba(0, 0, 0, .7);
            color: white;
            padding: 3px;
            border-radius: 3px;
            -webkit-transition: all .1s ease;
            transition: all .1s ease;
            pointer-events: none;
            -webkit-transform: translate(-50%, 0);
            transform: translate(-50%, 0);
        }
        .chartjs-tooltip-key{
            display:inline-block;
            width:10px;
            height:10px;
        }
    </style>
</head>
<body>
<div class="navbar navbar-inverse navbar-static-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
            </button>
            <a class="navbar-brand" href="#"><?=$site_name ?></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">频道切换<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($web_list as $k => $v): ?>
                            <li><a href="/sohp/index.php?c=monthly&m=overview&site=<?=$k ?>&month=<?=$tomonth ?>"><?=$v['name'] ?></a></li>
                        <?php endforeach?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">报表类型<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="/sohp/index.php?c=daily&m=overview&site=<?=$site ?>&day=<?=$trueday ?>">当日报表</a></li>
                        <li class="active"><a class="active" href="/sohp/index.php?c=monthly&m=overview&site=<?=$site ?>&month=<?=$tomonth ?>">月度报表</a></li>
                        <li><a href="/sohp/index.php?c=period&m=overview&site=<?=$site ?>">基于时间段</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="page-header">
    <h1 class="col-md-offset-2">
        <small><span style="color: gray;"><?=$tomonth_string?></span>
            <i>
                <span class="form_date glyphicon glyphicon-calendar" style="cursor: pointer;" aria-hidden="true"></span>
            </i> 总更新量 ：
        </small>
            <span id="total_sum">Subtext for header<span>
    </h1>
</div>

<div class="panel-group col-md-2" id="accordion" role="tablist" aria-multiselectable="true">
    <?php foreach ($web_list as $k => $v): ?>
        <div class="panel panel-info">
            <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$k?>" aria-expanded="false" aria-controls="collapseOne">
                        <?=$v['name']?>
                    </a>
                </h4>
            </div>
            <div id="collapse<?=$k?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                    <ul class="">
                        <li class="d"><a href="/sohp/index.php?c=daily&m=overview&site=<?=$k ?>&day=<?=$trueday ?>">日报表</a></li>
                        <li class="m"><a class="active" href="/sohp/index.php?c=monthly&m=overview&site=<?=$k ?>&month=<?=$tomonth ?>">月报表</a></li>
                        <li class="p"><a href="/sohp/index.php?c=period&m=overview&site=<?=$k ?>">最近30天(可选择时间段)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endforeach?>
</div>
<article class="col-md-10">
    <section class="col-md-10" style="height: 1000px;">
        <div class="well" style="width: 1250px; height: 600px;">
            <div class="legend" style="height: 50px; width: 200px;"></div>
            <canvas id="myChart" width="1200" height="500"></canvas>
            <div id="chartjs-tooltip"></div>
        </div>
    </section>
</article>
<script src="http://cdn.bootcss.com/jquery/2.1.3/jquery.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="/sohp/statics/js/bootstrap-datetimepicker.min.js"></script>
<script src="/sohp/statics/js/bootstrap-datetimepicker.zh-CN.js"></script>
<script src="/sohp/statics/js/Chart.min.js"></script>
<script>
    $(".panel-collapse").each(function() {
        id = $(this).attr('id');
        if(id.indexOf("<?=$site?>") > 0) {
            $(this).addClass('in');
            $(this).parent().removeClass("panel-info");
            $(this).parent().addClass("panel-primary");
            anode = $(this).find(".m").find("a");
            anode.append(' <span class="col-md-offset-1 glyphicon glyphicon-hand-left" aria-hidden="true"></span>');
        }
    });
</script>
<script type="text/javascript">
    Date.prototype.Format = function (fmt) { //author: meizz
        var o = {
            "M+": this.getMonth() + 1, //月份
            "d+": this.getDate(), //日
            "h+": this.getHours(), //小时
            "m+": this.getMinutes(), //分
            "s+": this.getSeconds(), //秒
            "q+": Math.floor((this.getMonth() + 3) / 3), //季度
            "S": this.getMilliseconds() //毫秒
        };
        if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o)
            if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    };

    date = new Date();
    $(".form_date").datetimepicker({
        format: "yyyymmdd",
        language:  'zh-CN',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 0,
        todayHighlight: 0,
        startView: 3,
        minView: 3,
        forceParse: 1,
        startDate: '2015-01-01',
        endDate: date,
        pickerPosition: "bottom-right"
    }).on('changeDate', function(ev){
        date_set = new Date(ev.date.valueOf()- 8 * 60 * 60 * 1000).Format("yyyyMM");
        window.location.href = "/sohp/index.php?c=monthly&m=overview&site=<?=$site ?>&month=" + date_set;
    });
</script>
<script>
    var data = <?=$info ?>;
    $("#total_sum").html(eval(data.datasets[0].data.join('+')));
    var watch_date;
    var ctx = $("#myChart").get(0).getContext("2d");
    var myLineChart = new Chart(ctx).Line(data, {
        scaleOverride :true ,
        scaleSteps: 8,
        scaleStepWidth: 50,
        responsive: true,
        scaleShowGridLines: true,
        scaleStartValue: 0,
        scaleShowLabels: true,
        scaleLabel: "<%=value%>",
        scaleOverlay: true,
        legendTemplate: "<div class=\"<%=name.toLowerCase()%>-legend\" style=\"width: 200px;\"><% for (var i=0; i<datasets.length; i++){%><div><span style=\"background-color:<%=datasets[i].strokeColor%>; width: 20px; height: 15px; display: inline-block;\"></span><%if(datasets[i].label){%>&nbsp;&nbsp;<%=datasets[i].label%><%}%></div><%}%></div>",
        bezierCurve: true,
        tooltipTemplate: "<%=label%>-<%= value %>",
        customTooltips: function(tooltip) {
            var tooltipEl = $('#chartjs-tooltip');

            if (!tooltip) {
                tooltipEl.css({
                    opacity: 0
                });
                return;
            }

            tooltipEl.removeClass('above below');
            tooltipEl.addClass(tooltip.yAlign);

            label = tooltip.text.split("-")[0];
            text = tooltip.text.split("-")[1];
            watch_date = label;
            active_label = label;

            innerHtml = [
                '<div class="chartjs-tooltip-section">',
                '	<span class="chartjs-tooltip-key" style="background-color:' + data.datasets[0]['strokeColor'].fill + '"></span>',
                '	<span class="chartjs-tooltip-value">' + '该日共更新稿件  <b style="color: darkturquoise;">' + text + '</b>  篇&nbsp;&nbsp;&nbsp;' + '</span>',
                '	<br><span class="chartjs-tooltip-value">&nbsp;&nbsp;&nbsp;<b style="color: red;">点击</b>&nbsp;显示详细信息&nbsp;&nbsp;&nbsp;</span>',
                '</div>'
            ].join('');

            tooltipEl.html(innerHtml);

            tooltipEl.css({
                opacity: 1,
                left: tooltip.chart.canvas.offsetLeft + tooltip.x + 'px',
                top: tooltip.chart.canvas.offsetTop + tooltip.y + 'px',
                fontFamily: tooltip.fontFamily,
                fontSize: tooltip.fontSize,
                fontStyle: tooltip.fontStyle
            });
        }
    });
    $(".legend").html(myLineChart.generateLegend());

    $("#myChart").click(function(event) {
        window.open("/sohp/index.php?c=daily&m=overview&site=<?=$site ?>&day=" + watch_date);
    });
</script>
</body>
</html>
