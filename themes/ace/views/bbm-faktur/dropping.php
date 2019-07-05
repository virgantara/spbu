<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url; 

/* @var $this yii\web\View */
/* @var $searchModel app\models\BbmFakturSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dropping';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bbm-faktur-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'namaSuplier',
            
            'no_so',
            'tanggal_so',
            [
                'label' => 'Item',
                'format' => 'raw',
                'value' => function($model,$i) {
                    return implode(',', \yii\helpers\ArrayHelper::map($model->bbmFakturItems, 'barang_id', 'barang.nama_barang'));
                }, 
            ],
            [
             'label' => 'Volume',
              'value' => function($model,$i) {
                    return $model->volume;
                },      
           ],
            [
                'attribute' => 'is_dropping',
                'label' => 'Status',
                'format' => 'raw',
                'filter'=>["1"=>"Selesai","0"=>"Pending"],
                'value'=>function($model,$url){

                    $st = $model->is_dropping == 1 ? 'success' : 'danger';
                    $label = $model->is_dropping == 1 ? 'Selesai' : 'Pending';
                    return '<button type="button" class="btn btn-'.$st.' btn-sm" >
                               <span>'.$label.'</span>
                            </button>';
                    
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {list}',
                'visibleButtons' => [
                    
                    'update' => function ($model) {
                        return \Yii::$app->user->can('admin') || \Yii::$app->user->can('gudang') || \Yii::$app->user->can('operatorAdmin') || \Yii::$app->user->can('adminSpbu');
                    },
                    'list' => function ($model) {
                        return \Yii::$app->user->can('admin') || \Yii::$app->user->can('gudang') || \Yii::$app->user->can('operatorAdmin') || \Yii::$app->user->can('adminSpbu');
                    },
                    
                ],
                'buttons' => [
                    
                    'list' => function ($url, $model) {
                        // $st = $model->pilih == 1 ? 'ok' : 'remove';
                        $icon = '<span class="glyphicon glyphicon-list"></span>';
                        return Html::a($icon, $url, [
                                   'title'        => 'List LO ',
                                    // 'data-confirm' => Yii::t('yii', 'Pilih harga item ini?'),
                                    // 'data-method'  => 'post',
                        ]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url =Url::to(['bbm-dropping/create','id'=>$model->id]);
                        return $url;
                    }

                    else if ($action === 'list') {
                        $url =Url::to(['bbm-dropping/index','id'=>$model->id]);
                        return $url;
                    }


                  }
            ],
        ],
    ]); ?>
</div>
