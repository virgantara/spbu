<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use keygenqt\autocompleteAjax\AutocompleteAjax;
use app\models\MasterJenisBarang;
use kartik\depdrop\DepDrop;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\PerusahaanSubStok */
/* @var $form yii\widgets\ActiveForm */

$listDepartment = \app\models\Departemen::getListDepartemens();
$listJenis=MasterJenisBarang::getList();

?>

<div class="perusahaan-sub-stok-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>
    <?= $form->field($model, 'departemen_id')->dropDownList($listDepartment, ['prompt'=>'..Pilih Departemen..']); ?>
     <label>Jenis Barang</label>
    <?=Html::dropDownList('jenis_barang_id','',$listJenis, ['prompt'=>'..Jenis..','id'=>'jenis_barang_id','class'=>'form-control']);?>
      <?php 
    AutoComplete::widget([
    'name' => 'barang_jenis',
    'id' => 'barang_jenis',
    'clientOptions' => [
         'source' =>new JsExpression('function(request, response) {
                        $.getJSON("'.Url::to(['sales-stok-gudang/ajax-get-barang-by-jenis/']).'", {
                            term: request.term,
                            jenis: $("#jenis_barang_id").val()
                        }, response);
             }'),
        'autoFill'=>true,
        'minLength'=>'1',
        'select' => new JsExpression("function( event, ui ) {
            if(ui.item.id != 0){
                $('#departemenstok-barang_id').val(ui.item.id);
                $('#departemenstok-exp_date').val(ui.item.ed);
                $('#departemenstok-batch_no').val(ui.item.batch_no);
            }
            

         }")
    ],
    'options' => [
        'size' => '40'
    ]
 ]); 
 ?>  <label>Barang</label>
     <input type="text" class="form-control" id="barang_jenis"/>
     <?= $form->field($model, 'barang_id')->hiddenInput()->label(false); ?>


    <?= $form->field($model, 'stok_bulan_lalu')->textInput() ?>

    <?= $form->field($model, 'stok')->textInput() ?>
    <?= $form->field($model, 'exp_date')->textInput() ?>
    <?= $form->field($model, 'batch_no')->textInput() ?>
    <?= $form->field($model, 'stok_minimal')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
