<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use yii\helpers\Url;
use yii\grid\GridView;
use yii\web\JsExpression;
use app\models\SalesGudang;



$listDataGudang=SalesGudang::getListGudangs();
/* @var $this yii\web\View */
/* @var $model app\models\SalesFaktur */

$this->title = 'Data Faktur No:'.$model->no_faktur;
$this->params['breadcrumbs'][] = ['label' => 'Sales Fakturs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-faktur-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_faktur], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id_faktur], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>


<?php

$url = '';
$userRole = Yii::$app->user->identity->access_role;
$acl = [

    Yii::$app->user->can('kepalaGudang')
];
// if(in_array($userRole, $acl)){
    
    // if($model->is_approved !=1){
        $label = 'Setujui Faktur ini?';
        $kode = 1;
        $warna = 'info';
        echo Html::a($label, ['approve', 'id' => $model->id_faktur,'kode'=>$kode], [
            'class' => 'btn btn-'.$warna,
            'data' => [
                'confirm' => $label.' ? Jika Anda menekan iya, maka data dalam faktur akan masuk ke stok gudang.',
                'method' => 'post',
            ],
        ]);
    // }
    
// } 


?>
    </p>
 <?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissable">
         <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
         <i class="icon fa fa-check"></i><?= Yii::$app->session->getFlash('success') ?>
         
    </div>
<?php endif; ?>

 <?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-error alert-dismissable">
         <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
         <i class="icon fa fa-check"></i><?= Yii::$app->session->getFlash('error') ?>
         
    </div>
<?php endif; ?>
<div class="row">
<div class="col-lg-6 col-sm-12">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id_faktur',
            'suplier.nama',
            'no_faktur',
            'created',
            'tanggal_faktur',
            'tanggal_jatuh_tempo',
            'tanggal_dropping',
            'perusahaan.nama',
        ],
    ]) ?>
    </div>
    <div class="col-lg-6 col-sm-12" style="font-size: 24px;">
        <table width="100%" >
            <tr>
                <td width="70%" style="text-align: right;vertical-align: bottom;">Total Biaya&nbsp;:&nbsp;</td>
                <td style="text-align: right"><span id="total">Rp <?=$model->totalFakturFormatted;?></span></td>
            </tr>
        </table>
        
    </div>
</div>
      <div class="row" >
        <div class="col-xs-12">

