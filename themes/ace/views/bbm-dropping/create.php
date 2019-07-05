<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BbmDropping */

$this->title = 'Create LO';
$this->params['breadcrumbs'][] = ['label' => 'LO', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bbm-dropping-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'bbmFaktur' => $bbmFaktur
    ]) ?>

</div>
