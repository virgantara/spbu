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
    		<th style="text-align: center;">PERIODE BERJALAN</th>
    	</tr>
    	</thead>
    	<tbody>
            <tr>
                <td colspan="6"><strong>PENJUALAN</strong></td>
            </tr>
    		<?php 
            $total = 0;
            $total_debet = 0;
            $total_kredit = 0;
            $i = 0;
            $total_periode_berjalan_pendapatan = 0;
            $total_awal = 0;
            $total_akhir = 0;

    		foreach($pendapatan as $q1 => $m1)
    		{

                $data = $results['pendapatan'][$m1->id];
                // foreach($m1->perkiraans as $q2 => $m2)
                // {
                $jumlah = $data['jumlah'];
                if($jumlah == 0) continue;
                    $kode = $data['kode'];
                    $nama = $data['nama'];
                    $periode_berjalan = $jumlah;
                    $total_periode_berjalan_pendapatan += $periode_berjalan;
                    $awal = 0;
                    $akhir = $awal + $periode_berjalan;

                    $total_awal += $awal;
$total_akhir += $akhir;
    		?>
    		<tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
    			<td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
                
    		</tr>
            

            <?php
                // }
    	    }
    		?>
            <tr>
                <td style="text-align: right"><strong>Total Pendapatan</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_pendapatan,2);?></strong></td>
                
                
            </tr>
            <tr>
                <td colspan="6"><strong>PERSEDIAAN AWAL</strong></td>
            </tr>
            <?php 
            $total = 0;
            $i = 0;
            $total_periode_berjalan_persediaan_awal = 0;
            $total_awal = 0;
            $total_akhir = 0;

            foreach($persediaan_awal as $q1 => $m1)
            {

                $data = $results['persediaan_awal'][$m1->id];
                // foreach($m1->perkiraans as $q2 => $m2)
                // {
                    $kode = $data['kode'];
                    $nama = $data['nama'];
                    $jumlah = $data['jumlah'];
                    $periode_berjalan = $jumlah;
                    $total_periode_berjalan_persediaan += $periode_berjalan;
                    $awal = 0;
                    $akhir = $awal + $periode_berjalan;

                    $total_awal += $awal;
$total_akhir += $akhir;
                if($jumlah == 0) continue;
            ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
               
            </tr>
            

            <?php
                // }
            }
            ?>
            <tr>
                <td style="text-align: right"><strong>Total Persediaan</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_persediaan_awal,2);?></strong></td>
                
                
            </tr>
            <tr>
                <td colspan="6"><strong>PEMBELIAN</strong></td>
            </tr>
            <?php 
            $total = 0;
            $i = 0;
            $total_periode_berjalan_pembelian = 0;
            $total_awal = 0;
            $total_akhir = 0;

            foreach($pembelian as $q1 => $m1)
            {

                $data = $results['pembelian'][$m1->id];
                // foreach($m1->perkiraans as $q2 => $m2)
                // {
                    $kode = $data['kode'];
                    $nama = $data['nama'];
                    $jumlah = $data['jumlah'];
                    $periode_berjalan = $jumlah;
                    $total_periode_berjalan_pembelian += $periode_berjalan;
                    $awal = 0;
                    $akhir = $awal + $periode_berjalan;

                    $total_awal += $awal;
$total_akhir += $akhir;
                if($jumlah == 0) continue;
            ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
               
            </tr>
            

            <?php
                // }
            }
            ?>
            <tr>
                <td style="text-align: right"><strong>Total Pembelian</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_pembelian,2);?></strong></td>
                
                
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
            foreach($beban as $q1 => $m1)
            {
                    $data = $results['beban'][$m1->id];
                
                   $kode = $data['kode'];
                    $nama = $data['nama'];
                    $jumlah = $data['jumlah'];
                   $periode_berjalan = $jumlah;
                   $total_periode_berjalan_beban += $periode_berjalan;
                   $awal = 0;
                   $akhir = $awal + $periode_berjalan;

                   $total_awal += $awal;
                   $total_akhir += $akhir;
                   if($jumlah == 0) continue;
            ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
                
            </tr>
            

            <?php
                // }
            }
            ?>
            <tr>
                <td style="text-align: right"><strong>Total Beban</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_beban,2);?></strong></td>
                
                
            </tr>
    	</tbody>
        <tfoot>
            <tr>
                <td style="text-align: right"><strong>Laba Rugi</strong></td>
                <td style="text-align: right"><strong>
                    <?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_pendapatan - $total_periode_berjalan_beban,2);?>
                        
                    </strong></td>
                
                
            </tr>
        </tfoot>
    </table>