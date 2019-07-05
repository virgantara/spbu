<?php

use yii\helpers\Html;

use yii\widgets\ActiveForm;

use app\models\Perusahaan;
use app\models\SalesSuplier;
use kartik\date\DatePicker;


$userLevel = Yii::$app->user->identity->access_role;    
        
if($userLevel != 'admin'){
    $userPt = Yii::$app->user->identity->perusahaan_id;
    $model->perusahaan_id = $userPt;   
}


$listData=Perusahaan::getListPerusahaans();
$listDataSupp=SalesSuplier::getListSupliers();

// $model->tanggal_lo = $model->isNewRecord || ! ? date('d-m-Y') : $model->tanggal_lo;
$model->tanggal_so = $model->isNewRecord ? date('d-m-Y') : $model->tanggal_so;

/* @var $this yii\web\View */
/* @var $model app\models\BbmFaktur */

$this->title = 'Dropping';
$this->params['breadcrumbs'][] = ['label' => 'Bbm Fakturs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bbm-faktur-update">

    <h1><?= Html::encode('Data SO') ?></h1>
   
 <div class="profile-user-info profile-user-info-striped">
	<div class="profile-info-row">
		<div class="profile-info-name"> Suplier </div>

		<div class="profile-info-value">
			<span class="editable" id="username"><?=$model->namaSuplier;?></span>
		</div>
	</div>

	<div class="profile-info-row">
		<div class="profile-info-name"> Nomor SO </div>

		<div class="profile-info-value">
			<?=$model->no_so;?>
		</div>
	</div>

	<div class="profile-info-row">
		<div class="profile-info-name"> Tgl SO </div>

		<div class="profile-info-value">
			<?=$model->tanggal_so;?>
		</div>
	</div>
	<div class="profile-info-row">
		<div class="profile-info-name"> Total </div>

		<div class="profile-info-value">
			Rp <?=\app\helpers\MyHelper::formatRupiah($model->hargaTotal,2);?>
		</div>
	</div>
</div> 

    <h1><?= Html::encode($this->title) ?></h1>
<div class="bbm-faktur-form">

    <?php $form = ActiveForm::begin(); ?>

   

    <?= $form->field($model, 'no_do')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tanggal_do')->widget(
        DatePicker::className(),[
            'name' => 'tanggal', 
            // 'value' => date('d-M-Y', strtotime('0 days')),
            'options' => ['placeholder' => 'Pilih tanggal DO ...'],
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'todayHighlight' => true
            ]
        ]
    ) ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


</div>
