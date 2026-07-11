<?php

declare(strict_types=1);

namespace app\models\operations;

use app\models\User;
use app\models\settings\Supplier;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "inventoy".
 *
 * @property int $id
 * @property string $accesory_type
 * @property int $inventory_item_id
 * @property int $supplier_id
 * @property string|null $remarks
 * @property int|null $quantity
 * @property string|null $received_on
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Inventory extends ActiveRecord
{
    public const ACCESSORY_EXERCISE_BOOKS = 'exercise_books';
    public const ACCESSORY_TEXT_BOOKS = 'text_books';
    public const ACCESSORY_CHALKS = 'chalks';
    public const ACCESSORY_MANILLA_PAPERS = 'manilla_papers';
    public const ACCESSORY_PENCILS = 'pencils';
    public const ACCESSORY_LAB_EQUIPMENTS = 'lab_equipments';

    public static function tableName(): string
    {
        return 'inventoy';
    }

    public function behaviors(): array
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
            [['accesory_type', 'inventory_item_id', 'supplier_id'], 'required'],
            [['inventory_item_id', 'supplier_id', 'quantity', 'created_by', 'updated_by'], 'integer'],
            [['remarks'], 'string'],
            [['received_on', 'created_at', 'updated_at'], 'safe'],
            [['accesory_type'], 'string', 'max' => 50],
            [['remarks'], 'trim'],
            [['accesory_type'], 'in', 'range' => array_keys(self::getAccessoryTypeOptions())],
            [['inventory_item_id'], 'exist', 'targetClass' => \app\models\operations\InventoryItem::class, 'targetAttribute' => ['inventory_item_id' => 'id']],
            [['supplier_id'], 'exist', 'targetClass' => Supplier::class, 'targetAttribute' => ['supplier_id' => 'id']],
            [['received_on'], 'date', 'format' => 'php:Y-m-d'],
            [['quantity'], 'integer', 'min' => 0],
            ['inventory_item_id', 'validateInventoryItemMatchesAccessoryType'],
        ];
    }

    public function validateInventoryItemMatchesAccessoryType(string $attribute): void
    {
        if ($this->hasErrors('accesory_type') || empty($this->accesory_type) || empty($this->$attribute)) {
            return;
        }

        $exists = \app\models\operations\InventoryItem::find()
            ->where([
                'id' => (int) $this->$attribute,
                'accesory_type' => $this->accesory_type,
            ])
            ->exists();

        if (!$exists) {
            $this->addError($attribute, 'Selected inventory item does not match the selected accessory type.');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'accesory_type' => 'Accessory Type',
            'inventory_item_id' => 'Inventory Item',
            'supplier_id' => 'Supplier',
            'remarks' => 'Remarks',
            'quantity' => 'Quantity',
            'received_on' => 'Received On',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getAccessoryTypeOptions(): array
    {
        return [
            self::ACCESSORY_EXERCISE_BOOKS => 'Exercise Books',
            self::ACCESSORY_TEXT_BOOKS => 'Text Books',
            self::ACCESSORY_CHALKS => 'Chalks',
            self::ACCESSORY_MANILLA_PAPERS => 'Manilla Papers',
            self::ACCESSORY_PENCILS => 'Pencils',
            self::ACCESSORY_LAB_EQUIPMENTS => 'Laboratory Equipments',
        ];
    }

    public static function getSupplierOptions(): array
    {
        return Supplier::find()
            ->select(['name', 'id'])
            ->orderBy(['name' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public function getAccessoryTypeLabel(): string
    {
        return self::getAccessoryTypeOptions()[$this->accesory_type] ?? $this->accesory_type;
    }

    public static function getInventoryItemOptions(?string $accesoryType = null): array
    {
        return \app\models\operations\InventoryItem::getOptions($accesoryType);
    }

    public function getInventoryItemLabel(): string
    {
        return $this->inventoryItem?->name ?? '-';
    }

    public function getSupplierLabel(): string
    {
        return $this->supplier?->name ?? '-';
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id' => 'supplier_id']);
    }

    public function getInventoryItem()
    {
        return $this->hasOne(\app\models\operations\InventoryItem::class, ['id' => 'inventory_item_id']);
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