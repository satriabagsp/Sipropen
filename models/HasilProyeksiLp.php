<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hasil_proyeksi_lp".
 *
 * @property int $no_proyeksi
 * @property string $id_kabkota
 * @property int $tahun_proyeksi
 * @property string $jenis_kelamin
 * @property int $kup_5
 * @property int $kup_10
 * @property int $kup_15
 * @property int $kup_20
 * @property int $kup_25
 * @property int $kup_30
 * @property int $kup_35
 * @property int $kup_40
 * @property int $kup_45
 * @property int $kup_50
 * @property int $kup_55
 * @property int $kup_60
 * @property int $kup_65
 * @property int $kup_70
 * @property int $kup_75
 * @property int $kup_80
 */
class HasilProyeksiLp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasil_proyeksi_lp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_wilayah', 'tahun_proyeksi', 'jenis_kelamin', 'kup_5', 'kup_10', 'kup_15', 'kup_20', 'kup_25', 'kup_30', 'kup_35', 'kup_40', 'kup_45', 'kup_50', 'kup_55', 'kup_60', 'kup_65', 'kup_70', 'kup_75', 'kup_80'], 'required'],
            [['tahun_proyeksi', 'kup_5', 'kup_10', 'kup_15', 'kup_20', 'kup_25', 'kup_30', 'kup_35', 'kup_40', 'kup_45', 'kup_50', 'kup_55', 'kup_60', 'kup_65', 'kup_70', 'kup_75', 'kup_80'], 'integer'],
            [['id_wilayah'], 'string', 'max' => 4],
            [['jenis_kelamin'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'no_proyeksi' => 'No Proyeksi',
            'id_wilayah' => 'Id Kabkota',
            'tahun_proyeksi' => 'Tahun Proyeksi',
            'jenis_kelamin' => 'Jenis Kelamin',
            'kup_5' => '0-4',
            'kup_10' => '5-9',
            'kup_15' => '10-14',
            'kup_20' => '15-19',
            'kup_25' => '20-24',
            'kup_30' => '25-29',
            'kup_35' => '30-34',
            'kup_40' => '35-39',
            'kup_45' => '40-44',
            'kup_50' => '45-49',
            'kup_55' => '50-54',
            'kup_60' => '55-59',
            'kup_65' => '60-64',
            'kup_70' => '65-69',
            'kup_75' => '70-74',
            'kup_80' => '75+',
        ];
    }
}
