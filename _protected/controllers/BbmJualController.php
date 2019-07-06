<?php

namespace app\controllers;

use Yii;
use app\models\BbmJual;
use app\models\BbmDispenser;
use app\models\BbmJualSearch;
use app\models\SalesMasterBarang;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Transaksi;
use app\models\Perkiraan;
use app\models\Jurnal;

/**
 * BbmJualController implements the CRUD actions for BbmJual model.
 */
class BbmJualController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    private function inputKas($barang_id, $bulan, $tahun)
    {
       
            
        $listJualTanggal = BbmJual::getListJualTanggal($bulan, $tahun,$barang_id);
        
        $barang = SalesMasterBarang::find()->where(['id_barang'=>$barang_id])->one();
        $listDispenser = \app\models\BbmDispenser::getDataProviderDispensers($barang_id);  

        foreach($listJualTanggal->models as $tgl)
        {
            $listShift = BbmJual::getListJualShifts($tgl->tanggal,$barang_id);
            $listShifts[$tgl->tanggal] = $listShift;
            foreach($listShift as $shift)
            {
                 $subtotal_liter = 0;
                foreach($listDispenser->models as $disp)
                {
                    $params = [
                        'tanggal' => $tgl->tanggal,
                        'barang_id' => $barang_id,
                        'shift_id' => $shift->shift_id,
                        'dispenser_id' => $disp->id
                    ];

                    $dataProvider = $searchModel->searchBy($params);
                    $listData[$tgl->tanggal][$shift->shift_id][$disp->id] = $dataProvider;
                    $stok_awal = !empty($dataProvider) ? $dataProvider->stok_awal : 0;
                    $stok_akhir = !empty($dataProvider) ? $dataProvider->stok_akhir : 0;
                    $saldo = $stok_akhir - $stok_awal;
                    $subtotal_liter += $saldo;
                    
                    $harga = !empty($dataProvider) && $dataProvider->harga != 0 ? $dataProvider->harga : $harga;
                }

                $kode_transaksi = $barang->id_barang.'-'.$tgl->id.'-'.$shift->shift_id;
                $userLevel = Yii::$app->user->identity->access_role;    
                
                $userPt = Yii::$app->user->identity->perusahaan_id;
                $kas = \app\models\Kas::find()->where(['kode_transaksi'=>$kode_transaksi])->one();
                if(empty($kas))
                {
                    $kas = new \app\models\Kas;    
                    
                }

                $kas->kas_masuk = $subtotal_liter * $harga;
                $kas->perkiraan_id = $barang->perkiraan_id;
                $kas->perusahaan_id = $userPt;
                $kas->penanggung_jawab = Yii::$app->user->identity->username;
                $uk = 'besar';
                $kas->keterangan = $barang->perkiraan->nama.' '.$barang->nama_barang;
                $kas->tanggal = $tgl->tanggal;
                $kas->jenis_kas = 1; // kas masuk    
                $kas->perusahaan_id = $userPt;
                $kas->kas_besar_kecil = $uk;
                $kas->kode_transaksi = $kode_transaksi;

                $kas->save();
                
                \app\models\Kas::updateSaldo($uk,$bulan,$tahun);
            }
        }            
        
    }

    /**
     * Lists all BbmJual models.
     * @return mixed
     */
    public function actionIndex()
    {
        $barang = null;

        $searchModel = new BbmJualSearch();
        $dataProvider = null;

        $listDispenser = BbmDispenser::getListDispensers();

        $listData = [];
        $listShifts = [];
        $listJualTanggal = [];

        $results = [];

        if(!empty($_POST['barang_id']))
        {
            
            foreach($listDispenser as $q => $d)
            {
                $models = BbmJual::getListJualTanggal($_POST['bulan'], $_POST['tahun'],$_POST['barang_id'],$q);    
                $results[$q] = $models;
            }
            
          
            
            $barang = SalesMasterBarang::find()->where(['id_barang'=>$_POST['barang_id']])->one();
              
            
        }
    
        return $this->render('index',[
            'dataProvider' => $dataProvider,
            'barang'=>$barang,
            'results' => $results,
            // 'listShifts' => $listShifts,
            // 'listJualTanggal' => $listJualTanggal,
            'listDispenser' => $listDispenser,
            // 'listData' => $listData
        ]);
    }

    /**
     * Displays a single BbmJual model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BbmJual model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BbmJual();
        $model->perusahaan_id = Yii::$app->user->identity->perusahaan_id;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            //if callback returns true than commit transaction
            if ($model->load(Yii::$app->request->post())) 
            {

                $model->qty = $model->stok_akhir - $model->stok_awal;
                $model->save();

                $query = \app\models\DepartemenStok::find()->where([
                    'barang_id' => $model->barang_id,
                    'departemen_id' => $model->dispenser_id
                ]);

                $ds = $query->one();


                if(!empty($ds))
                {

                    $ds->stok = $ds->stok - $model->qty;
                    $ds->save();

                    $bbmLog = new \app\models\BbmDispenserLog;
                    $bbmLog->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                    $bbmLog->barang_id = $model->barang_id;
                    $bbmLog->shift_id = $model->shift_id;
                    $bbmLog->dispenser_id = $model->dispenser_id;
                    $bbmLog->tanggal = date('Y-m-d',strtotime($model->tanggal));
                    $bbmLog->jumlah = $model->stok_akhir;
                    $bbmLog->save();    
                }

                $pars = [
                    'kode_akun_lawan' => '1-1102',
                    'perkiraan_id' => $model->barang->perkiraan_jual_id,
                    'no_bukti' => 'INVOICE'.$model->id,
                    'keterangan' => 'Jual '.$model->barang->nama_barang,
                    'tanggal' => $model->tanggal,
                    'jumlah' => $model->qty * $model->barang->harga_jual
                ];

                Transaksi::insertTransaksi($pars);
                
                $transaction->commit();
                Yii::$app->session->setFlash('success', "Data telah tersimpan");
                // print_r($_POST);exit;
                if(!empty($_POST['input-saja']))
                return $this->redirect(['index']);
                else if(!empty($_POST['input-lagi']))
                    return $this->redirect(['create']);
            }    
            // else
            // {
            //     print_r($model->getErrors());exit;
            // }
             
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        

        return $this->render('create', [
            'model' => $model,
        ]);
            
    }

    /**
     * Updates an existing BbmJual model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Data telah tersimpan");
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BbmJual model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BbmJual model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BbmJual the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BbmJual::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
