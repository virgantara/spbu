<?php

namespace app\controllers;

use Yii;
use app\models\BarangOpname;
use app\models\BarangOpnameSearch;
use app\models\Transaksi;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\DepartemenStok;

/**
 * BarangOpnameController implements the CRUD actions for BarangOpname model.
 */
class BarangOpnameController extends Controller
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
     * Lists all BarangOpname models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BarangOpnameSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BarangOpname model.
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

   

    public function actionList(){

        $list = [];
        if(!empty($_POST['tanggal']) && !empty($_POST['btn-cari']))
        {

            $tanggal = date('d',strtotime($_POST['tanggal']));
            $bulan = date('m',strtotime($_POST['tanggal']));
            $tahun = date('Y',strtotime($_POST['tanggal']));

            $query = DepartemenStok::find();
            $query->where(['<>','barang.nama_barang','-']);
            $query->andWhere(['departemen_id'=>$_POST['dept_id']]);
            // $query->andWhere(['departemen_id'=>Yii::$app->user->identity->departemen]);
            // $query->andWhere(['tahun'=>$tahun.$bulan]);
            $query->andWhere(['barang.is_hapus'=>0]);
            $query->joinWith(['barang as barang']);
            $query->orderBy(['barang.nama_barang'=>SORT_ASC]);
            $list = $query->all();

        }

        return $this->render('create', [
            'list' => $list,
            'model' => $model,

        ]);
    }

  
    /**
     * Creates a new BarangOpname model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        
        $list = [];

        if(!empty($_POST['cari']))
        {
            $tanggal = date('d',strtotime($_POST['tanggal']));
            $bulan = date('m',strtotime($_POST['tanggal']));
            $tahun = date('Y',strtotime($_POST['tanggal']));

            $query = DepartemenStok::find();
            $query->where(['<>','barang.nama_barang','-']);
            $query->andWhere(['departemen_id'=>$_POST['dept_id']]);
            // $query->andWhere(['departemen_id'=>Yii::$app->user->identity->departemen]);
            // $query->andWhere(['tahun'=>$tahun.$bulan]);
            $query->andWhere(['barang.jenis_barang_id'=>$_POST['jenis_barang_id']]);
            $query->andWhere(['barang.is_hapus'=>0]);
            $query->joinWith(['barang as barang']);
            $query->orderBy(['barang.nama_barang'=>SORT_ASC]);
            $list = $query->all();
        }

        if(!empty($_POST['simpan']))
        {

            $tanggal = date('d',strtotime($_POST['tanggal_pilih']));
            $bulan = date('m',strtotime($_POST['tanggal_pilih']));
            $tahun = date('Y',strtotime($_POST['tanggal_pilih']));

            $query = DepartemenStok::find();
            $query->where(['<>','barang.nama_barang','-']);
            $query->andWhere(['departemen_id'=>$_POST['dept_id_pilih']]);
            $query->andWhere(['barang.jenis_barang_id'=>$_POST['jenis_barang_id']]);
            $query->andWhere(['barang.is_hapus'=>0]);
            $query->joinWith(['barang as barang']);
            $query->orderBy(['barang.nama_barang'=>SORT_ASC]);
            $list = $query->all();

            // $date = date('Y-m-d',strtotime($_POST['tanggal_pilih']));
            // $prev_month_ts = strtotime($tahun.'-'.$bulan.'-15 -1 month');
            // $prev_month_ts = date('Y-m-d', $prev_month_ts); 
            // $bulan_lalu = date('m',strtotime($prev_month_ts));
            // $tahun_lalu = date('Y',strtotime($prev_month_ts));

            $transaction = \Yii::$app->db->beginTransaction();
            
            try 
            {

                foreach($list as $m)
                {
                    $stok_riil = $_POST['stok_riil_'.$m->id] ?: 0;
                    $batch_no = $_POST['batch_no_'.$m->id] ?: '';
                    $exp_date = $_POST['exp_date_'.$m->id] ?: date('Y-m-d');
                    $stok = $m->stok;
                    $model = BarangOpname::find()->where([
                        'departemen_stok_id' => $m->id,
                        'bulan' => $bulan,
                        'tahun' => $tahun.$bulan
                    ])->one();

                    $m->stok_bulan_lalu = $stok;
                    $m->stok = $stok_riil;
                    $m->batch_no = $batch_no;
                    $m->exp_date = $exp_date;
                    if(!$m->save())
                    {
                        print_r($m->getErrors());exit;
                    }
                    

                    if(empty($model))
                        $model = new BarangOpname;

                    $model->barang_id = $m->barang_id;
                    $model->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                    $model->departemen_stok_id = $m->id;
                    $model->selisih = $stok - $stok_riil;
                    $model->stok = $stok_riil;
                    $model->stok_riil = $stok_riil;
                    $model->stok_lalu = $stok;
                    $model->harga_beli = $m->barang->harga_beli;
                    $model->harga_jual = $m->barang->harga_jual;
                    $model->bulan = $bulan;
                    $model->tahun = $tahun.$bulan;
                    $model->tanggal = date('Y-m-d',strtotime($_POST['tanggal_pilih']));
                    

                    if($model->validate())
                    {

                        $model->save();
                        $pars = [
                            'kode_akun_lawan' => '1-1102',
                            'perkiraan_id' => $m->barang->akun_persediaan_id,
                            'no_bukti' => 'OPNAME_'.$model->id,
                            'keterangan' => 'Stok Opname '.$m->barang->nama_barang,
                            'tanggal' => $model->tanggal,
                            'jumlah' => $model->stok * $model->barang->harga_beli
                        ];

                        Transaksi::insertTransaksi($pars);
                        $params = [
                            'barang_id' => $m->barang_id,
                            'status' => 1,
                            'kode_transaksi' => 'OPNAME_'.$model->id,
                            'qty' => $stok_riil,
                            'tanggal' => date('Y-m-01'),
                            'departemen_id' => $_POST['dept_id_pilih'],
                            'stok_id' => $m->id,
                            'keterangan' => 'Stok Opname Tahun '.date('Y').' bulan '.date('m'),
                        ];
                        $ks = \app\models\KartuStok::find()->where(['kode_transaksi'=>'OPNAME_'.$model->id])->one();
                            

                        if(empty($ks))
                        {
                            $m = new \app\models\KartuStok;
                            $m->barang_id = $params['barang_id'];
                            $m->qty_in = ceil($params['qty']);
                            $prevStok = \app\models\KartuStok::getPrevStok($params);
                            if(!empty($prevStok))
                            {

                                if(count($prevStok) > 1)
                                {
                                    $m->sisa_lalu = $prevStok[1]->sisa;
                                    $m->sisa = $m->qty_in;
                                    $m->prev_id = $prevStok[1]->id;
                                }

                                else if (count($prevStok) == 1)
                                {
                                    $m->sisa_lalu = $prevStok[0]->sisa;
                                    $m->sisa = $m->qty_in;
                                    $m->prev_id = $prevStok[0]->id;
                                }

                            }

                            else
                            {
                                $m->sisa_lalu = 0;
                                $m->sisa = $m->qty_in;
                            }
                           
                            $m->kode_transaksi = !empty($params['kode_transaksi']) ? $params['kode_transaksi'] : '-';
                            $m->tanggal = $params['tanggal'];
                            $m->departemen_id = $params['departemen_id'];
                            $m->stok_id = $params['stok_id'];
                            $m->keterangan = $params['keterangan'];
                            if($m->validate())
                                $m->save();
                            else{
                                $errors = '';
                                foreach($m->getErrors() as $attribute){
                                    foreach($attribute as $error){
                                        $errors .= $error.' ';
                                    }
                                }
                                    
                                print_r($errors);exit;             
                            }
                        }
                        else{
                            $m = \app\models\KartuStok::find()->where([
                                'barang_id' => $params['barang_id'],
                                'departemen_id' => $params['departemen_id'],
                                'kode_transaksi' => $params['kode_transaksi'] 
                            ])->one();
                            if(!empty($m))
                            {
                                $m->barang_id = $params['barang_id'];
                                $m->qty_in = ceil($params['qty']);
                                $prevStok = \app\models\KartuStok::getPrevStok($params);
                                if(!empty($prevStok))
                                {

                                    if(count($prevStok) > 1)
                                    {
                                        $m->sisa_lalu = $prevStok[1]->sisa;
                                        $m->sisa = $m->qty_in;
                                        $m->prev_id = $prevStok[1]->id;
                                    }

                                    else if (count($prevStok) == 1)
                                    {
                                        $m->sisa_lalu = $prevStok[0]->sisa;
                                        $m->sisa = $m->qty_in;
                                        $m->prev_id = $prevStok[0]->id;
                                    }

                                }

                                else
                                {
                                    $m->sisa_lalu = 0;
                                    $m->sisa = $m->qty_in;
                                }
                               
                                if($m->validate())
                                    $m->save();
                                else{
                                    $errors = '';
                                    foreach($m->getErrors() as $attribute){
                                        foreach($attribute as $error){
                                            $errors .= $error.' ';
                                        }
                                    }
                                        
                                    print_r($errors);exit;             
                                }    
                            }
                        }


                    }
                    else{
                        
                        $errors = \app\helpers\MyHelper::logError($m);
                        print_r($errors);exit;
                        return $this->render('create', [
                            'list' => $list,
                            'model' => $model,
                        ]);
                    }
                    
                }
                Yii::$app->session->setFlash('success', "Data tersimpan");
                $transaction->commit();
                return $this->redirect(['create']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                
                throw $e;
            }
        }
        // print_r(count($_POST['stok_riil']));exit;

        return $this->render('create', [
            'list' => $list,
            'model' => $model,

        ]);
    }

    /**
     * Updates an existing BarangOpname model.
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
     * Deletes an existing BarangOpname model.
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
     * Finds the BarangOpname model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BarangOpname the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BarangOpname::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
