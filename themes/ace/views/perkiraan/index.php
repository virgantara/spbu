<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PerkiraanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Akun';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="perkiraan-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Akun', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],


            'kode',
            'nama',
            'namaParent',
            // 'perusahaan_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
