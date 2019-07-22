<?php 
if(!empty($export)){
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="laporan_neraca.xls"');
            header('Cache-Control: max-age=0');

     ?>
    <table>
        <tr>
            <td colspan="8" style="text-align: center">
                
                <h1>Laporan Neraca</h1>
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
    		<th style="text-align: center;">JUMLAH</th>
    	</tr>
    	</thead>
    	<tbody>
            <tr>
                <td colspan="6"><strong>AKTIVA</strong></td>
            </tr>
            <tr>
                <td colspan="6"><strong>&nbsp;&nbsp;&nbsp;&nbsp;AKTIVA LANCAR</strong></td>
            </tr>
    		<?php 
            $total = 0;
            $i = 0;
            $total_aktiva = 0;

    		foreach($aktiva_lancar as $q1 => $m1)
    		{

                $data = $results['aktiva_lancar'][$m1->id];
                // foreach($m1->perkiraans as $q2 => $m2)
                // {
                $jumlah = $data['jumlah'];
                if($jumlah == 0) continue;
                    $kode = $data['kode'];
                    $nama = $data['nama'];
                    $total_aktiva += $jumlah;
                    
    		?>
    		<tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td></td>
    			<td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($jumlah,2);?></td>
                
    		</tr>
            

            <?php
                // }
    	    }
    		?>
            <tr>
                <td colspan="6"><strong>&nbsp;&nbsp;&nbsp;&nbsp;AKTIVA TETAP</strong></td>
            </tr>
            <?php 
            $i = 0;

            foreach($aktiva_tetap as $q1 => $m1)
            {

                $data = $results['aktiva_tetap'][$m1->id];
                // foreach($m1->perkiraans as $q2 => $m2)
                // {
                $jumlah = $data['jumlah'];
                if($jumlah == 0) continue;
                    $kode = $data['kode'];
                    $nama = $data['nama'];
                    $total_aktiva += $jumlah;
                    
            ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($jumlah,2);?></td>
                
            </tr>
            

            <?php
                // }
            }
            ?>
            <tr>
                <td style="text-align: right" colspan="2"><strong>Total Aktiva</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_aktiva,2);?></strong></td>
                
                
            </tr>
            <tr>
                <td colspan="6"><strong>PASIVA</strong></td>
            </tr>
             <tr>
                <td colspan="6"><strong>&nbsp;&nbsp;&nbsp;&nbsp;KEWAJIBAN/HUTANG</strong></td>
            </tr>
            <?php 
            $i = 0;
            $total_pasiva = 0;

            foreach($hutang as $q1 => $m1)
            {

                $data = $results['hutang'][$m1->id];
                // foreach($m1->perkiraans as $q2 => $m2)
                // {
                $jumlah = $data['jumlah'];
                if($jumlah == 0) continue;
                    $kode = $data['kode'];
                    $nama = $data['nama'];
                    $total_pasiva += $jumlah;
                    
            ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($jumlah,2);?></td>
                
            </tr>
            

            <?php
                // }
            }
            ?>
            <tr>
                <td colspan="6"><strong>&nbsp;&nbsp;&nbsp;&nbsp;MODAL</strong></td>
            </tr>
            <?php 
            $i = 0;
            
            foreach($modal as $q1 => $m1)
            {

                $data = $results['modal'][$m1->id];
                // foreach($m1->perkiraans as $q2 => $m2)
                // {
                $jumlah = $data['jumlah'];
                if($jumlah == 0) continue;
                    $kode = $data['kode'];
                    $nama = $data['nama'];
                    $total_pasiva += $jumlah;
                    
            ?>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nama;?></td>
                <td></td>
                <td  style="text-align: right"><?=\app\helpers\MyHelper::formatRupiah($jumlah,2);?></td>
                
            </tr>
            

            <?php
                // }
            }
            ?>
            <tr>
                <td style="text-align: right" colspan="2"><strong>Total Pasiva</strong></td>
                <td style="text-align: right"><strong><?=\app\helpers\MyHelper::formatRupiah($total_pasiva,2);?></strong></td>
                
                
            </tr>
    	</tbody>
        <tfoot>
           
        </tfoot>
    </table>
</div>