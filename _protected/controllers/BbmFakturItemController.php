<?php

namespace app\controllers;

use Yii;
use app\models\BbmFakturItem;
use app\models\BbmFakturItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BbmFakturItemController implements the CRUD actions for BbmFakturItem model.
 */
class BbmFakturItemController extends Controller
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

    public function actionAjaxUpdateItem()
    {
        if (Yii::$app->request->isPost) {

            $dataItem = $_POST['dataItem'];

            $model = BbmFakturItem::findOne($dataItem['faktur_item_id']);
            $model->attributes = $dataItem;
            
            $result = [
                'code' => 'success',
                'message' => 'Data telah disimpan'
            ];
            if($model->validate())
            {
                $model->save();
            }

            else{

                $errors = '';
                foreach($model->getErrors() as $attribute){
                    foreach($attribute as $error){
                        $errors .= $error.' ';
                    }
                }
                        
                $result = [
                    'code' => 'danger',
                    'message' => $errors
                ];
                // print_r();exit;
            }

            echo json_encode($result);
        }
    }

    public function actionAjaxCreate()
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try 
        {
            if(!empty($_POST['fakturItem']))
            {
                $data = $_POST['fakturItem'];
                // print_r($data);exit;
                $model = new BbmFakturItem();
                $model->attributes = $data;
                $result = [];
                if ($model->save()) {
                    $result = [
                        'code' => 'success',
                        'message' => 'Data tersimpan',
                    ];                
                    $transaction->commit();
                }

                else{
                    $errors = '';
                    foreach($model->getErrors() as $key => $value)
                    {
                        $errors .= $value[0].' ';
                    }
                    $result = [
                        'code' => 'danger',
                        'message' => $errors,
                    ];
                }
                
                echo json_encode($result);

            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


    /**
     * Lists all BbmFakturItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BbmFakturItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BbmFakturItem model.
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
     * Creates a new BbmFakturItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($faktur_id = '')
    {
        $model = new BbmFakturItem();

        $model->faktur_id = !empty($faktur_id) ? $faktur_id : '';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Data tersimpan");
            return $this->redirect(['bbm-faktur/view', 'id' => $model->faktur_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BbmFakturItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Data tersimpan");
            return $this->redirect(['bbm-faktur/view', 'id' => $model->faktur_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BbmFakturItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
         $this->findModel($id)->delete();

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the BbmFakturItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BbmFakturItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BbmFakturItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
