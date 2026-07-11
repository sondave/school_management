<?php

declare(strict_types=1);

namespace app\models\operations;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stock_levels".
 *
 * @property int $id
 * @property int $inventory_item_id
 * @property int $total_received
 * @property int $total_issued
 * @property int $total_returned
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class StockLevel extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'stock_levels';
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
        ];
    }

    public function rules(): array
    {
        return [
            [['inventory_item_id'], 'required'],
            [['inventory_item_id', 'total_received', 'total_issued', 'total_returned'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['inventory_item_id'], 'unique'],
            [['total_received', 'total_issued', 'total_returned'], 'default', 'value' => 0],
            [['inventory_item_id'], 'exist', 'targetClass' => InventoryItem::class, 'targetAttribute' => ['inventory_item_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'inventory_item_id' => 'Inventory Item',
            'total_received' => 'Total Received',
            'total_issued' => 'Total Issued',
            'total_returned' => 'Total Returned',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function ensureForInventoryItem(int $inventoryItemId): self
    {
        $model = self::findOne(['inventory_item_id' => $inventoryItemId]);
        if ($model !== null) {
            return $model;
        }

        $model = new self();
        $model->inventory_item_id = $inventoryItemId;
        $model->total_received = 0;
        $model->total_issued = 0;
        $model->total_returned = 0;
        $model->save(false);

        return $model;
    }

    public function getInventoryItem()
    {
        return $this->hasOne(InventoryItem::class, ['id' => 'inventory_item_id']);
    }
}