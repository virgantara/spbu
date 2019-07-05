<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BbmDroppingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bbm Droppings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bbm-dropping-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Bbm Dropping', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'bbm_faktur_id',
            'no_lo',
            'tanggal',
            'jam',
            //'barang_id',
            //'jumlah',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
