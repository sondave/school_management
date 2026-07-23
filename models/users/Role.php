<?php

declare(strict_types=1);

namespace app\models\users;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\rbac\Item;

/**
 * This is the model class for table "auth_item" when type = role.
 *
 * @property string $name
 * @property int $type
 * @property string|null $description
 * @property string|null $rule_name
 * @property string|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Role extends ActiveRecord
{
    public const TYPE_ROLE = Item::TYPE_ROLE;

    /** @var string[] */
    public array $permissionNames = [];

    public static function tableName(): string
    {
        return 'auth_item';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'trim'],
            [['name'], 'unique'],
            [['type'], 'default', 'value' => self::TYPE_ROLE],
            [['type'], 'in', 'range' => [self::TYPE_ROLE]],
            [['permissionNames'], 'safe'],
            [['permissionNames'], 'each', 'rule' => ['string', 'max' => 64]],
        ];
    }

    public function beforeValidate(): bool
    {
        $this->type = self::TYPE_ROLE;
        return parent::beforeValidate();
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $now = time();
        $this->updated_at = $now;
        if ($insert) {
            $this->created_at = $now;
        }

        $this->type = self::TYPE_ROLE;
        return true;
    }

    public function afterFind(): void
    {
        parent::afterFind();

        $this->permissionNames = (new Query())
            ->select('child')
            ->from('auth_item_child')
            ->where(['parent' => $this->name])
            ->column();
    }

    public function saveWithPermissions(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->save(false)) {
                $transaction->rollBack();
                return false;
            }

            $this->syncPermissions();
            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->addError('name', 'Unable to save role.');
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Role Name',
            'description' => 'Description',
            'permissionNames' => 'Permissions',
        ];
    }

    public static function getPermissionOptions(): array
    {
        $rows = (new Query())
            ->select([
                'p.name AS permission_name',
                'g.name AS group_name',
            ])
            ->from(['p' => 'auth_item'])
            ->leftJoin(['g' => 'auth_item_group'], 'g.id = p.auth_item_group_id')
            ->where(['p.type' => Permission::TYPE_PERMISSION])
            ->orderBy(['g.name' => SORT_ASC, 'p.name' => SORT_ASC])
            ->all();

        $options = [];
        foreach ($rows as $row) {
            $group = (string) ($row['group_name'] ?: 'Ungrouped');
            $permissionName = (string) $row['permission_name'];
            $options[$group][$permissionName] = $permissionName;
        }

        return $options;
    }

    private function syncPermissions(): void
    {
        Yii::$app->db->createCommand()
            ->delete('auth_item_child', ['parent' => $this->name])
            ->execute();

        $selected = array_values(array_unique(array_filter(array_map('strval', $this->permissionNames))));
        if ($selected === []) {
            return;
        }

        $validPermissions = (new Query())
            ->select('name')
            ->from('auth_item')
            ->where(['type' => Permission::TYPE_PERMISSION, 'name' => $selected])
            ->column();

        if ($validPermissions === []) {
            return;
        }

        $rows = [];
        foreach ($validPermissions as $permissionName) {
            $rows[] = [$this->name, (string) $permissionName];
        }

        Yii::$app->db->createCommand()
            ->batchInsert('auth_item_child', ['parent', 'child'], $rows)
            ->execute();
    }
}
