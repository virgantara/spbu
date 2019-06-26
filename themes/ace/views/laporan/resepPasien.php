<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

// use keygenqt\autocompleteAjax\AutocompleteAjax;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesStokGudangSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
use app\models\JenisResep;
$listJenisResep = \app\models\JenisResep::getListJenisReseps();

$this->title = 'Laporan Resep Per Pasien';
$this->params['breadcrumbs'][] = $this->title;

$model->tanggal_awal = !empty($_GET['Penjualan']['tanggal_awal']) ? $_GET['Penjualan']['tanggal_awal'] : date('Y-m-d');
$model->tanggal_akhir = !empty($_GET['Penjualan']['tanggal_akhir']) ? $_GET['Penjualan']['tanggal_akhir'] : date('Y-m-d');
?>
<div class="sales-stok-gudang-index">

    <h1><?= Html::encode($this->title) ?></h1>
  
    <?php $form = ActiveForm::begin([
    	'method' => 'get',
    	'action' => ['laporan/resep-pasien'],
        'options' => [
            'class' => 'form-horizontal'
        ]
    ]); ?>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Tanggal Awal</label>
        <div class="col-lg-2 col-sm-10">
          <?= yii\jui\DatePicker::widget(
            [
                'model' => $model,
                'attribute' => 'tanggal_awal',
            // 'value' => date('d-m-Y'),
            'options' => ['placeholder' => 'Pilih tanggal awal ...'],
            // 'formatter' => [
                'dateFormat' => 'php:d-m-Y',
                // 'todayHighlight' => true
            // ]
        ]      
    ) ?> 
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Tanggal Akhir</label>
        <div class="col-lg-2 col-sm-10">
          <?= yii\jui\DatePicker::widget(
            [
                'model' => $model,
                'attribute' => 'tanggal_akhir',
            // 'value' => date('d-m-Y'),
            'options' => ['placeholder' => 'Pilih tanggal akhir ...'],
            // 'formatter' => [
                'dateFormat' => 'php:d-m-Y',
                // 'todayHighlight' => true
            // ]
        ]      
    ) ?> 
        </div>
    </div>
    
      <div class="form-group">
        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> No RM</label>
        <div class="col-lg-2 col-sm-10">
        
             <input name="customer_id"  type="text" id="customer_id" value="<?=!empty($_GET['customer_id']) ? $_GET['customer_id'] : '';?>"/> 
             
        </div>
    </div>
    <div class="col-sm-2">

 
</div>
<div class="col-sm-3">

    <div class="form-group"><br>
        <?= Html::submitButton(' <i class="ace-icon fa fa-check bigger-110"></i>Cari', ['class' => 'btn btn-info','name'=>'search','value'=>1]) ?>    
        <?= Html::submitButton(' <i class="ace-icon fa fa-check bigger-110"></i>Export XLS', ['class' => 'btn btn-success','name'=>'export','value'=>1]) ?>    
    </div>

</div>
     


    <?php ActiveForm::end(); ?>

    <table class="table table-bordered table-striped">
    	<thead>
    		<tr>
            <th>No</th>
            <th>Tgl</th>
    		<th>Nama Px</th>
    		<th>No RM</th>
    		<th>No Resep</th>
    		<th>Jenis<br>Resep</th>
            <th>Poli</th>
            <th>Dokter</th>
            <th>Kode Obat</th>
            <th>Nama Obat</th>
            <th>Qty</th>
            <th>Subtotal</th>
    		<th>Total</th>
    	</tr>
    	</thead>
    	<tbody>
    		<?php 
            $total = 0;
            $i = 0;
    		foreach($results['items'] as $key => $model)
    		{
                $i++;
                $subtotal = $model['sb_blt'];
                $total += $subtotal;
      
                
    		?>
    		<tr>
                <td><?=($i);?></td>
    			<td><?=$model['tgl'];?></td>
                <td><?=$model['px_id'];?></td>
                <td><?=$model['px_nm'];?></td>
                <td><?=$model['no_rx'];?></td>
                <td><?=$listJenisResep[$model['jns']];?></td>
                <td><?=$model['un'];?></td>
                <td><?=$model['d'];?></td>
                <td><?=$model['kd'];?></td>
                <td><?=$model['nm'];?></td>
                <td><?=$model['qty_bulat'];?></td>
                <td style="text-align: right"><?=$model['sb_blt'];?></td>
                <td style="text-align: right"><?=$model['tot_lbl'];?></td>
                

    		</tr>
    		<?php 
    	   }
    		?>

    	</tbody>
        <tfoot>
            <tr>
                <td colspan="11" style="text-align: right">Total</td>
                <td style="text-align: right"><?=$total;?></td>
                
            </tr>
        </tfoot>
    </table>
   
</div>
<?php

$uid = !empty($_GET['unit_id']) ? $_GET['unit_id'] : '';
$script = "

function fetchAllRefUnit(tipe){
    $.ajax({
        type : 'POST',
        data : 'tipe='+tipe,
        url : '/api/ajax-all-ref-unit',

        success : function(data){
            var data = $.parseJSON(data);
            
            $('#unit_id').empty();
            var row = '';
            row += '<option value=\"\">- Pilih Unit -</option>';
            $.each(data,function(i, obj){
                if(obj.id == '".$uid."')
                    row += '<option selected value='+obj.id+'>'+obj.label+'</option>';
                else
                    row += '<option value='+obj.id+'>'+obj.label+'</option>';
            });

            $('#unit_id').append(row);
            
        }

    });
}

$(document).ready(function(){
    fetchAllRefUnit(1);

    $('#jenis_rawat').change(function(){
        fetchAllRefUnit($(this).val());
    });
});

";
$this->registerJs(
    $script,
    \yii\web\View::POS_READY
);
// $this->registerJs($script);
?>