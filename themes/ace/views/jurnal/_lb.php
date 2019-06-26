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
            <th style="text-align: center;" >KETERANGAN</th>
            <th style="text-align: center;">AWAL PERIODE</th>
    		<th style="text-align: center;">PERIODE BERJALAN</th>
            <th style="text-align: center;">AKHIR PERIODE</th>
            <th style="text-align: center;">KOREKSI</th>
    		<th style="text-align: center;">FISKAL</th>
    		
    	</tr>
    	</thead>
    	<tbody>
            <tr>
                <td colspan="6"><strong>PENDAPATAN</strong></td>
            </tr>
    		<?php 
            $total = 0;
            $total_debet = 0;
            $total_kredit = 0;
            $i = 0;
            $total_periode_berjalan_pendapatan = 0;
            $total_awal = 0;
            $total_akhir = 0;
    		foreach($pendapatan->perkiraans as $q1 => $m1)
    		{
                foreach($m1->perkiraans as $q2 => $m2)
                {
                    $debet = $results['pendapatan'][$m2->id]['debet'];
                    $kredit = $results['pendapatan'][$m2->id]['kredit'];
                   $total_debet += $debet;
                   $total_kredit += $kredit;
                   $periode_berjalan = -1 * ($debet - $kredit);
                   $total_periode_berjalan_pendapatan += $periode_berjalan;
                   $awal = 0;
                   $akhir = $awal + $periode_berjalan;

                   $total_awal += $awal;
                   $total_akhir += $akhir;
    		?>
    		<tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$m2->nama;?></td>
    			<td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($awal,2);?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($akhir,2);?></td>
    			<td style="text-align: right"></td>
                <td style="text-align: right"></td>
    		</tr>
            

            <?php
                }
    	    }
    		?>
            <tr>
                <td style="text-align: right"><strong>Total Pendapatan</strong></td>
                <td style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($total_awal,2);?></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_pendapatan,2);?></strong></td>
                <td style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($total_akhir,2);?></td>
                <td style="text-align: right"></td>
                <td style="text-align: right"></td>
                
                
            </tr>
            <tr>
                <td colspan="6"><strong>BEBAN</strong></td>
            </tr>
            <?php 
            $total_debet = 0;
            $total_kredit = 0;
            $total_periode_berjalan_beban = 0;
            $total_awal = 0;
            $total_akhir = 0;
            foreach($beban->perkiraans as $q1 => $m1)
            {
                foreach($m1->perkiraans as $q2 => $m2)
                {
                    $debet = $results['beban'][$m2->id]['debet'];
                    $kredit = $results['beban'][$m2->id]['kredit'];
                   $total_debet += $debet;
                   $total_kredit += $kredit;
                   $periode_berjalan = ($debet - $kredit);
                   $total_periode_berjalan_beban += $periode_berjalan;
                   $awal = 0;
                   $akhir = $awal + $periode_berjalan;

                   $total_awal += $awal;
                   $total_akhir += $akhir;
            ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$m2->nama;?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($awal,2);?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($akhir,2);?></td>
                <td  style="text-align: right"></td>
                <td style="text-align: right"></td>
                
            </tr>
            

            <?php
                }
            }
            ?>
            <tr>
                <td style="text-align: right"><strong>Total Beban</strong></td>
                <td style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($total_awal,2);?></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_beban,2);?></strong></td>
                <td style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($total_akhir,2);?></td>
                <td style="text-align: right"></td>
                <td style="text-align: right"></td>
                
                
            </tr>
    	</tbody>
        <tfoot>
            <tr>
                <td style="text-align: right"><strong>Laba Rugi</strong></td>
                <td style="text-align: right"></td>
                <td style="text-align: right"><strong>
                    <?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_pendapatan - $total_periode_berjalan_beban,2);?>
                        
                    </strong></td>
                <td style="text-align: right"></td>
                <td style="text-align: right"></td>
                <td style="text-align: right"></td>
                
                
            </tr>
        </tfoot>
    </table>