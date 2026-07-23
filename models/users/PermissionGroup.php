<?php

declare(strict_types=1);

namespace app\models\users;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_item_group".
 *
 * @property int $id
 * @property string $name
 */
class PermissionGroup extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'auth_item_group';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'trim'],
            [['name'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
}
