<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RequestOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Request Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Request Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'no_ro',
            'petugas1',
            'petugas2',
            'tanggal_pengajuan',
            'tanggal_penyetujuan',
            //'perusahaan_id',
           [
                'attribute' => 'is_approved',
                'label' => 'Status Order',
                'format' => 'raw',
                'filter'=>["1"=>"Dilayani","0"=>"Belum","2"=>"Disetujui"],
                'value'=>function($model,$url){

                    $st = '';
                    $label = '';

                    switch ($model->is_approved) {
                        case 1:
                            $label = 'Dilayani';
                            $st = 'success';
                            break;
                        case 2:
                            $label = 'Disetujui';
                            $st = 'info';
                            break;

                        default:
                            $label = 'Belum';
                            $st = 'danger';
                            break;
                    }
                    
                    return '<button type="button" class="btn btn-'.$st.' btn-sm" >
                               <span>'.$label.'</span>
                            </button>';
                    
                },
            ],
            //'created',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
