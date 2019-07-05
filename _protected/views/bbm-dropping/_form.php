<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BbmDropping */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bbm-dropping-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'bbm_faktur_id')->hiddenInput() ?>
    <label>Nomor SO</label>
    <?= $bbmFaktur->no_so; ?>

    <?= $form->field($model, 'no_lo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'waktu')->textInput() ?>

    <?= $form->field($model, 'barang_id')->textInput() ?>

    <?= $form->field($model, 'jumlah')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
