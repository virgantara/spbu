<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Transaksi */
/* @var $form yii\widgets\ActiveForm */
use yii\helpers\ArrayHelper;

use app\models\Perusahaan;
use app\models\Perkiraan;

use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\date\DatePicker;
$url = \yii\helpers\Url::to(['/perkiraan/ajax-perkiraan']);
?>

<div class="transaksi-form">

    <?php $form = ActiveForm::begin(); ?>

     <?php 
     echo $form->field($model, 'perkiraan_id')->widget(Select2::classname(), [
        // 'initValueText' => $cityDesc, // set the initial display text
        'options' => ['placeholder' => 'Cari akun ...'],

        'pluginEvents' => [
            "change" => 'function() { 
                var data_id = $(this).val();
                
                $.ajax({
                    url : "'.\yii\helpers\Url::to(['/perkiraan/ajax-get-perkiraan']).'",
                    type : "post",
                    data : "id="+data_id,
                    success : function(res){
                        var data = $.map(res, function(value, index) {
                            return [value];
                        });

                        // $("#perkiraan-level").val(data[0][2]);
                        
                    },
                });
            }',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' =>2,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],

            'ajax' => [
                'url' => $url,
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                // 'success' => new JsExpression('function(data) { alert(data.text) }'),
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(city) { return city.text; }'),
            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
        ],
    ]);?>
    <?= $form->field($model, 'keterangan')->textInput(['maxlength' => true]) ?>
    <?php 
     echo $form->field($model, 'perkiraan_lawan_id')->widget(Select2::classname(), [
        // 'initValueText' => $cityDesc, // set the initial display text
        'options' => ['placeholder' => 'Cari akun ...'],

        'pluginEvents' => [
            "change" => 'function() { 
                var data_id = $(this).val();
                
                $.ajax({
                    url : "'.\yii\helpers\Url::to(['/perkiraan/ajax-get-perkiraan']).'",
                    type : "post",
                    data : "id="+data_id,
                    success : function(res){
                        var data = $.map(res, function(value, index) {
                            return [value];
                        });

                       
                    },
                });
            }',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' =>2,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],

            'ajax' => [
                'url' => $url,
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                // 'success' => new JsExpression('function(data) { alert(data.text) }'),
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(city) { return city.text; }'),
            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
        ],
    ]);?>
    <?= $form->field($model, 'keterangan_lawan')->textInput(['maxlength' => true]) ?>
     <?= $form->field($model, 'tanggal')->widget(
        DatePicker::className(),[
            // 'value' => date('d-M-Y'),
            'options' => ['placeholder' => 'Pilih tanggal transaksi ...'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true
            ]
        ]
    ) ?>
    <?= $form->field($model, 'no_bukti')->textInput() ?>
    <?= $form->field($model, 'jumlah')->textInput() ?>

    


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
