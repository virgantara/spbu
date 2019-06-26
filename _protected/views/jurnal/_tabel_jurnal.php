<?php 
if(!empty($export)){
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="laporan_mutasi_masuk.xls"');
            header('Cache-Control: max-age=0');

     ?>
    <table>
        <tr>
            <td colspan="8" style="text-align: center">
                
                <h1>Laporan Tentang Penerimaan Obat</h1>
            </td>
        </tr>
    </table>
    <?php
}
?>

<table class="table table-bordered table-striped">
    	<thead>
    		<tr>
            <th>TANGGAL</th>
            <th>NO BUKTI</th>
    		<th>KETERANGAN</th>
            <th>NO AKUN</th>
            <th>NAMA AKUN</th>
    		<th>DEBET</th>
    		<th>KREDIT</th>
    		<th>SALDO</th>
    		
    	</tr>
    	</thead>
    	<tbody>
    		<?php 
            $total = 0;
            $total_debet = 0;
            $total_kredit = 0;
            $i = 0;
    		foreach($dataProvider->getModels() as $key => $m)
    		{
                // $total += $model->subtotal;
    			// print_r($model);exit;
                // $subtotal = $m->harga_beli * $m->jumlah;
                $j = 0;
               

               $total_debet += $m->debet;
               $total_kredit += $m->kredit;
    		?>
    		<tr>
                <td><?php 
                    echo date('d/m/Y',strtotime($m->tanggal));
                ?></td>
    			<td><?php
                    echo $m->no_bukti;
                ?></td>
    			<td><?php 
                    echo $m->keterangan;
                ?></td>
                <td><?=$m->perkiraan->kode;?></td>
                <td><?=$m->perkiraan->nama;?></td>
    			<td style="text-align: right"><?php
                if(!empty($m->debet))
                    echo \app\helpers\MyHelper::formatRupiah($m->debet,2);
                ?></td>
                <td style="text-align: right"><?php
                if(!empty($m->kredit))
                    echo \app\helpers\MyHelper::formatRupiah($m->kredit,2);
                ?></td>
                <td></td>
                
    		</tr>
         

            <?php
    	   }
    		?>

    	</tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right">Total</td>
                <td style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($total_debet,2);?></td>
                <td style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($total_kredit,2);?></td>
                <td style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($total,2);?></td>
                
            </tr>
        </tfoot>
    </table>