<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use app\models\SalesMasterBarang;
use app\models\Shift;
use app\models\SalesGudang;
use app\models\Perusahaan;

$listDataBarang=SalesMasterBarang::getListBarangs();
$listDataShift=Shift::getListShifts();
$listDataGudang=SalesGudang::getListGudangs();

/* @var $this yii\web\View */
/* @var $model app\models\BbmDropping */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bbm-dropping-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="profile-user-info">
        <div class="profile-info-row">
            <div class="profile-info-name"> Nomor SO </div>

            <div class="profile-info-value">
               <?= $bbmFaktur->no_so; ?>
            </div>
        </div>
         <div class="profile-info-row">
            <div class="profile-info-name"> Tangki </div>

            <div class="profile-info-value">
               <?= $form->field($model, 'gudang_id')->dropDownList($listDataGudang, ['prompt'=>'.. Pilih Tangki'])->label(false); ?>
            </div>
        </div>
        
        <div class="profile-info-row">
            <div class="profile-info-name"> Shift </div>

            <div class="profile-info-value">
               <?= $form->field($model, 'shift_id')->dropDownList($listDataShift, ['prompt'=>'.. Pilih Shift'])->label(false); ?>
            </div>
        </div>
        <div class="profile-info-row">
            <div class="profile-info-name"> Barang </div>

            <div class="profile-info-value">
               <?= $form->field($model, 'barang_id')->dropDownList($listDataBarang, ['prompt'=>'.. Pilih BBM','id'=>'barang_id'])->label(false); ?>
            </div>
        </div>
       <div class="profile-info-row">
            <div class="profile-info-name"> Jam </div>

            <div class="profile-info-value">
               <?= $form->field($model, 'jam')->widget(TimePicker::className(),[
        'options' => ['placeholder' => 'Pilih jam LO ...'],
        'pluginOptions' => [
            'showSeconds' => true,
            'secondStep' => 10, 
            'showMeridian' => false,
            'minuteStep' => 5,
  
        ]
    ])->label(false) ?>
            </div>
        </div>
        <div class="profile-info-row">
            <div class="profile-info-name"> Tanggal </div>

            <div class="profile-info-value">
               <?= $form->field($model, 'tanggal')->widget(
        DatePicker::className(),[
            // 'value' => date('d-M-Y'),
            'options' => ['placeholder' => 'Pilih tanggal transaksi ...'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true
            ]
        ]
    )->label(false) ?>
            </div>
        </div>
       
        <div class="profile-info-row">
            <div class="profile-info-name"> Nomor LO </div>

            <div class="profile-info-value">
               <?= $form->field($model, 'no_lo')->textInput(['maxlength' => true])->label(false) ?>
            </div>
        </div>
        <div class="profile-info-row">
            <div class="profile-info-name"> Jumlah </div>

            <div class="profile-info-value">
               <?= $form->field($model, 'jumlah')->textInput(['maxlength' => true])->label(false) ?>
            </div>
        </div>
         
        <div class="profile-info-row">
            <div class="profile-info-name"></div>

            <div class="profile-info-value">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    

    <?php ActiveForm::end(); ?>

</div>
