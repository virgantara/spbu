<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "erp_bbm_dropping".
 *
 * @property int $id
 * @property int $bbm_faktur_id
 * @property string $no_lo
 * @property string $tanggal
 * @property string $jam
 * @property int $barang_id
 * @property double $jumlah
 * @property string $created_at
 * @property string $updated_at
 *
 * @property SalesFaktur $bbmFaktur
 * @property SalesMasterBarang $barang
 */
class BbmDropping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'erp_bbm_dropping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bbm_faktur_id', 'no_lo', 'jam', 'barang_id', 'jumlah','shift_id','departemen_id'], 'required'],
            [['bbm_faktur_id', 'barang_id'], 'integer'],
            [['tanggal', 'jam', 'created_at', 'updated_at'], 'safe'],
            [['jumlah'], 'number'],
            [['no_lo'], 'string', 'max' => 255],
            [['bbm_faktur_id'], 'exist', 'skipOnError' => true, 'targetClass' => BbmFaktur::className(), 'targetAttribute' => ['bbm_faktur_id' => 'id']],
            [['barang_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalesMasterBarang::className(), 'targetAttribute' => ['barang_id' => 'id_barang']],
            [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shift::className(), 'targetAttribute' => ['shift_id' => 'id']],
            [['departemen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departemen::className(), 'targetAttribute' => ['departemen_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'departemen_id' => 'Tangki',
            'bbm_faktur_id' => 'Bbm Faktur ID',
            'no_lo' => 'No Lo',
            'tanggal' => 'Tanggal',
            'jam' => 'Jam',
            'barang_id' => 'Barang ID',
            'jumlah' => 'Jumlah',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->tanggal = date('Y-m-d', strtotime($this->tanggal));
       
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBbmFaktur()
    {
        return $this->hasOne(BbmFaktur::className(), ['id' => 'bbm_faktur_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBarang()
    {
        return $this->hasOne(SalesMasterBarang::className(), ['id_barang' => 'barang_id']);
    }

    public function getNamaBarang()
    {
        return $this->barang->nama_barang;
    }

    public function getNamaShift()
    {
        return $this->shift->nama;
    }

    public function getNamaTangki()
    {
        return $this->tangki->nama;
    }

    public function getShift()
    {
        return $this->hasOne(Shift::className(), ['id' => 'shift_id']);
    }

    public function getTangki()
    {
        return $this->hasOne(Departemen::className(), ['id' => 'departemen_id']);
    }
}
