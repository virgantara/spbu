<?php

namespace app\controllers;

use Yii;
use app\models\Transaksi;
use app\models\TransaksiSearch;
use app\models\Jurnal;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransaksiController implements the CRUD actions for Transaksi model.
 */
class TransaksiController extends Controller
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
     * Lists all Transaksi models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransaksiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Transaksi model.
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
     * Creates a new Transaksi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transaksi();
        $model->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
        if ($model->load(Yii::$app->request->post())) 
        {
            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try 
            {
                $perkiraan_lawan_id = $_POST['Transaksi']['perkiraan_lawan_id'];
                
                if($model->save())
                {
                    $params = [
                        'perkiraan_id' => $model->perkiraan_id,
                        'transaksi_id' => $model->id,
                        'no_bukti' => $model->no_bukti,
                        'jumlah' => $model->jumlah,
                        'keterangan' => $model->keterangan,
                        'keterangan_lawan' => $_POST['Transaksi']['keterangan_lawan'],
                        'tanggal' => $model->tanggal
                    ];

                    $kodeAsal = $model->perkiraan->kode;
                    if(
                        \app\helpers\MyHelper::startsWith($kodeAsal, '1') ||
                        \app\helpers\MyHelper::startsWith($kodeAsal, '5')
                    )
                    {
                        $jurnal = new Jurnal;
                        $jurnal->perkiraan_id = $params['perkiraan_id'];
                        $jurnal->debet = $params['jumlah'];
                        $jurnal->transaksi_id = $params['transaksi_id'];
                        $jurnal->no_bukti = $params['no_bukti'];
                        $jurnal->keterangan = $params['keterangan'];
                        $jurnal->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                        $jurnal->tanggal = $params['tanggal'];
                        if(!$jurnal->save())
                        {
                            print_r($jurnal->getErrors());exit;
                        }

                        if(!empty($perkiraan_lawan_id))
                        {
                            $kodeLawan = $model->perkiraanLawan->kode;
                            
                            $jurnal = new Jurnal;
                            $jurnal->perkiraan_id = $model->perkiraan_lawan_id;
                            
                            $jurnal->transaksi_id = $params['transaksi_id'];
                            $jurnal->no_bukti = $params['no_bukti'];
                            $jurnal->kredit = $params['jumlah'];
                            $jurnal->keterangan = $params['keterangan_lawan'];
                            $jurnal->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                            $jurnal->tanggal = $params['tanggal'];
                            if(!$jurnal->save())
                            {
                                print_r($jurnal->getErrors());exit;
                            }

                        }
                    }

                    else if(
                        \app\helpers\MyHelper::startsWith($kodeAsal, '2') ||
                        \app\helpers\MyHelper::startsWith($kodeAsal, '3') ||
                        \app\helpers\MyHelper::startsWith($kodeAsal, '4')
                    )
                    {
                        $jurnal = new Jurnal;
                        $jurnal->perkiraan_id = $params['perkiraan_id'];
                        $jurnal->debet = 0;
                        $jurnal->transaksi_id = $params['transaksi_id'];
                        $jurnal->no_bukti = $params['no_bukti'];
                        $jurnal->kredit = $params['jumlah'];
                        $jurnal->keterangan = $params['keterangan'];
                        $jurnal->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                        $jurnal->tanggal = $params['tanggal'];
                        if(!$jurnal->save())
                        {
                            print_r($jurnal->getErrors());exit;
                        }

                        if(!empty($perkiraan_lawan_id))
                        {
                            $kodeLawan = $model->perkiraanLawan->kode;
                            
                            $jurnal = new Jurnal;
                            $jurnal->perkiraan_id = $model->perkiraan_lawan_id;
                            $jurnal->debet = $params['jumlah'];
                            $jurnal->transaksi_id = $params['transaksi_id'];
                            $jurnal->no_bukti = $params['no_bukti'];
                           
                            $jurnal->keterangan = $params['keterangan_lawan'];
                            $jurnal->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                            $jurnal->tanggal = $params['tanggal'];
                            if(!$jurnal->save())
                            {
                                print_r($jurnal->getErrors());exit;
                            }

                        }
                    }
                }
                else
                {
                    print_r($model->getErrors());exit;
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
                

           
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Transaksi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Transaksi model.
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
     * Finds the Transaksi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transaksi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transaksi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
