<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\JurnalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Jurnal Umum';
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
        'action' => array('jurnal/index')
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

    <div class="form-group"><br>
        <?= Html::submitButton(' <i class="ace-icon fa fa-check bigger-110"></i>Cari', ['class' => 'btn btn-info','name'=>'search','value'=>1]) ?>    
        
    </div>

</div>
     


    <?php ActiveForm::end(); ?>

    <?php 
   echo $this->render('_tabel_jurnal', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'results' => $results
        ]); 
    ?>
   
</div>


</div>
