<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
// use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\models\Kas;
use \kartik\grid\GridView;
use app\models\SalesMasterBarang;
use app\models\Departemen;

/* @var $this yii\web\View */
/* @var $searchModel app\models\KasSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hasil Penjualan';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kas-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php 

$form = ActiveForm::begin();
    $bulans = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    $tahuns = [];

    for($i = 2016 ;$i<=date('Y')+50;$i++)
        $tahuns[$i] = $i;

    $bulan = !empty($_POST['bulan']) ? $_POST['bulan'] : date('m');
    $tahun = !empty($_POST['tahun']) ? $_POST['tahun'] : date('Y');

    $listBarang = SalesMasterBarang::getListBarangs();
    // $listDispenser = Departemen::getListDepartemens();
    ?>

    <div class="col-xs-4 col-md-3 col-lg-2">
        
        <?= Html::dropDownList('bulan', $bulan,$bulans,['class'=>'form-control ']); ?>

    </div>
     <div class="col-xs-4 col-md-3 col-lg-2">
        
       
        <?= Html::dropDownList('tahun', $tahun,$tahuns,['class'=>'form-control']); ?>
    </div>
   
    <div class="col-xs-4 col-md-3 col-lg-2">
        
        <?= Html::dropDownList('barang_id',!empty($_POST['barang_id']) ? $_POST['barang_id'] : '',$listBarang,['class'=>'form-control']); ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
    </div>
    <?php 
    ActiveForm::end();
    ?>

<?php 
if(!empty($results))
{
    // $countDispenser = count($listDispenser);
?>


<table class="table table-striped table-hovered table-bordered">
    <thead>
    <tr>
    
    <th colspan="6" style="text-align: center;"><?=$bulans[$bulan];?></th>
    
    
    <th colspan="3" style="text-align: center;"><?=$barang->nama_barang;?></th>
</tr>
<tr>
    <th style="text-align: center;">No</th>
    <th style="text-align: center;">Tgl</th>
    <th style="text-align: center;">Shift</th>
     <?php 

        // print_r($disp);
        echo '<th style="text-align: center;">Akhir</th>';
        echo '<th style="text-align: center;">Awal</th>';
        echo '<th style="text-align: center;">Saldo</th>';
    
    ?>
    <th style="text-align: center;">Harga</th>
    <th style="text-align: center;">Total (Rp)</th>
</tr>
</thead>
<?php 
$total_saldo_all = 0;
$total = 0;
foreach($listDispenser as $q => $d)
{


?>


<tbody>

<tr>
    <td colspan="8"><?=$d;?></td>
</tr>
<?php 

    foreach($results[$q] as $q => $item)
    {

        $saldo = $item->qty;
        $harga = $item->harga;
        $subtotal = $saldo * $harga;

        $total_saldo_all += $saldo;
        $total += $subtotal;
?>
    
    <tr>
    <td style="text-align: center;"><?=($q+1);?></td>
     <td style="text-align: center;"><?=Yii::$app->formatter->asDate($item->tanggal);?></td>
     <td style="text-align: center;"><?=$item->shift->nama;?></td>
    <td style="text-align: right;"><?=$item->stok_akhir;?></td>
    <td style="text-align: right;"><?=$item->stok_awal;?></td>
    <td style="text-align: right;"><?=$saldo;?></td>
     <td style="text-align: right;"><?=Yii::$app->formatter->asInteger($harga);?></td>
     <td style="text-align: right;"><?=Yii::$app->formatter->asInteger($subtotal);?></td>
     </tr>
<?php 
    }


}
?>        
    
</tbody>
<tfoot>
    <tr>
        <td colspan="5" style="text-align: right">Total</td>
        <td style="text-align: right;"><?=$total_saldo_all;?></td>
        <td></td>
        <td style="text-align: right;"><?=Yii::$app->formatter->asInteger($total);?></td>
    </tr>
</tfoot>
</table>
<?php
}

else if(empty($results)){
?>

<div class="alert alert-info">Data Tidak Ditemukan</div>
<?php
}
?>


</div>
