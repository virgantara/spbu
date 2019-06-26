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
    		
    	</tr>
    	</thead>
    	<tbody>
    		<?php 
            $total = 0;
            $total_debet = 0;
            $total_kredit = 0;
            $i = 0;
            if(!empty($listakun))
    		foreach($listakun->perkiraans as $q1 => $m1)
    		{
                // $total += $model->subtotal;
    			// print_r($model);exit;
                // $subtotal = $m->harga_beli * $m->jumlah;
                $j = 0;
               
                $debet = $results[$m1->id]['debet'];
                $kredit = $results[$m1->id]['kredit'];
               $total_debet += $debet;
               $total_kredit += $kredit;
    		?>
    		
            <tr>

                <td></td>
                <td></td>
                <td></td>
                <td><?=$m1->kode;?></td>
                <td><?=$m1->nama;?></td>
                <td style="text-align: right">
                <?php
                if(!empty($debet))
                    echo \app\helpers\MyHelper::formatRupiah($debet,2);
                
                ?>
                        
                </td>
                <td style="text-align: right"><?php
                if(!empty($kredit))
                    echo \app\helpers\MyHelper::formatRupiah($kredit,2);
                
                ?>
                    </td>
                
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
                
                
            </tr>
        </tfoot>
    </table>