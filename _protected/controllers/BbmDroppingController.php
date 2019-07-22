<?php

namespace app\controllers;

use Yii;
use app\models\BbmDropping;
use app\models\BbmFaktur;
use app\models\BbmFakturItem;
use app\models\DepartemenStok;
use app\models\BbmDroppingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BbmDroppingController implements the CRUD actions for BbmDropping model.
 */
class BbmDroppingController extends Controller
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

    /**
     * Lists all BbmDropping models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $searchModel = new BbmDroppingSearch();
        $searchModel->bbm_faktur_id = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            
        ]);
    }

    /**
     * Displays a single BbmDropping model.
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
     * Creates a new BbmDropping model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new BbmDropping();
        $bbmFaktur = BbmFaktur::findOne($id);
        
        $model->bbm_faktur_id = $id;
        $transaction = \Yii::$app->db->beginTransaction();
        try 
        {
            if ($model->load(Yii::$app->request->post()) && $model->save()) 
            {

                $sumSo = BbmFakturItem::find()->where(['faktur_id'=>$id]);
                $sumSo = $sumSo->sum('jumlah');
                $sumLo = BbmDropping::find()->where(['bbm_faktur_id'=>$id]);
                $sumLo = $sumLo->sum('jumlah');
                $bbmFaktur->is_dropping = $sumLo >= $sumSo;
                $bbmFaktur->save();

                $stokUnit = DepartemenStok::find()->where([
                    'barang_id' => $model->barang_id,
                    'departemen_id' => $model->departemen_id
                ]);

                $stokUnit = $stokUnit->one();

                if(!empty($stokUnit))
                {
                    $stokUnit->stok_bulan_lalu = $stokUnit->stok;
                    $stokUnit->stok = $model->jumlah;
                    $stokUnit->hb = $model->barang->harga_beli;
                    $stokUnit->hj = $model->barang->harga_jual;

                }

                else{
                    $stokUnit = new DepartemenStok;
                    $stokUnit->stok_minimal = 100;
                    $stokUnit->hb = $model->barang->harga_beli;
                    $stokUnit->hj = $model->barang->harga_jual;
                    $stokUnit->stok_bulan_lalu = 0;
                    $stokUnit->stok = $model->jumlah;
                    $stokUnit->barang_id = $model->barang_id;
                    $stokUnit->departemen_id = $model->departemen_id;
   
                }

                $stokUnit->tanggal = $model->tanggal;

                $pars = [
                    'kode_akun_lawan' => '1-1101',
                    'perkiraan_id' => $model->barang->perkiraan_beli_id,
                    'no_bukti' => $model->no_lo,
                    'keterangan' => 'Beli '.$model->barang->nama_barang,
                    'tanggal' => $model->tanggal,
                    'jumlah' => $stokUnit->stok * $model->barang->harga_beli
                ];

                \app\models\Transaksi::insertTransaksi($pars);

                if(!$stokUnit->save()){

                    print_r($stokUnit->getErrors());exit;
                }
                else{
                    
                    $params = [
                        'barang_id' => $stokUnit->barang_id,
                        'kode_transaksi' => 'DROPPING_'.$stokUnit->id,
                        'status' => 1,
                        'qty' => $stokUnit->stok,
                        'tanggal' => $model->tanggal,
                        'departemen_id' => $stokUnit->departemen_id,
                        'stok_id' => $stokUnit->id,
                        'keterangan' => 'DROPPING '.$stokUnit->barang->nama_barang.' Qty: '.$model->jumlah,
                    ];

                    $ks = \app\models\KartuStok::find()->where(['kode_transaksi'=>$params['kode_transaksi']])->one();

                    if(empty($ks))
                        \app\models\KartuStok::createKartuStok($params);
                    else
                        \app\models\KartuStok::updateKartuStok($params);

                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', "Data telah tersimpan");
                return $this->redirect(['index', 'id' => $model->bbm_faktur_id]);
            }
            

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            
            throw $e;
        }
        return $this->render('create', [
            'model' => $model,
            'bbmFaktur' => $bbmFaktur
        ]);
    }

    /**
     * Updates an existing BbmDropping model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $bbmFaktur = BbmFaktur::findOne($model->bbm_faktur_id);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $sumSo = BbmFakturItem::find()->where(['faktur_id'=>$model->bbm_faktur_id]);
            $sumSo = $sumSo->sum('jumlah');
            $sumLo = BbmDropping::find()->where(['bbm_faktur_id'=>$model->bbm_faktur_id]);
            $sumLo = $sumLo->sum('jumlah');

            $bbmFaktur->is_dropping = $sumLo >= $sumSo;

            $bbmFaktur->save();
            Yii::$app->session->setFlash('success', "Data telah tersimpan");
            return $this->redirect(['index', 'id' => $model->bbm_faktur_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'bbmFaktur' => $bbmFaktur
        ]);
    }

    /**
     * Deletes an existing BbmDropping model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['bbm-faktur/dropping']);
    }

    /**
     * Finds the BbmDropping model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BbmDropping the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BbmDropping::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
