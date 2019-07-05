<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BbmDispenserLog */
/* @var $form yii\widgets\ActiveForm */

use app\models\SalesMasterBarang;
use app\models\Shift;
use app\models\Departemen;


use kartik\depdrop\DepDrop;

use kartik\date\DatePicker;

$listDepartemen = Departemen::getListDepartemens();
$listDataBarang=SalesMasterBarang::getListBarangs();
$listDataShift=Shift::getListShifts();

$model->tanggal = $model->isNewRecord ? date('d-m-Y') : date('d-m-Y',strtotime($model->tanggal));


?>

<div class="bbm-dispenser-log-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'tanggal',['options'=>['class'=>'form-group col-xs-12 col-lg-12']])->widget(
        DatePicker::className(),[
            'options' => ['placeholder' => 'Pilih tanggal ...'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true
            ]
        ]
    ) ?>

    <?= $form->field($model, 'barang_id',['options'=>['class'=>'col-xs-12 col-lg-12']])->dropDownList($listDataBarang, ['prompt'=>'.. Pilih Barang']); ?>

    <?= $form->field($model, 'dispenser_id',['options'=>['class'=>'col-xs-12 col-lg-12']])->dropDownList($listDepartemen, ['prompt'=>'.. Pilih Dispenser']); ?>
    
    <?= $form->field($model, 'shift_id',['options'=>['class'=>'col-xs-12 col-lg-12']])->dropDownList($listDataShift, ['prompt'=>'.. Pilih Shift']); ?>
    <?= $form->field($model, 'jumlah',['options'=>['class'=>'col-xs-12 col-lg-12']])->textInput() ?>

    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
