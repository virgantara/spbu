<?php

namespace app\controllers;

use Yii;
use app\models\Jurnal;
use app\models\JurnalSearch;
use app\models\Perkiraan;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * JurnalController implements the CRUD actions for Jurnal model.
 */
class JurnalController extends Controller
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

    public function actionLabaRugi()
    {
        $searchModel = new JurnalSearch();
        
        $pendapatan = Perkiraan::find();
        $pendapatan->where([
            'perusahaan_id'=>Yii::$app->user->identity->perusahaan_id,
            'kode' => 4
        ]);

        $beban = Perkiraan::find();
        $beban->where([
            'perusahaan_id'=>Yii::$app->user->identity->perusahaan_id,
            'kode' => 5
        ]);

        $results = [];
        $params = Yii::$app->request->queryParams;
        $pendapatan = $pendapatan->one();
        
        foreach($pendapatan->perkiraans as $q1 => $m1)
        {
            foreach($m1->perkiraans as $q2 => $m2)
            {
                $params['Jurnal']['perkiraan_id'] = $m2->id;
                $sumDebet = $searchModel->searchByTanggalAkun($params,'debet');
                $sumKredit = $searchModel->searchByTanggalAkun($params,'kredit');
                $results['pendapatan'][$m2->id] = [
                    'debet' => $sumDebet,
                    'kredit' => $sumKredit
                ];
            }
        }

        $beban = $beban->one();
        foreach($beban->perkiraans as $q1 => $m1)
        {
            foreach($m1->perkiraans as $q2 => $m2)
            {
                $params['Jurnal']['perkiraan_id'] = $m2->id;
                $sumDebet = $searchModel->searchByTanggalAkun($params,'debet');
                $sumKredit = $searchModel->searchByTanggalAkun($params,'kredit');
                $results['beban'][$m2->id] = [
                    'debet' => $sumDebet,
                    'kredit' => $sumKredit
                ];
            }
        }
        $model = new Jurnal;
        // print_r($results);exit;
        return $this->render('lb', [
            'searchModel' => $searchModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
            'pendapatan' => $pendapatan,
            'beban' => $beban,
            'results'=>$results
        ]);
    }

    public function actionBukuBesar()
    {

        $akun = Perkiraan::findOne($_GET['Jurnal']['perkiraan_id']);
        $listakun = $akun;
        $searchModel = new JurnalSearch();

        
        $results = [];
        $params = Yii::$app->request->queryParams;
        if(!empty($listakun))
        foreach($listakun->perkiraans as $q1 => $m1)
        {

            $params['Jurnal']['perkiraan_id'] = $m1->id;
            $sumDebet = $searchModel->searchByTanggalAkun($params,'debet');
            $sumKredit = $searchModel->searchByTanggalAkun($params,'kredit');
            
            $results[$m1->id] = [
                'debet' => $sumDebet,
                'kredit' => $sumKredit
            ];
        }
        $model = new Jurnal;
        return $this->render('bb', [
            'searchModel' => $searchModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
            'listakun' => $listakun,
            'results' => $results
        ]);
    }

    /**
     * Lists all Jurnal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new JurnalSearch();
        $dataProvider = $searchModel->searchByTanggal(Yii::$app->request->queryParams);
        $model = new Jurnal;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Jurnal model.
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
     * Creates a new Jurnal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Jurnal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Jurnal model.
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
     * Deletes an existing Jurnal model.
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
     * Finds the Jurnal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Jurnal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Jurnal::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
