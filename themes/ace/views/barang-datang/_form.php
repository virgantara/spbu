<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use kartik\time\TimePicker;

use app\models\SalesMasterBarang;
use app\models\Shift;
use app\models\Perusahaan;

use kartik\date\DatePicker;
use keygenqt\autocompleteAjax\AutocompleteAjax;
use app\models\SalesGudang;

$listDataBarang=SalesMasterBarang::getListBarangs();
$listDataShift=Shift::getListShifts();

$listDataGudang=SalesGudang::getListGudangs();

$userLevel = Yii::$app->user->identity->access_role;    
        
if($userLevel != 'admin'){
    $userPt = Yii::$app->user->identity->perusahaan_id;
    $model->perusahaan_id = $userPt;   
}

$model->tanggal = $model->isNewRecord ? date('d-m-Y') : $model->tanggal;

$listData=Perusahaan::getListPerusahaans();
?>

<div class="barang-datang-form">

    <?php $form = ActiveForm::begin(); ?>
      <?= $form->field($model, 'faktur_id',['options'=>['class'=>'form-group col-xs-12 col-lg-6']])->widget(AutocompleteAjax::classname(), [
        'multiple' => false,
        'url' => ['bbm-faktur/ajax-search'],
        'options' => ['placeholder' => 'Cari No SO.']
    ]) ?>
      <?= $form->field($model, 'gudang_id',['options'=>['class'=>'form-group col-xs-12 col-lg-6']])->dropDownList($listDataGudang, ['prompt'=>'.. Pilih Gudang','id'=>'gudang_id']); ?>
     <?= $form->field($model, 'no_lo',['options'=>['class'=>'form-group col-xs-12 col-lg-6']])->textInput() ?>
    <?= $form->field($model, 'tanggal_lo',['options'=>['class'=>'form-group col-xs-12 col-lg-6']])->widget(
        DatePicker::className(),[
            'name' => 'tanggal_lo', 
            'size' => 'md',
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            // 'value' => date('d-M-Y'),
            'options' => ['placeholder' => 'Pilih Tanggal LO ...'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true
            ]
        ]
    ) ?>
    <?= $form->field($model, 'tanggal',['options'=>['class'=>'form-group col-xs-12 col-lg-6']])->widget(
        DatePicker::className(),[
            'name' => 'tanggal', 
            'size' => 'md',
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            // 'value' => date('d-M-Y'),
            'options' => ['placeholder' => 'Select issue date ...'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true
            ]
        ]
    ) ?>
     <?= $form->field($model, 'jam',['options'=>['class'=>'form-group col-xs-12 col-lg-6']])->widget(TimePicker::className(),[
        'options' => ['placeholder' => 'Select start operating time ...'],
        'size' => 'md',
        'pluginOptions' => [
            'showSeconds' => true,
            'secondStep' => 10, 
            'showMeridian' => false,
            'minuteStep' => 5,
            
        ]
    ]) ?>
     <?= $form->field($model, 'barang_id',['options'=>['class'=>'form-group col-xs-12 col-lg-6']])->dropDownList($listDataBarang, ['prompt'=>'.. Pilih BBM','id'=>'barang_id']); ?>
    <?= $form->field($model, 'jumlah',['options'=>['class'=>'form-group col-xs-12 col-lg-6']])->textInput(['type'=>'number']) ?>

     <?= $form->field($model, 'shift_id',['options'=>['class'=>'form-group col-xs-12 col-lg-6']])->dropDownList($listDataShift, ['prompt'=>'.. Pilih Shift']); ?>

    <?= $form->field($model, 'perusahaan_id',['options'=>['class'=>'form-group col-xs-12 col-lg-6']])->dropDownList($listData, ['prompt'=>'..Pilih Perusahaan..']);?>


  

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
