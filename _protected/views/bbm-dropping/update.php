<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BbmDropping */

$this->title = 'Update Bbm Dropping: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bbm Droppings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bbm-dropping-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
