<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bbm_faktur".
 *
 * @property int $id
 * @property int $suplier_id
 * @property string $no_lo
 * @property string $tanggal_lo
 * @property string $no_so
 * @property string $tanggal_so
 * @property int $perusahaan_id
 * @property string $created
 *
 * @property Perusahaan $perusahaan
 * @property SalesSuplier $suplier
 * @property BbmFakturItem[] $bbmFakturItems
 */
class BbmFaktur extends \yii\db\ActiveRecord
{

    const SCENARIO_DO = 'update_do';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%bbm_faktur}}';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DO] = ['no_do', 'tanggal_do'];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['no_do','tanggal_do'],'required','on' => self::SCENARIO_DO],
            [['suplier_id', 'perusahaan_id','no_so','tanggal_so'], 'required'],
            [['suplier_id', 'perusahaan_id'], 'integer'],
            [['tanggal_so', 'created_at','updated_at','is_selesai','is_dropping'], 'safe'],
            [['no_so'], 'string', 'max' => 100],
            [['perusahaan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Perusahaan::className(), 'targetAttribute' => ['perusahaan_id' => 'id_perusahaan']],
            [['suplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalesSuplier::className(), 'targetAttribute' => ['suplier_id' => 'id_suplier']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'suplier_id' => 'Suplier',
            'no_so' => 'Nomor SO',
            'tanggal_so' => 'Tanggal SO',
            'perusahaan_id' => 'Perusahaan',
            'is_selesai' => 'Selesai',
            'no_do' => 'No LO',
            'tanggal_do' => 'Tanggal LO',
            'created_at' => 'Created',
            'updated_at' => 'Updated'
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->tanggal_so = date('Y-m-d', strtotime($this->tanggal_so));
       
        return true;
    }

    // public function behaviors()
    // {
    //     return [
    //         [
    //             'class' => \yii\behaviors\AttributeBehavior::className(),
    //             'attributes' => [
    //                 // update 1 attribute 'created' OR multiple attribute ['created','updated']
    //                 \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['tanggal_so','tanggal_lo'],
    //                 \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['tanggal_so','tanggal_lo'],
    //             ],
               
    //             'value' => function ($event) {
    //                 print_r($event->name);exit;
    //                 $this->tanggal_so = date('Y-m-d', strtotime($this->tanggal_so));
    //                 $this->tanggal_lo = date('Y-m-d', strtotime($this->tanggal_lo));
    //                 return true;
    //             },
    //         ],
    //     ];
    // }

    public function getVolume()
    {
        return $this->hasMany(BbmFakturItem::className(), ['faktur_id' => 'id'])->sum('jumlah');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerusahaan()
    {
        return $this->hasOne(Perusahaan::className(), ['id_perusahaan' => 'perusahaan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuplier()
    {
        return $this->hasOne(SalesSuplier::className(), ['id_suplier' => 'suplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBbmFakturItems()
    {
        return $this->hasMany(BbmFakturItem::className(), ['faktur_id' => 'id']);
    }

    public function getNamaSuplier()
    {
        return $this->suplier->nama;
    }

    // public function getHargaTotal()
    // {
    //     $total = 0;
    //     foreach($this->bbmFakturItems as $item)
    //     {
    //         $total += $item->harga;
    //     }

    //     return $total;
    // }

    public function getHargaTotal()
    {
        // Customer has_many Order via Order.customer_id -> id
        return $this->hasMany(BbmFakturItem::className(), ['faktur_id' => 'id'])->sum('harga');
    }    

}
