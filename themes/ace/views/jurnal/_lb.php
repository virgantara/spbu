<?php 
if(!empty($export)){
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="laporan_neraca.xls"');
            header('Cache-Control: max-age=0');

     ?>
    <table>
        <tr>
            <td colspan="8" style="text-align: center">
                
                <h1>Laporan Laba Rugi</h1>
            </td>
        </tr>
    </table>
    <?php
}

$total_barang_untuk_jual = 0;
?>
<div class="col-lg-6">
<table class="table table-bordered table-striped">
    	<thead>
    		<tr>
            <th style="text-align: center;" >KETERANGAN</th>
            <th style="text-align: center;" >QTY</th>
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
                <td></td>
    			<td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
                
    		</tr>
            

            <?php
                // }
    	    }
    		?>
            <tr>
                <td style="text-align: right" colspan="2"><strong>Total Pendapatan</strong></td>
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

                // $data = $results['persediaan_awal'][$m1->id];
                // foreach($m1->perkiraans as $q2 => $m2)
                // {
                $nama = $m1->barang->nama_barang;
                $jumlah = $m1->sisa;
                $periode_berjalan = $jumlah * $m1->barang->harga_beli;
                $total_periode_berjalan_persediaan_awal += $periode_berjalan;
                    
                // if($jumlah == 0) continue;
            ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td style="text-align: right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$jumlah;?> <?=$m1->barang->id_satuan;?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
               
            </tr>
            

            <?php
                // }
            }

            $total_barang_untuk_jual += $total_periode_berjalan_persediaan_awal;
            ?>
            <tr>
                <td style="text-align: right" colspan="2"><strong>Total Persediaan</strong></td>
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

                
                $nama = $m1->barang->nama_barang;
                $jumlah = $m1->jumlah;
                $periode_berjalan = $jumlah  * $m1->barang->harga_beli;
                $total_periode_berjalan_pembelian += $periode_berjalan;
            ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?> tgl <?=date('d/m/Y',strtotime($m1->tanggal));?></td>
                 <td style="text-align: right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$jumlah;?> <?=$m1->barang->id_satuan;?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
               
            </tr>
            

            <?php
                // }
            }
            $total_barang_untuk_jual += $total_periode_berjalan_pembelian;
            ?>
            <tr>
                <td style="text-align: right" colspan="2"><strong>Total Pembelian</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_pembelian,2);?></strong></td>
                
                
            </tr>
            <tr>
                <td style="text-align: right" colspan="2"><strong>Nilai Barang tersedia untuk penjualan</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_barang_untuk_jual,2);?></strong></td>
                
                
            </tr>
            <tr>
                <td colspan="6"><strong>PERSEDIAAN AKHIR</strong></td>
            </tr>
            <?php 
            $total = 0;
            $i = 0;
            $total_periode_berjalan_persediaan_akhir = 0;
            $total_awal = 0;
            $total_akhir = 0;
            foreach($persediaan_akhir as $q1 => $m1)
            {

                // $data = $results['persediaan_awal'][$m1->id];
                // foreach($m1->perkiraans as $q2 => $m2)
                // {
                $nama = $m1->barang->nama_barang;
                $jumlah = $m1->sisa;
                $periode_berjalan = $jumlah * $m1->barang->harga_beli;
                $total_periode_berjalan_persediaan_akhir += $periode_berjalan;
                    
                // if($jumlah == 0) continue;
            ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td style="text-align: right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$jumlah;?> <?=$m1->barang->id_satuan;?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
               
            </tr>
            

            <?php
                // }
            }

            $hpp = $total_barang_untuk_jual - $total_periode_berjalan_persediaan_akhir;
            $laba_kotor = $total_periode_berjalan_pendapatan - $hpp;
            ?>
            <tr>
                <td style="text-align: right" colspan="2"><strong>Total Persediaan Akhir</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_persediaan_akhir,2);?></strong></td>
                
                
            </tr>
            </tr>
            <tr>
                <td style="text-align: right" colspan="2"><strong>HPP</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($hpp,2);?></strong></td>
                
                
            </tr>
            <tr>
                <td style="text-align: right" colspan="2"><strong>Laba Kotor</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($laba_kotor,2);?></strong></td>
                
                
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
                <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
                
            </tr>
            <?php
            }
            ?>
             <tr>
                <td colspan="6"><strong>BEBAN LAIN-LAIN</strong></td>
            </tr>
            <?php
            foreach($bebanLain as $q1 => $m1)
            {
                    $data = $results['bebanLain'][$m1->id];
                
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
                <td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($periode_berjalan,2);?></td>
                
            </tr>
            

            <?php
                // }
            }
            ?>
            <tr>
                <td style="text-align: right" colspan="2"><strong>Total Beban</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_periode_berjalan_beban,2);?></strong></td>
                
                
            </tr>
    	</tbody>
        <tfoot>
            <tr>
                <td style="text-align: right" colspan="2"><strong>Laba Bersih</strong></td>
                <td style="text-align: right"><strong>
                    <?=\app\helpers\MyHelper::formatRupiah($laba_kotor - $total_periode_berjalan_beban,2);?>
                        
                    </strong></td>
                
                
            </tr>
        </tfoot>
    </table>
</div>