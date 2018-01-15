<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "taller".
 *
 * @property int $id
 * @property string $nombre
 * @property string $url_bucket
 */
class Taller extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'taller';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['nombre'], 'string', 'max' => 20],
            [['url_bucket'], 'string', 'max' => 200],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'url_bucket' => 'Url Bucket',
        ];
    }
}
