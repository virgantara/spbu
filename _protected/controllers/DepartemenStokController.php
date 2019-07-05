<?php

namespace app\controllers;

use Yii;
use app\models\DepartemenStok;
use app\models\DepartemenStokSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

/**
 * PerusahaanSubStokController implements the CRUD actions for PerusahaanSubStok model.
 */
class DepartemenStokController extends Controller
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

    public function actionAjaxStokUnit()
    {
        if (Yii::$app->request->isPost) {

            $dataItem = $_POST['dataItem'];

            $query = DepartemenStok::find()->where([
                'departemen_id' => $dataItem['dispenser_id'],
                'barang_id' => $dataItem['barang_id'],
            ]);

            $hasil = $query->one();

            $jumlah = !empty($hasil) ? $hasil->stok : 0;

            $result = [
                'code' => 200,
                'message' => 'success',
                'jumlah' => $jumlah
            ];
           
            echo json_encode($result);
        }
    }

    public function actionAjaxDepartemenBarang() {

        if (Yii::$app->request->isAjax) {
            $query = new \yii\db\Query;
            $barang_id = $_POST['barang_id'];
            $query->select('ds.id, ds.stok, od.kekuatan')
                ->from('erp_departemen_stok ds')
                ->join('LEFT JOIN','erp_obat_detil od','ds.barang_id=od.barang_id')
                ->where(['ds.departemen_id'=>Yii::$app->user->identity->departemen,'ds.barang_id' => $barang_id])
                ->orderBy(['exp_date'=>SORT_ASC])
                ->limit(1);
            $command = $query->createCommand();
            $data = $command->queryOne();
            $out = [
                'dept_stok_id' => $data['id'],
                'stok' => $data['stok'],
                'kekuatan' => $data['kekuatan']
            ];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; 
            \Yii::$app->response->data = $out;
            \Yii::$app->end();
        }
    }

    public function actionAjaxBarang($term = null) {

        if (Yii::$app->request->isAjax) {
            $query = new \yii\db\Query;
        
            $query->select('b.kode_barang , b.id_barang, b.nama_barang, b.id_satuan as satuan, SUM(ds.stok) as stok, b.harga_jual, b.harga_beli')
                ->from('erp_departemen_stok ds')
                ->join('JOIN','erp_sales_master_barang b','b.id_barang=ds.barang_id')
                // ->join('JOIN','erp_obat_detil od','b.id_barang=od.barang_id')
                ->join('JOIN','erp_departemen_user du','du.departemen_id=ds.departemen_id')
                ->where(['du.user_id'=>Yii::$app->user->identity->id,'ds.departemen_id'=>Yii::$app->user->identity->departemen])
                ->andWhere('(nama_barang LIKE "%' . $term .'%" OR kode_barang LIKE "%' . $term .'%")')
                ->orderBy('nama_barang')
                ->groupBy(['id_barang'])
                ->limit(10);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out = [];
            foreach ($data as $d) {
                if($d['nama_barang'] == '-') continue;
                $out[] = [
                    'id' => $d['id_barang'],
                    'kode' => $d['kode_barang'],
                    'nama' => $d['nama_barang'],
                    // 'dept_stok_id' => $d['id'],
                    'satuan' => $d['satuan'],
                    'stok' => $d['stok'],
                    // 'kekuatan' => $d['kekuatan'],
                    'harga_jual' => $d['harga_jual'],
                    'harga_beli' => $d['harga_beli'],
                    'label'=> $d['nama_barang'].' - '.$d['kode_barang']
                ];
            }

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; 
            \Yii::$app->response->data = $out;
            \Yii::$app->end();
        }
    }

    public function actionAjaxStokBarang($q = null) {

        $query = new \yii\db\Query;
    
        $query->select('b.kode_barang , b.id_barang, b.nama_barang, b.id_satuan as satuan, ds.id, ds.stok')
            ->from('erp_departemen_stok ds')
            ->join('JOIN','erp_sales_master_barang b','b.id_barang=ds.barang_id')
            ->join('JOIN','erp_departemen_user du','du.departemen_id=ds.departemen_id')
            ->where(['du.user_id'=>Yii::$app->user->identity->id])
            ->andWhere('(nama_barang LIKE "%' . $q .'%" OR kode_barang LIKE "%' . $q .'%")')
            ->orderBy('nama_barang')
            // ->groupBy(['kode'])
            ->limit(20);
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out = [];
        foreach ($data as $d) {
            $out[] = [
                'id' => $d['id_barang'],
                'kode' => $d['kode_barang'],
                'nama' => $d['nama_barang'],
                'dept_stok_id' => $d['id'],
                'satuan' => $d['satuan'],
                'stok' => $d['stok'],
                'label'=> $d['nama_barang']
            ];
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; 
        \Yii::$app->response->data = $out;
        \Yii::$app->end();

      
    }

    public function actionGetDepartemenStok()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $out = self::getDepartemenListStok($cat_id); 
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                echo \yii\helpers\Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo \yii\helpers\Json::encode(['output'=>'', 'selected'=>'']);
    }

    private function getDepartemenListStok($id)
    {
        $query = DepartemenStok::find()->where(['departemen_id'=>$id])->all();
        // $query->joinWith(['departemen as d']);
        $result = [];
        foreach($query as $item)
        {
            $result[] = [
                'id' => $item->barang_id,
                'name' => $item->barang->nama_barang
            ];
        }

        return $result;
    }

    /**
     * Lists all PerusahaanSubStok models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartemenStokSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PerusahaanSubStok model.
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
     * Creates a new PerusahaanSubStok model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DepartemenStok();
        $model->tanggal = date('Y-m-d');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Data telah ditambahkan");
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PerusahaanSubStok model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Data telah diupdate");
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PerusahaanSubStok model.
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

    public function actionAjaxDelete($id)
    {
        $this->findModel($id)->delete();

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the PerusahaanSubStok model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PerusahaanSubStok the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DepartemenStok::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
