# Yii2 Attachment files module
This module handle all files in application

All uploaded files have status `TEMPORARY` by default.
When model with attaches save yourself, all the files attached to the model change their status to permanent.

# Cli Commands

`php yii attachment/manager/clear` clears all temporary files from system
`php yii attachment/manager/refresh-styles` resaves all image files with styles (files with mime 'image/jpeg', 'image/png')

# How to use

Create your own type of file
```php 

namespace app\modules\user\models;

use app\modules\attachment\models\ImageFile;

class UserAvatarFile extends ImageFile
{

    public $path = 'user/profile';

    public static function type()
    {
        return 'user_profile';
    }
}

```

Create model that have `attachment_id` field, and attach 'AttachBehavior` to it (ATTENTION: model can have only one instance of behavior)

```php
/**
 * Class Profile
 * @package app\modules\user\models
 * User profile model.
 *
 * @property integer $user_id User ID
 * @property string $name Name
 * @property string $surname Surname
 * @property int $avatar_id Avatar
 * @property boolean $sex
 *
 * @property User $user User
 * @property UserAvatarFile $avatar avatar file
 */
class UserProfile extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    public function behaviors()
    {
        return [
            AttachBehavior::NAME => [
                'class' => AttachBehavior::className(),
                'attributes' => [
                    'avatar' => [
                        'class' => UserAvatarFile::className(),
                        'attribute' => 'avatar_id'
                    ]
                ]
            ]
        ];
    }

    public function getAvatar()
    {
        return $this->hasOne(UserAvatarFile::className(), ['id' => 'avatar_id'])
            ->andWhere(['type' => UserAvatarFile::type(), 'status_id' => UserAvatarFile::STATUS_PERMANENT]);
    }
}
```

Currently supported only `FileAPI` upload, but you can add yours.

Add action into controller
```php
namespace app\modules\user\controllers;

use app\modules\attachment\fileapi\actions\UploadAction as FileAPIUpload;
use app\modules\user\models\UserProfile;

/**
 * Profile controller for authenticated users.
 */
class ProfileController extends Controller
{

    public function actions()
    {
        return [
            'fileapi-upload' => [
                'class' => FileAPIUpload::className(),
                'modelClass' => UserProfile::className(),
                'attribute' => 'avatar',
                'unique' => true
            ]
        ];
    }

}
```

in action view file you can use widget for upload files

```php
<?php
use app\modules\attachment\fileapi\widgets\File as FileAPIWidget;
?>
...
<?= $form->field($model, 'preview')->widget(
    FileAPIWidget::className(),
    [
        'url' => ['upload-preview'],
        'settings' => [
            'autoUpload' => true
        ]
    ]
)->label(false) ?>

```
