<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use app\models\Perusahaan;

use kartik\depdrop\DepDrop;


$listData=Perusahaan::getListPerusahaans();

// $list=SalesGudang::find()->where(['jenis' => '3'])->all();
// $listSatuan=ArrayHelper::map($list,'id_satuan','nama');


use kartik\select2\Select2;
use yii\web\JsExpression;

$url = \yii\helpers\Url::to(['/perkiraan/ajax-perkiraan']);
?>

<div class="sales-master-barang-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'kode_barang')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'nama_barang')->textInput(['maxlength' => true]) ?>

  
    <?= $form->field($model, 'harga_beli')->textInput() ?>

    <?= $form->field($model, 'harga_jual')->textInput() ?>
       
    <?php


    echo $form->field($model, 'id_perusahaan')->dropDownList($listData, ['prompt'=>'..Pilih Perusahaan..','id'=>'id_perusahaan']);

   
     ?>


      <?php 
       echo $form->field($model, 'id_satuan')->widget(DepDrop::classname(), [
        'options'=>['id'=>'id_satuan'],
        'pluginOptions'=>[
            'depends'=>['id_perusahaan'],
            'placeholder'=>'..Pilih Satuan..',
            'url'=>Url::to(['/perusahaan/get-satuan'])
        ]
    ]);
    

      ?> 
       

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php

$this->registerJs(' 
    $(document).ready(function(){
         $(\'#id_perusahaan\').trigger(\'change\');
    });', \yii\web\View::POS_READY);

?>