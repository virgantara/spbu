<?php

namespace app\controllers;

use Yii;
use app\models\SalesStokGudang;
use app\models\SalesStokGudangSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


use app\models\SalesMasterBarang;
use app\models\SalesMasterBarangSearch;
use app\models\KartuStokSearch;
use app\models\KartuStok;

use yii\db\Query;
use yii\helpers\Json;


/**
 * SalesStokGudangController implements the CRUD actions for SalesStokGudang model.
 */
class SalesStokGudangController extends Controller
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

    public function actionAjaxGetBarangByJenis()
    {
        

        $q = $_GET['term'];
        $jenis = $_GET['jenis'];
        
        
        $list = SalesStokGudang::find();
        $list->joinWith(['barang as barang']);
        $list->where([
            'barang.jenis_barang_id'=>$jenis,
            'barang.id_perusahaan' => Yii::$app->user->identity->perusahaan_id
        ]);

        $list->andFilterWhere(['like', 'barang.nama_barang', $q]);

        $list->limit(10);
        $list = $list->all();

        $result = [];
        foreach($list as $item)
        {   
            $label = $item->barang->nama_barang.' | '.$item->barang->kode_barang;
            $result[] = [
                'id' => $item->id_barang,
                'label' => $label,
                'ed' => $item->exp_date,
                'batch_no' => $item->batch_no
            ];
        }

        echo \yii\helpers\Json::encode($result);

    }

    public function actionInitStok(){
        $model = new SalesStokGudang();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('initStok', [
            'model' => $model,
        ]);
    }

    public function actionAjaxGetItemByID(){
        if (Yii::$app->request->isPost) {

            $dataItem = $_POST['dataItem'];

            $model = SalesStokGudang::find($dataItem);

            
            if(!empty($model)){
                
                $result = [
                    'code' => 200,
                    'message' => 'success',
                    'id' =>$model->id_stok,
                    'barang_id' => $model->id_barang,
                    'jumlah' => $model->jumlah,
                    'exp_date' => $model->exp_date,
                    'batch_no' => $model->batch_no,
                    'gudang_id'=> $model->id_gudang,
                    'nama_barang' => $model->barang->nama_barang,
                    'kode_barang' => $model->barang->kode_barang,

                ]; 
                
            }

            else{

                $result = [
                    'code' => 510,
                    'message' => 'data tidak ditemukan'
                ];

            }
            echo json_encode($result);

        }
    }

    

    public function actionKartu()
    {
        $barang_id = !empty($_GET['barang_id']) ? $_GET['barang_id'] : 0;
        
        $searchModel = new KartuStokSearch();
        $params = Yii::$app->request->queryParams;
        $searchModel->barang_id = $barang_id;
        $params['KartuStok']['tanggal_awal'] = date('Y-m-d',strtotime($_GET['KartuStok']['tanggal_awal']));
        $params['KartuStok']['tanggal_akhir'] = date('Y-m-d',strtotime($_GET['KartuStok']['tanggal_akhir']));
        $dataProvider = $searchModel->search($params);
        
        $results = [];

        $barang = SalesMasterBarang::findOne($barang_id);


        foreach($dataProvider->getModels() as $item)
        {
            // $listStok = $searchModel->searchByTanggal($item->barang_id);
            // $jml_masuk = 0;
            // $jml_keluar = 0;

            // foreach($listStok->getModels() as $stok)
            // {

            //     $jml_masuk += $stok->qty_in;
            //     $jml_keluar += $stok->qty_out;
            // }

            $stokGudang = SalesStokGudang::findOne($item->stok_id);


            $results[] = [
                'masuk' => $item->qty_in,
                'keluar' => $item->qty_out,
                'item' => $item,
                'batch_no' => !empty($stokGudang) ? $stokGudang->batch_no : '',
                'exp_date' => !empty($stokGudang) ? $stokGudang->exp_date : '',
                'keterangan' => $item->keterangan
            ];
        }
        
        $model = new KartuStok;
        return $this->render('kartu', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'results' => $results,
            'barang' => $barang,
            'tanggal_awal' => Yii::$app->request->queryParams['KartuStok']['tanggal_awal'],
            'tanggal_akhir' => Yii::$app->request->queryParams['KartuStok']['tanggal_akhir'],
        ]);
    }

    public function actionAjaxUpdateStok()
    {
        if (Yii::$app->request->isPost) {

            $dataItem = $_POST['dataItem'];
            $isNew = $dataItem['isNew'];

            if($isNew){
                $model = new SalesStokGudang;
                $model->id_gudang = $dataItem['gudang_id'];
                $model->id_barang = $dataItem['barang_id'];
                $model->batch_no = $dataItem['batch_no'];
            }
            else{
                $model = SalesStokGudang::findOne($dataItem['stok_id']);
                $model->batch_no = $dataItem['batch_no'];
                
            }
            
            $model->exp_date = $dataItem['exp_date'];
            $model->exp_date = date('Y-m-d',strtotime($model->exp_date));
            $model->jumlah = $dataItem['jml_stok'];
            
            $result = [
                'code' => 200,
                'message' => 'success'
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
                    'code' => 510,
                    'message' => $errors
                ];
                // print_r();exit;
            }

            echo json_encode($result);
        }
    }

    public function actionStatus()
    {
        $searchModel = new SalesStokGudangSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('status', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAjaxBarang($q = null) {
        
        $query = new Query;
        
        if(Yii::$app->user->can('gudang') ){
            $query->select('b.kode_barang , b.id_barang, b.nama_barang, g.id_stok, b.id_satuan as satuan, b.harga_jual')
                ->from('erp_sales_master_barang b')
                ->join('JOIN','erp_sales_stok_gudang g','g.id_barang=b.id_barang')
                ->where('(nama_barang LIKE "%' . $q .'%" OR kode_barang LIKE "%' . $q .'%") AND b.is_hapus = 0 AND g.is_hapus = 0')
                ->orderBy('nama_barang')
                // ->groupBy(['kode'])
                ->limit(20);
        }

        else if(Yii::$app->user->can('operatorCabang') || Yii::$app->user->can('distributor'))
        {
            $query->select('b.kode_barang , b.id_barang, b.nama_barang, b.id_satuan as satuan, b.harga_jual')
                ->from('erp_sales_master_barang b')
                // ->join('JOIN','erp_departemen_stok ds','ds.barang_id=b.id_barang')
                // ->join('JOIN','erp_sales_stok_gudang g','g.id_barang=b.id_barang')
                ->where('(nama_barang LIKE "%' . $q .'%" OR kode_barang = "' . $q .'") AND b.is_hapus = 0')
                ->orderBy('nama_barang')
                // ->groupBy(['kode'])
                ->limit(20);
        }
        
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out = [];
        foreach ($data as $d) {
            $out[] = [
                'id' => $d['id_barang'],
                'kode' => $d['kode_barang'],
                'nama' => $d['nama_barang'],
                'id_stok' => 0,
                'satuan' => $d['satuan'],
                'harga_jual' => $d['harga_jual']
            ];
        }
        echo Json::encode($out);
    }


    public function actionGetGudangByBarang()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $out = self::getGudangByBarangList($cat_id); 
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    private function getGudangByBarangList($idbarang)
    {
        $list = SalesStokGudang::find()->where(['id_barang'=>$idbarang])->all();

        $result = [];
        foreach($list as $item)
        {
            $result[] = [
                'id' => $item->id_gudang,
                'name' => $item->gudang->nama
            ];
        }

        return $result;
    }

    public function actionGetBarangStok()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $out = self::getBarangListStok($cat_id); 
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    private function getBarangListStok($id_stok)
    {
        $list = SalesStokGudang::find()->where(['id_stok'=>$id_stok])->all();

        $result = [];
        foreach($list as $item)
        {
            $result[] = [
                'id' => $item->id_barang,
                'name' => $item->barang->nama_barang
            ];
        }

        return $result;
    }

    public function actionGetBarang()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $cat_id = $parents[0];
                $out = self::getBarangList($cat_id); 
                // the getSubCatList function will query the database based on the
                // cat_id and return an array like below:
                // [
                //    ['id'=>'<sub-cat-id-1>', 'name'=>'<sub-cat-name1>'],
                //    ['id'=>'<sub-cat_id_2>', 'name'=>'<sub-cat-name2>']
                // ]
                echo Json::encode(['output'=>$out, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

    private function getBarangList($id_gudang)
    {
        $list = SalesStokGudang::find()->where(['id_gudang'=>$id_gudang])->all();

        $result = [];
        foreach($list as $item)
        {
            $result[] = [
                'id' => $item->id_stok,
                'name' => $item->barang->nama_barang
            ];
        }

        return $result;
    }

    /**
     * Lists all SalesStokGudang models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SalesStokGudangSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalesStokGudang model.
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
     * Creates a new SalesStokGudang model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($barang_id = '')
    {
        $model = new SalesStokGudang();

        $model->id_barang = !empty($barang_id) ? $barang_id : '';

        if ($model->load(Yii::$app->request->post())) {
            $temp = SalesStokGudang::find()->where([
                'id_gudang'=> $model->id_gudang,
                'id_barang'=> $model->id_barang,
                'is_hapus' => 0
            ])->one();

            if(!empty($temp)){
                $temp->load(Yii::$app->request->post());
                $temp->save();
            }

            else{
                $model->save();
            }

            Yii::$app->session->setFlash('success', "Data tersimpan");
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SalesStokGudang model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->save(false,['jumlah']);
            Yii::$app->session->setFlash('success', "Data terupdate");
            return $this->redirect(['sales-gudang/view','id'=>$model->id_gudang]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SalesStokGudang model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_hapus = 1;
        $model->exp_date = date('Y',strtotime('+2 years')).'-'.date('m-d');
        
        $model->save();

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
    }
    
    /**
     * Finds the SalesStokGudang model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalesStokGudang the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalesStokGudang::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
