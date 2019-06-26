<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Penjualan */

$this->title = $model->kode_penjualan;
$this->params['breadcrumbs'][] = ['label' => 'Penjualan', 'url' => ['index-kasir']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="penjualan-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php 

        switch ($model->status_penjualan) {
            case 1:
                $label = 'SUDAH BAYAR';
                $st = 'success';
                break;
            case 2:
                $label = 'BON';
                $st = 'warning';
                break;
            default:
                $label = 'BELUM BAYAR';
                $st = 'danger';
                break;
        }
        
        echo '<div class="alert alert-'.$st.' " >
                   '.$label.'
                </div>&nbsp;';


        $userRole = Yii::$app->user->identity->access_role;
        $acl = [
            Yii::$app->user->can('kasir'),
            Yii::$app->user->can('operatorApotik')
        ];
        if(in_array($userRole, $acl)){
            

            $label = $model->status_penjualan ? '<i class="fa fa-money"></i> Batal Pembayaran' : '<i class="fa fa-money"></i> Setujui Pembayaran';
            $kode = $model->status_penjualan ? 0 : 1;
            $warna = $model->status_penjualan ? 'warning' : 'info';
            echo Html::a($label, ['bayar', 'id' => $model->id,'kode'=>$kode], [
                'class' => 'btn btn-'.$warna,
                'data' => [
                    'confirm' => 'Setujui Pembayaran ini?',
                    'method' => 'post',
                ],
            ]);
            echo '&nbsp;';

            if($model->status_penjualan){
                echo Html::a('<span class="glyphicon glyphicon-print"></span>&nbsp;Cetak Bukti', ['print-bayar', 'id' => $model->id], [
                    'class' => 'btn btn-success print-bayar',
                    'data-item' =>$model->id,
                    
                ]);
            }
            
        } 

        
        ?>


    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Total',
                'format' => 'raw',
                'value'=>function($model,$url){
                    $total = \app\models\Penjualan::getTotalSubtotalBulat($model);
                    $total = ceil($total/100) * 100;
                    return '<label style="font-size:24px;font-weight:bold">Rp '.\app\helpers\MyHelper::formatRupiah($total).'</label>';
                    
                },
            ],
            'kode_penjualan',
            'namaPasien',
            'RMPasien',
            'jenisPasien',
            'tanggal',
            'namaUnit',
            
            'created_at',
            
        ],
    ]) ?>

</div>
<?php
$script = "

function popitup(url,label) {
    var w = screen.width * 0.8;
    var h = screen.height * 0.5;
    var left = (screen.width  - w) / 2;
    var top = (screen.height- h) / 2;
    
    newwindow=window.open(url,label,'height='+h+',width='+w+',top='+top+',left='+left);
    if (window.focus) {newwindow.focus()}
    return false;
}


$(document).on('click','.print-bayar', function(e) {  // 'pjax:success' use if you have used pjax
    e.preventDefault();
    var url = $(this).attr('href');
    var id = $(this).attr('data-item');
    popitup(url,'bayar');
    
});


";
$this->registerJs(
    $script,
    \yii\web\View::POS_READY
);


?>