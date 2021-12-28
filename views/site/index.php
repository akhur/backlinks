<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

$data = \app\models\Backlink::graphData();
?>
<div class="site-index">


    <div id="bar-container">
        <div id="bar-chart"></div>
    </div>


</div>
<?php
$labels = json_encode($data['allMonths']);
$noFollowData = json_encode($data['noFollowData']);
$followData = json_encode($data['followData']);
$script = <<<JS
    var data = {
      labels: $labels,
      series: [
        $followData,
        $noFollowData
      ]
    };
    
    var options = {
      seriesBarDistance: 30
    };
    var responsiveOptions = [
      ['screen and (max-width: 640px)', {
        seriesBarDistance: 5,
        axisX: {
          labelInterpolationFnc: function (value) {
            return value[0];
          }
        }
      }]
    ];
    
    new Chartist.Bar('#bar-chart', data, options, responsiveOptions);
JS;

$this->registerJs($script, \yii\web\View::POS_LOAD);