<?php
use yii\helpers\Url;
use yii\helpers\Html;

?>
<table width="100%">
    <tr>
        <td width="10%"></td>
        <td width="80%" style="text-align: center">
            <strong style="font-size: 18px">RSUD KABUPATEN KEDIRI</strong><br>
            <span style="font-size:10px">Jl. PAHLAWAN KUSUMA BANGSA NO 1 TLP (0354) 391718, 391169, 394956 FAX. 391833<BR>
            PARE KEDIRI (64213) email : rsud.pare@kedirikab.go.id</span>
        </td>
        <td width="10%"></td>
    </tr>
</table>
<hr style="height: 1px;margin: 0px">
<h4 style="text-align: center;">SURAT PENGANTAR BAYAR OBAT</h4>
<table >
    <tr>
        <td style="width: 100px">No Resep</td>
        <td  style="width: 20px">:</td>
        <td style="width: 250px"><?=$model->kode_penjualan;?></td>
    </tr>
    <tr>
        <td >Tgl Resep</td>
        <td>:</td>
        <td><?=date('d-m-Y',strtotime($model->tanggal));?></td>
    </tr>

    <tr>
        <td >Tgl Cetak</td>
        <td>:</td>
        <td><?=date('d-m-Y');?></td>
    </tr>
     <tr>
        <td >No RM</td>
        <td>:</td>
        <td><?=$model->customer_id;?></td>
    </tr>
     <tr>
        <td >Jenis Pasien</td>
        <td>:</td>
        <td><?=$model->customer_id;?></td>
    </tr>
    <tr>
        <td >Nama Px</td>
        <td>:</td>
        <td><?=$model->customer_id;?></td>
    </tr>
    <tr>
        <td >Unit</td>
        <td>:</td>
        <td><?=$model->kode_daftar;?></td>
    </tr>
    <tr>
        <td >Dokter</td>
        <td>:</td>
        <td><?=$model->penjualanResep->dokter_id;?></td>
    </tr>
    <tr>
        <td >Nominal</td>
        <td>:</td>
        <td>Rp <?=\app\helpers\MyHelper::formatRupiah(\app\models\Penjualan::getTotalSubtotal($model),2);?></td>
    </tr>
</table>

<table width="100%">
    <tr>
        
        <td width="100%" style="text-align: center">
            Pare, <?=date('d-m-Y');?>
            <br>
            Petugas Apotek
           
            
            <br>
            <br>
            <u><b>(...................................)</b></u><br>
            
            
        </td>
    </tr>
</table>
<br>
<br>
<table width="100%" border="1">
    <tr>
        <td width="80%" style="text-align: center;vertical-align: top;">
            Telah diterima sesuai dengan jenis dan jumlah pemberian pada tanggal ................
            
        </td>
        <td width="20%" style="text-align: center">
             Penerima
            <br>
            <br>
            
            
            <br>
            <u><b>(...........................)</b></u><br>

        </td>
    </tr>
</table>