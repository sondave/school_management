<?php

declare(strict_types=1);

namespace app\models\operations;

use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "inventory_items".
 *
 * @property int $id
 * @property string $accesory_type
 * @property string $name
 * @property string|null $description
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class InventoryItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'inventory_items';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['accesory_type', 'name'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['accesory_type'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 255],
            [['name'], 'trim'],
            [['accesory_type'], 'in', 'range' => array_keys(Inventory::getAccessoryTypeOptions())],
            [['accesory_type', 'name'], 'unique', 'targetAttribute' => ['accesory_type', 'name'], 'message' => 'This inventory item name already exists for the selected accessory type.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'accesory_type' => 'Accessory Type',
            'name' => 'Name',
            'description' => 'Description',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getOptions(?string $accesoryType = null): array
    {
        $query = self::find()
            ->select(['name', 'id'])
            ->orderBy(['name' => SORT_ASC])
            ->indexBy('id');

        if ($accesoryType !== null && $accesoryType !== '') {
            $query->andWhere(['accesory_type' => $accesoryType]);
        }

        return $query->column();
    }

    public function getAccessoryTypeLabel(): string
    {
        return Inventory::getAccessoryTypeOptions()[$this->accesory_type] ?? $this->accesory_type;
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            StockLevel::ensureForInventoryItem((int) $this->id);
        }
    }

    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}