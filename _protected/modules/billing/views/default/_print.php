<?php
use yii\helpers\Url;
use yii\helpers\Html;
$fontfamily = 'Times';
$fontSize = '20px';
$fontSizeBawah = '12px';
?>
<table width="100%" style="height: 1px;margin: 0px">
    <tr>
        <td width="10%"></td>
        <td width="80%" style="text-align: center">
            <strong style="font-size: 13px;font-family: <?=$fontfamily;?>">RSUD KABUPATEN KEDIRI</strong><br>
            <span style="font-size:10px;font-family: <?=$fontfamily;?>">Jl. PAHLAWAN KUSUMA BANGSA NO 1 TLP (0354) 391718, 391169, 394956 FAX. 391833<BR>
            PARE KEDIRI (64213) email : rsud.pare@kedirikab.go.id</span>
        </td>
        <td width="10%"></td>
    </tr>
</table>
<hr style="height: 1px;margin: 0px">
<div style="text-align: center;margin: 0px;font-size:12px;font-family: <?=$fontfamily;?>">NO KWITANSI : <?=\app\helpers\MyHelper::appendZeros($model->id,8);?></div>
<table width="100%">
    <tr>
        <td width="55%" valign="top" style="border:1px solid">
            

<table style="font-size: <?=$fontSizeBawah;?>;font-family: <?=$fontfamily;?>">
    
     <tr>
        <td style="width: 100px" >No RM</td>
        <td style="width: 20px">:</td>
        <td style="width: 250px"><?=$model->custid;?></td>
    </tr>
     <tr>
        <td >Jenis Px</td>
        <td>:</td>
        <td><?=$model->jenis_customer;?></td>
    </tr>
    <tr>
        <td >Nama Px</td>
        <td>:</td>
        <td><?=$model->nama;?></td>
    </tr>

    <tr>
        <td >Alamat</td>
        <td>:</td>
        <td><?=!empty($pasien) ? $pasien['alamat'] : '-';?></td>
    </tr>
    <tr>
        <td >Unit</td>
        <td>:</td>
        <td><?=$model->issued_by;?></td>
    </tr>
    <tr>
        <td >No Resep</td>
        <td>:</td>
        <td><?=$model->kode_trx;?></td>
    </tr>
    <tr>
        <td >Tgl Resep</td>
        <td>:</td>
        <td><?=date('d/m/Y',strtotime($model->created_at));?></td>
    </tr>
    <tr>
        <td >Dokter</td>
        <td>:</td>
        <td><?=$model->person_in_charge;?></td>
    </tr>
    <tr>
        <td >Untuk Pembayaran</td>
        <td>:</td>
        <td><?=$model->jenis_tagihan;?></td>
    </tr>
</table>
        </td>
        <td width="45%"  style="border:1px solid" valign="top">
    <table style="font-size: <?=$fontSizeBawah;?>;font-family: <?=$fontfamily;?>">
    
    
     <tr>
        <td >Nominal</td>
        <td>:</td>
        <td style="font-weight: bold">Rp <?php
         $total = $model->nilai;
        $total = ceil($total/100) * 100;
        echo  \app\helpers\MyHelper::formatRupiah($total);
        
        ?></td>
    </tr>
    <tr>
        <td >Terbilang</td>
        <td>:</td>
        <td><?=ucwords(\app\helpers\MyHelper::terbilang($total));?></td>
    </tr>
</table>
<br><br>
<table width="100%">
    <tr>
        <td width="50%" style="text-align: center;font-size:12px;font-family: <?=$fontfamily;?>">
            Penyetor
           
            <br>
            <br>
            <br>
            <u><b>(........................)</b></u><br>
        </td>
        <td width="50%" style="text-align: center;font-size:12px;font-family: <?=$fontfamily;?>">
            
            Pare, <?=date('d M Y');?>
            <br>

            Petugas Kasir
           
            <br>
            <br>
            <br>
            <u><b>(<?=Yii::$app->user->identity->display_name;?>)</b></u><br>
            <?=Yii::$app->user->identity->nip;?>
            
            
        </td>
    </tr>
</table>
</td>
        
    </tr>
</table>

<table width="100%" >
    <tr>
        <td width="55%" valign="top">
        </td>
        <td width="45%" valign="center" style="text-align: center">
            <br>
          <h1 style="border:2px solid;">  <?=$model->issued_by;?></h1>
        </td>
    </tr>
</table>