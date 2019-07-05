<?php

namespace app\controllers;

use Yii;
use app\models\BbmJual;
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

        $listDispenser = \app\models\Departemen::getListDepartemens();

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
            
            
            // foreach($models->getModels() as $item)
            // {
            //     $results[] = 
            // }

            // exit;
            // $listJualTanggal = BbmJual::getListJualTanggal($_POST['bulan'], $_POST['tahun'],$_POST['barang_id']);
            
            $barang = SalesMasterBarang::find()->where(['id_barang'=>$_POST['barang_id']])->one();
              
            // for($i = 0;$i<31;$i++)
            // {
                
                
            //     $tgl = str_pad(($i+1), 2, '0', STR_PAD_LEFT);
            //     $fulldate = $_POST['tahun'].'-'.$_POST['bulan'].'-'.$tgl;
            // // foreach($listJualTanggal->models as $tgl)
            // // {
            //     $listShift = BbmJual::getListJualShifts($fulldate,$_POST['barang_id']);
            //     // $listShifts[$fulldate] = $listShift;
            //     foreach($listShift as $shift)
            //     {
            //          $subtotal_liter = 0;
            //         foreach($listDispenser->models as $disp)
            //         {
            //             $params = [
            //                 'tanggal' => $fulldate,
            //                 'barang_id' => $_POST['barang_id'],
            //                 'shift_id' => $shift->shift_id,
            //                 'dispenser_id' => $disp->id
            //             ];

            //             $dataProvider = $searchModel->searchBy($params);
            //             $listData[$fulldate][$shift->shift_id][$disp->id] = $dataProvider;
            //             $stok_awal = !empty($dataProvider) ? $dataProvider->stok_awal : 0;
            //             $stok_akhir = !empty($dataProvider) ? $dataProvider->stok_akhir : 0;
            //             $saldo = $stok_akhir - $stok_awal;
            //             $subtotal_liter += $saldo;
                        
            //             $harga = !empty($dataProvider) && $dataProvider->harga != 0 ? $dataProvider->harga : $harga;
            //         }

            //         // $kode_transaksi = $barang->id_barang.'-'.$tgl->id.'-'.$shift->shift_id;
            //         // $userLevel = Yii::$app->user->identity->access_role;    
                    
            //         // $userPt = Yii::$app->user->identity->perusahaan_id;
            //         // $kas = \app\models\Kas::find()->where(['kode_transaksi'=>$kode_transaksi])->one();
            //         // if(empty($kas))
            //         // {
            //         //     $kas = new \app\models\Kas;    
                        
            //         // }

            //         // $kas->kas_masuk = $subtotal_liter * $harga;
            //         // $kas->perkiraan_id = $barang->perkiraan_id;
            //         // $kas->perusahaan_id = $userPt;
            //         // $kas->penanggung_jawab = Yii::$app->user->identity->username;
            //         // $uk = 'besar';
            //         // $kas->keterangan = $barang->perkiraan->nama.' '.$barang->nama_barang;
            //         // $kas->tanggal = $tgl->tanggal;
            //         // $kas->jenis_kas = 1; // kas masuk    
            //         // $kas->perusahaan_id = $userPt;
            //         // $kas->kas_besar_kecil = $uk;
            //         // $kas->kode_transaksi = $kode_transaksi;

            //         // $kas->save();
                    
            //         // \app\models\Kas::updateSaldo($uk,$_POST['bulan'],$_POST['tahun']);
            //     }
            // }            
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
                
                $trx = new Transaksi();
                $trx->perusahaan_id = Yii::$app->user->identity->perusahaan_id;

                $akun_lawan = Perkiraan::find()->where([
                    'kode' => '1-11',
                    'perusahaan_id' => Yii::$app->user->identity->perusahaan_id
                ]);

                $akun_lawan = $akun_lawan->one();

                if(!empty($akun_lawan))
                {
                    $perkiraan_lawan_id = $akun_lawan->id;
                    $trx->perkiraan_id = $model->barang->perkiraan_jual_id;
                    $trx->no_bukti = 'INV'.$model->id;
                    $trx->keterangan = 'Jual '.$model->barang->nama_barang;
                    $trx->tanggal = $model->tanggal;    
                    $trx->jumlah = $model->qty * $model->barang->harga_jual;
                    $trx->perkiraan_lawan_id = $perkiraan_lawan_id;
                    if($trx->save())
                    {
                        $params = [
                            'perkiraan_id' => $trx->perkiraan_id,
                            'transaksi_id' => $trx->id,
                            'no_bukti' => $trx->no_bukti,
                            'jumlah' => $trx->jumlah,
                            'keterangan' => $trx->keterangan,
                            'keterangan_lawan' => 'Kas',
                            'tanggal' => $model->tanggal
                        ];


                        $kodeAsal = $trx->perkiraan->kode;
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
                            $jurnal->tanggal = date('Y-m-d',strtotime($params['tanggal']));
                            if(!$jurnal->save())
                            {
                                print_r($jurnal->getErrors());exit;
                            }

                            if(!empty($perkiraan_lawan_id))
                            {
                                $kodeLawan = $trx->perkiraanLawan->kode;
                                
                                $jurnal = new Jurnal;
                                $jurnal->perkiraan_id = $trx->perkiraan_lawan_id;
                                
                                $jurnal->transaksi_id = $params['transaksi_id'];
                                $jurnal->no_bukti = $params['no_bukti'];
                                $jurnal->kredit = $params['jumlah'];
                                $jurnal->keterangan = $params['keterangan_lawan'];
                                $jurnal->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                                $jurnal->tanggal = date('Y-m-d',strtotime($params['tanggal']));
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
                            $jurnal->tanggal = date('Y-m-d',strtotime($params['tanggal']));
                            if(!$jurnal->save())
                            {
                                echo 'Journal';
                                print_r($jurnal->getErrors());exit;
                            }

                            if(!empty($perkiraan_lawan_id))
                            {
                                $kodeLawan = $trx->perkiraanLawan->kode;
                                
                                $jurnal = new Jurnal;
                                $jurnal->perkiraan_id = $trx->perkiraan_lawan_id;
                                $jurnal->debet = $params['jumlah'];
                                $jurnal->transaksi_id = $params['transaksi_id'];
                                $jurnal->no_bukti = $params['no_bukti'];
                               
                                $jurnal->keterangan = $params['keterangan_lawan'];
                                $jurnal->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                                $jurnal->tanggal = date('Y-m-d',strtotime($params['tanggal']));
                                if(!$jurnal->save())
                                {
                                    echo 'Journal lawan';
                                    print_r($jurnal->getErrors());exit;
                                }

                            }
                        }
                    }

                    else{
                        echo 'Trx Error';
                        print_r($trx->getErrors());exit;
                    }
                }

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
