<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PerkiraanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Akun';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="perkiraan-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Akun', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row">
    <div class="col-xs-12">

        <table class="table table-striped">
            <thead>
            <tr>
                <th >Nama Akun</th>
                <th >Option</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach($listAkun as $m1)
            {


            ?>
            <tr>
                <td><?=$m1->kode?> <?=$m1->nama?></td>
                <td><?=\yii\helpers\Html::a('Update',\yii\helpers\Url::to(['perkiraan/update','id'=>$m1->id]));?></td>
            </tr>
            <?php
                foreach($m1->perkiraans as $m2)
                {


                ?>
                <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$m2->kode?> <?=$m2->nama?></td>
                   <td><?=\yii\helpers\Html::a('Update',\yii\helpers\Url::to(['perkiraan/update','id'=>$m2->id]));?></td>
                </tr>
                <?php
                    foreach($m2->perkiraans as $m3)
                    {


                    ?>
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=$m3->kode?>  <?=$m3->nama?></td>
                        <td><?=\yii\helpers\Html::a('Update',\yii\helpers\Url::to(['perkiraan/update','id'=>$m3->id]));?></td>
                    </tr>
                    <?php

                    }
                }
            }
            ?>
        </tbody>
        </table>        
    </div>
</div>
</div>
