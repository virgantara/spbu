<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\JurnalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


use yii\helpers\ArrayHelper;

use kartik\select2\Select2;
use yii\web\JsExpression;
$url = \yii\helpers\Url::to(['/perkiraan/ajax-perkiraan']);

$this->title = 'Buku Besar';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jurnal-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php

$model->tanggal_awal = !empty($_GET['Jurnal']['tanggal_awal']) ? $_GET['Jurnal']['tanggal_awal'] : date('Y-m-01');
$model->tanggal_akhir = !empty($_GET['Jurnal']['tanggal_akhir']) ? $_GET['Jurnal']['tanggal_akhir'] : date('Y-m-d');

?>

  
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => array('jurnal/buku-besar')
    ]); ?>
    <div class="col-sm-3">
   <?= $form->field($model, 'tanggal_awal')->widget(
        \yii\jui\DatePicker::className(),[
             'options' => ['placeholder' => 'Pilih tanggal awal ...'],
            'dateFormat' => 'php:d-m-Y',
        ]
    ) ?>
</div>
<div class="col-sm-3">
    <?php
    echo $form->field($model, 'tanggal_akhir')->widget(
        \yii\jui\DatePicker::className(),[ 
            'options' => ['placeholder' => 'Pilih tanggal awal ...'],
            'dateFormat' => 'php:d-m-Y',
        ]
    ) ;

    ?>

</div>
<div class="col-sm-3">
     <?php 
     echo $form->field($model, 'perkiraan_id')->widget(Select2::classname(), [
        // 'initValueText' => $cityDesc, // set the initial display text
        'options' => ['placeholder' => 'Cari perkiraan ...'],

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

</div>
<div class="col-sm-3">

    <div class="form-group"><br>
        <?= Html::submitButton(' <i class="ace-icon fa fa-check bigger-110"></i>Cari', ['class' => 'btn btn-info','name'=>'search','value'=>1]) ?>    
        
    </div>

</div>
     


    <?php ActiveForm::end(); ?>

    <?php 
   echo $this->render('_bb', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'results' => $results,
            'listakun' => $listakun,
            'results' => $results
        ]); 
    ?>
   
</div>


</div>
