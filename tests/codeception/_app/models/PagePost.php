<?php

use artkost\yii2\attachment\behaviors\AttachBehavior;
use artkost\yii2\attachment\models\AttachmentFile;
use yii\db\ActiveRecord;

class PagePost extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%page_post}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'attachBehavior' => [
                'class' => AttachBehavior::className(),
                'models' => [
                    'avatar' => [
                        'class' => AttachmentFile::className()
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Required
            [['title'], 'required'],
        ];
    }
}