<form class="form-horizontal">
    <div class="col-lg-6">
        
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-right">Gudang</label>
        <div class="col-sm-4">
             <?= Html::dropDownList('id_gudang',null,$listDataGudang, ['prompt'=>'..Pilih Gudang..','id'=>'id_gudang','class'=>'form-control']); ?>
        </div>
        <label class="col-sm-2 control-label no-padding-right">Obat</label>
        <div class="col-sm-4">
             <?php 

  \yii\jui\AutoComplete::widget([
    'name' => 'nama_barang_item',
    'id' => 'nama_barang_item',
    'clientOptions' => [
    'source' => Url::to(['sales-master-barang/ajax-search']),
    'autoFill'=>true,
    'minLength'=>'3',
    'select' => new JsExpression("function( event, ui ) {
       $('#id_barang').val(ui.item.id); 
       $('#kode_barang').val(ui.item.kode);
       $('#nama_barang').val(ui.item.nama);
       $('#id_satuan').val(ui.item.satuan);
       $('#jumlah').focus();
        
     }")],
    'options' => [
        'size' => '40'
    ]
 ]); 
    
    ?>
 <input type="text" id="nama_barang_item" placeholder="Kode Barang" class="col-xs-10 " />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-right">Kode</label>
        <div class="col-sm-4">
            <input id="kode_barang" type="text" class="form-control">
        </div>
        <label class="col-sm-2 control-label no-padding-right">Barang</label>
        <div class="col-sm-4">
            <input id="id_faktur" type="hidden" value="<?=$model->id_faktur;?>">
                <input id="id_gudang" type="hidden">
                <input id="id_barang" type="hidden">
                <input id="nama_barang" type="text" class="form-control">
        </div>
    </div>
   
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-right">Satuan</label>
        <div class="col-sm-4">
            <input id="id_satuan" type="text" class="form-control">
            
            
        </div>
        <label class="col-sm-2 control-label no-padding-right">Qty</label>
        <div class="col-sm-4">
            <input id="jumlah" type="number" class="form-control">
        </div>
    </div>
   
</div>
    <div class="col-lg-6">
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-right">Exp Date</label>
            <div class="col-sm-4">
                <input name="exp_date"  type="text" id="exp_date" />
                    
            </div>
            <label class="col-sm-2 control-label no-padding-right">Batch No.</label>
            <div class="col-sm-4">
                <input id="no_batch" type="text" class="form-control">
            </div>
        </div>
       
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-right">Harga Nett (Rp)</label>
            <div class="col-sm-3">
                <input id="harga_netto" type="number" class="form-control">
            </div>
            <label class="col-sm-1 control-label no-padding-right">PPn (%)</label>
            <div class="col-sm-2">
                <input id="ppn" type="number" min="0" max="100" class="form-control" >
            </div>
            <label class="col-sm-2 control-label no-padding-right">Diskon (%)</label>
            <div class="col-sm-2">
                <input id="diskon" type="number" min="0" max="100" class="form-control">
            </div>
        </div>
      
        <div class="form-group">
            <div class="col-sm-9">
                <button class="btn btn-sm btn-primary" id="input-barang"><i class="fa fa-plus"></i> Tambah</button>
            </div>
        </div>
    </div>
</form>
    
</div>
    </div>
<p>
        <div id="alert-message" style="display: none"></div>
       

    </p>
    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-container']); ?>   
 <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showFooter' => true,
        'footerRowOptions'=>['style'=>'text-align:right;'],
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id_barang',
            
            'namaGudang',
            'namaBarang',
            'no_batch',
            'exp_date',
            
            'jumlah',
            'id_satuan',
             [
             'attribute' =>'harga_netto',
             // 'footer' => \app\models\SalesFaktur::getTotal($dataProvider->models),
             'format'=>'Currency',
             'contentOptions' => ['class' => 'text-right'],
            ],
            'diskon',
            'ppn',
            // [
            //     'attribute'=> 'harga_beli',
            //     'footer' => '<strong>Total</strong>',
            // ],
            
            [
             'attribute' =>'harga_jual',
             // 'footer' => \app\models\SalesFaktur::getTotal($dataProvider->models),
             'format'=>'Currency',
             'contentOptions' => ['class' => 'text-right'],
            ],
            [
             'attribute' =>'harga_beli',
             'footer' => 'Total',
             'format'=>'Currency',
             'contentOptions' => ['class' => 'text-right'],
            ],
            [
             'header' =>'Subtotal',
             'value' => function($model){
                return $model->harga_beli * $model->jumlah;
             },
             'footer' => '<b>'.$model->totalFakturFormatted.'</b>',
             'format'=>'Currency',
             'contentOptions' => ['class' => 'text-right'],
            ],
            

            //'created',
            //'id_perusahaan',
            //'id_gudang',

            [
                'class' => 'yii\grid\ActionColumn',
                // 'visible'=>Yii::$app->user->can('adm'),
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                       return Html::a('<span class="glyphicon glyphicon-pencil"></span>', 'javascript:void(0)', [
                                   'title'        => 'update',

                                   'data-item' => $model->id_faktur_barang,
                                   'class' => 'update-faktur-item',
                                    
                                    // 'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    // 'data-method'  => 'post',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                   'title'        => 'delete',
                                    'onclick' => "
                                    if (confirm('Are you sure you want to delete this item?')) {
                                        $.ajax('$url', {
                                            type: 'POST'
                                        }).done(function(data) {
                                            var d = $.parseJSON(data);
                                            $.pjax.reload({container: '#pjax-container'});
                                            $('#total').html(d.items);
                                            $('#alert-message').html('<div class=\"alert alert-success\">Data berhasil dihapus</div>');
                                            $('#alert-message').show();    
                                            $('#alert-message').fadeOut(2500);
                                        });
                                    }
                                    return false;
                                ",
                                    // 'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    // 'data-method'  => 'post',
                        ]);
                    }
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    
                    if ($action === 'delete') {
                        $url =Url::to(['sales-faktur-barang/delete','id'=>$model->id_faktur_barang]);
                        return $url;
                    }

                    else if ($action === 'update') {
                        $url =Url::to(['sales-faktur-barang/update','id'=>$model->id_faktur_barang]);
                        return $url;
                    }

                }
            ],
        ],
    ]); ?>
    
        <?php \yii\widgets\Pjax::end(); ?>
</div>
<?php

