<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BbmDropping */

$this->title = 'Create Bbm Dropping';
$this->params['breadcrumbs'][] = ['label' => 'Bbm Droppings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bbm-dropping-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
