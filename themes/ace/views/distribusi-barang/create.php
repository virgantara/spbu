<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DistribusiBarang */

$this->title = 'Create Distribusi Barang';
$this->params['breadcrumbs'][] = ['label' => 'Distribusi Barangs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribusi-barang-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