$this->registerJs(' 

    $(document).on(\'click\',\'.update-faktur-item\', function() {
        
        var fid = $(this).attr("data-item");

        $.ajax({
            type : "POST",
            dataType : "json",
            url : "'.\yii\helpers\Url::to(['/sales-faktur-barang/ajax-load-item']).'",
            data : {fakturItem : fid},
            
            success : function(hsl){
                if(hsl.code == 200){
                    var item = hsl.item;
                    $("#update_exp_date").datetextentry("set_date",item.exp_date); 
                    $("#update_id_faktur_barang").val(fid);
                    $("#update_id_barang").val(item.id_barang);
                    $("#update_kode_barang").val(item.kode_barang);
                    $("#update_nama_barang").val(item.nama_barang);
                    $("#update_id_satuan").val(item.id_satuan);
                    $("#update_id_gudang").val(item.id_gudang);
                    $("#update_no_batch").val(item.no_batch);
                    $("#update_exp_date").val(item.exp_date);
                    $("#update_jumlah").val(item.jumlah);
                    $("#update_harga_netto").val(item.harga_netto);
                    $("#update_ppn").val(item.ppn);
                    $("#update_diskon").val(item.diskon);
                    $("#test").trigger("click");
                }

                else{
                    alert(hsl.message);
                }
                

            }
        });
        


    });

     $(document).on(\'click\',\'#btn-update-faktur-item\', function() {
        
        var obj = new Object;
        obj.id_faktur = "'.$model->id_faktur.'";
        obj.id_faktur_barang = $("#update_id_faktur_barang").val();
        obj.id_barang = $("#update_id_barang").val();
        obj.id_gudang = $("#update_id_gudang").val();
        obj.jumlah = $("#update_jumlah").val();
        obj.harga_netto = $("#update_harga_netto").val();
        obj.id_satuan = $("#update_id_satuan").val();
        obj.ppn = $("#update_ppn").val();
        obj.diskon = $("#update_diskon").val();
        obj.exp_date = $("#update_exp_date").val();
        obj.no_batch = $("#update_no_batch").val();
        $.ajax({
            type : "POST",
            dataType : "json",

            url : "'.\yii\helpers\Url::to(['/sales-faktur-barang/ajax-update']).'",
            data : {fakturItem : obj},
            
            success : function(hsl){
                $.pjax.reload({container: \'#pjax-container\'});
                getTotal(obj.id_faktur);
                $("#alert-message").show();
                if(hsl.code == "success")
                    $("#update_kd_barang").focus();
                $("#alert-message").html("<div class=\'alert alert-"+hsl.code+"\' >"+hsl.message+"</div>");
                $("#alert-message").fadeOut(1000);
            }
        });


    });

    $(document).on(\'keydown\',\'input\', function(e) {
        var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
        
        if (e.ctrlKey && e.keyCode == 13) {
            $(\'form\').submit();
        }
        else if(key == 13) {
            e.preventDefault();

            var inputs = $(this).closest(\'form\').find(\':input:visible\');
                  
            inputs.eq( inputs.index(this)+ 1 ).focus().select();
           


        }
    });

    $(document).ready(function(){
        $("#exp_date").datetextentry(); 
        $("#update_exp_date").datetextentry(); 
        


        $("#input-barang").click(function(e){
            e.preventDefault();
            var diskon = isNaN($("#diskon").val())  || $("#diskon").val()==\'\' ? 0 : $("#diskon").val();
            var ppn = isNaN($("#ppn").val())  || $("#ppn").val()==\'\'? 0 : $("#ppn").val();
            var harga_netto = isNaN($("#harga_netto").val()) || $("#harga_netto").val()==\'\' ? 0 : $("#harga_netto").val();

            var obj = new Object;
            obj.id_faktur = "'.$model->id_faktur.'";
            obj.id_barang = $("#id_barang").val();
            obj.id_gudang = $("#id_gudang").val();
            obj.jumlah = $("#jumlah").val();
            obj.harga_netto = harga_netto;
            obj.id_satuan = $("#id_satuan").val();
            obj.ppn = ppn;
            obj.diskon = diskon;;
            obj.exp_date = $("#exp_date").val();
            obj.no_batch = $("#no_batch").val();
            $.ajax({
                type : "POST",
                dataType : "json",

                url : "'.\yii\helpers\Url::to(['/sales-faktur-barang/ajax-create']).'",
                data : {fakturItem : obj},
                
                success : function(hsl){
                    $.pjax.reload({container: \'#pjax-container\'});
                    
                    
                    $("#alert-message").show();
                    if(hsl.code == "success"){
                        $("#kd_barang").focus();
                        $("#total").html(hsl.items);
                    }
                    $("#alert-message").html("<div class=\'alert alert-"+hsl.code+"\' >"+hsl.message+"</div>");
                    // window.location = "'.\yii\helpers\Url::to(['/sales-faktur/view','id'=>$model->id_faktur]).'";
                }
            });
        });

        
    });', \yii\web\View::POS_READY);

?>


<?php 

\yii\bootstrap\Modal::begin([
    'header' => '<h2>Update Faktur Item</h2>',
    'toggleButton' => ['label' => '','id'=>'test','style'=>'display:none'],
    
]);

?>
<form class="form-horizontal" role="form">
        
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-right">Gudang</label>
        <div class="col-sm-9">
            <input type="hidden" id="update_id_faktur_barang"/>
             <?= Html::dropDownList('update_id_gudang',null,$listDataGudang, ['prompt'=>'..Pilih Gudang..','id'=>'update_id_gudang','class'=>'form-control']); ?>
        </div>
    </div>
    <div class="form-group">

        <label class="col-sm-2 control-label no-padding-right">Obat</label>
        <div class="col-sm-9">
             <?php 
     $url = \yii\helpers\Url::to(['/sales-stok-gudang/ajax-barang']);
    
    $template = '<div><p class="repo-language">{{nama}}</p>' .
    '<p class="repo-name">{{kode}}</p>';
    echo \kartik\typeahead\Typeahead::widget([
    'name' => 'update_kd_barang',
    'value' => '',
    'options' => ['placeholder' => 'Ketik nama barang ...',],
    'pluginOptions' => ['highlight'=>true],
    'pluginEvents' => [
        "typeahead:select" => "function(event,ui) { 
           $('#update_id_barang').val(ui.id); 
           $('#update_kode_barang').val(ui.kode);
           $('#update_nama_barang').val(ui.nama);
           $('#update_id_satuan').val(ui.satuan);
           $('#update_jumlah').focus();
        }",
    ],
    
    'dataset' => [
        [
            'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
            'display' => 'value',
            // 'prefetch' => $baseUrl . '/samples/countries.json',
            'remote' => [
                'url' => Url::to(['sales-stok-gudang/ajax-barang']) . '?q=%QUERY',
                'wildcard' => '%QUERY'
            ],
            'templates' => [
                'notFound' => '<div class="text-danger" style="padding:0 8px">Data Item Barang tidak ditemukan.</div>',
                'suggestion' => new JsExpression("Handlebars.compile('{$template}')")
            ]
        ]
    ]
]);
    ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-right">Kode</label>
        <div class="col-sm-9">
            <input id="update_kode_barang" type="text" class="form-control">
        </div>
    </div>
    <div class="form-group">

        <label class="col-sm-2 control-label no-padding-right">Barang</label>
        <div class="col-sm-9">
            <input id="update_id_faktur" type="hidden" value="<?=$model->id_faktur;?>">
                <input id="update_id_gudang" type="hidden">
                <input id="update_id_barang" type="hidden">
                <input id="update_nama_barang" type="text" class="form-control">
        </div>
    </div>
   
    <div class="form-group">
        <label class="col-sm-2 control-label no-padding-right">Satuan</label>
        <div class="col-sm-4">
            <input id="update_id_satuan" type="text" class="form-control">
            
            
        </div>
    
        <label class="col-sm-2 control-label no-padding-right">Qty</label>
        <div class="col-sm-4">
            <input id="update_jumlah" type="number" class="form-control">
        </div>
    </div>
    <div class="form-group">
            <label class="col-sm-2 control-label no-padding-right">Exp Date</label>
            <div class="col-sm-4">
                <input name="update_exp_date"  type="text" id="update_exp_date" class="form-control"/>
                    
            </div>
       

            <label class="col-sm-2 control-label no-padding-right">Batch No.</label>
            <div class="col-sm-4">
                <input id="update_no_batch" type="text" class="form-control">
            </div>
        </div>
       
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-right">Harga Nett (Rp)</label>
            <div class="col-sm-3">
                <input id="update_harga_netto" type="number" class="form-control">
            </div>
            <label class="col-sm-1 control-label no-padding-right">PPn (%)</label>
            <div class="col-sm-2">
                <input id="update_ppn" type="number" min="0" max="100" class="form-control" >
            </div>
            <label class="col-sm-2 control-label no-padding-right">Diskon (%)</label>
            <div class="col-sm-2">
                <input id="update_diskon" type="number" min="0" max="100" class="form-control">
            </div>
        </div>
      

    <div class="clearfix form-actions">
        <div class="col-md-offset-3 col-md-9">
            <button class="btn btn-info" type="button" id="btn-update-faktur-item">
                <i class="ace-icon fa fa-check bigger-110"></i>
                Simpan
            </button>

            &nbsp; &nbsp; &nbsp;
            <button class="btn" type="reset">
                <i class="ace-icon fa fa-undo bigger-110"></i>
                Reset
            </button>
        </div>
    </div>
</form>
<?php

\yii\bootstrap\Modal::end();
?>

<?php 
$script = "

function getTotal(id){



     $.ajax({
        type : 'POST',
        data : {dataItem:id},
        url : '/sales-faktur/ajax-get-total-faktur',

        success : function(data){
            var hsl = $.parseJSON(data);
            $('#total').html(hsl.items);

        }
    });
}


";
$this->registerJs(
    $script,
    \yii\web\View::POS_READY
);


?>