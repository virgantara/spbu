<?php

namespace app\controllers;

use Yii;
use app\models\SalesFaktur;
use app\models\SalesFakturSearch;
use app\models\SalesStokGudang;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use yii\data\ActiveDataProvider;
use yii\helpers\Json;
/**
 * SalesFakturController implements the CRUD actions for SalesFaktur model.
 */
class SalesFakturController extends Controller
{
    /**
     * @inheritdoc
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

    public function actionAjaxGetTotalFaktur()
    {
        if (Yii::$app->request->isAjax) {

            $id = $_POST['dataItem'];
            
            $model = $this->findModel($id);
            $results = [
                'code' => 200,
                'message' => 'success',
                'items' => $model->totalFakturFormatted
            ];
            echo Json::encode($results);
        }
    }

    public function actionAjaxSearchFaktur($term)
    {
        if (Yii::$app->request->isAjax) {

            $results = [];

            $q = addslashes($term);

            foreach(SalesFaktur::find()->where(['like','no_faktur',$q])->andWhere(['id_perusahaan'=>Yii::$app->user->identity->perusahaan_id])->all() as $model) {
                $results[] = [
                    'id' => $model->id_faktur,
                    'label' => $model->no_faktur
                ];
            }
            echo Json::encode($results);
        }
    }


    public function actionApprove($id,$kode)
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try 
        {
            $model = $this->findModel($id);
            $model->is_approved = $kode;

            $model->save();

            // \app\models\RequestOrder::updateStok($id);

            if($kode==1)
            {
                foreach($model->salesFakturBarangs as $item)
                {
                    $sg = SalesStokGudang::find()->where([
                        'faktur_barang_id' => $item->id_faktur_barang,
                    ])->one();

                    if(empty($sg))     
                        $sg = new SalesStokGudang;

                    $sg->jumlah = $item->jumlah;
                    $sg->id_gudang = $item->id_gudang;
                    $sg->id_barang = $item->id_barang;
                    $sg->exp_date = $item->exp_date;
                    $sg->batch_no = $item->no_batch;
                    $sg->faktur_barang_id = $item->id_faktur_barang;                    
                    if($sg->validate()){
                        
                        $sg->save();
                        $barang = $item->barang;
                        $barang->harga_beli = $item->harga_beli;
                        $barang->harga_jual = $item->harga_jual;
                        // print_r($barang->harga_jual);exit;
                        $barang->save();

                        $listDepartemen = \app\models\Departemen::find()->where(['perusahaan_id'=>Yii::$app->user->identity->perusahaan_id])->all();

                        $api_baseurl = Yii::$app->params['api_baseurl'];
                        $client = new \yii\httpclient\Client(['baseUrl' => $api_baseurl]);

                        foreach($listDepartemen as $d)
                        {

                            $dept_id = $d->id;

                            $params = [
                                'dept_id' => $dept_id,
                                'barang_id' => $item->id_barang,
                                'exp_date' => $item->exp_date,
                                'batch_no' => $item->no_batch
                            ];
                            $response = $client->post('/integra/generate/stok', $params)->send();

                            $result = [];
                            
                            if ($response->isOk) 
                            {
                                $result = $response->data['values'];   
                                // print_r($response);exit;
                            }

                            else
                            {
                                print_r($response);exit;
                                
                            }
                        }

                        Yii::$app->session->setFlash('success', "Data telah tersimpan");
                    }
                    $params = [
                        'barang_id' => $item->id_barang,
                        'status' => 1,
                        'qty' => $item->jumlah,
                        'tanggal' => $model->tanggal_faktur,
                        'departemen_id' => Yii::$app->user->identity->departemen,
                        'stok_id' => $sg->id_stok,
                        'keterangan' => '',
                    ];
                    \app\models\KartuStok::createKartuStok($params);
                    
                }
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', "Data tersimpan");
            return $this->redirect(['view','id'=>$id]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


    /**
     * Lists all SalesFaktur models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SalesFakturSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalesFaktur model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);
        $searchModel = $model->getSalesFakturBarangs();

        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel,
            'pagination' => false
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new SalesFaktur model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalesFaktur();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_faktur]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SalesFaktur model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_faktur]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SalesFaktur model.
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
     * Finds the SalesFaktur model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalesFaktur the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalesFaktur::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
