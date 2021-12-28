<?php

use yii\grid\GridView;

/**
 * @var $this \yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
?>

<h1>Список</h1>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items} {pager}',
    'columns' => [
        'referring_page_title',
        'referring_page_url',
        'language',
        [
            'attribute' => 'nofollow',
            'value' => function($model) {
                return $model->nofollow ? 'TRUE' : 'FALSE';
            }
        ],
        'first_seen',

    ],
]); ?>