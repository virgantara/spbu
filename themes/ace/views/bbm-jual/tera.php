<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\BbmJual */

$this->title = 'Create Terra BBM';
$this->params['breadcrumbs'][] = ['label' => 'BBM Terra', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bbm-jual-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_tera', [
        'model' => $model,
    ]) ?>

</div>
