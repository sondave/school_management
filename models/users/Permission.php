<?php

declare(strict_types=1);

namespace app\models\users;

use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\rbac\Item;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property int $type
 * @property string|null $description
 * @property int|null $auth_item_group_id
 * @property string|null $rule_name
 * @property string|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Permission extends ActiveRecord
{
    public const TYPE_PERMISSION = Item::TYPE_PERMISSION;

    private static ?array $routeOptions = null;

    public static function tableName(): string
    {
        return 'auth_item';
    }

    public function rules(): array
    {
        return [
            [['name', 'auth_item_group_id','description'], 'required'],
            [['type', 'auth_item_group_id', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'trim'],
            [['name'], 'unique'],
            [['type'], 'default', 'value' => self::TYPE_PERMISSION],
            [['type'], 'in', 'range' => [self::TYPE_PERMISSION]],
            [['auth_item_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => PermissionGroup::class, 'targetAttribute' => ['auth_item_group_id' => 'id']],
        ];
    }

    public function beforeValidate(): bool
    {
        $this->type = self::TYPE_PERMISSION;
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

        $this->type = self::TYPE_PERMISSION;
        return true;
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Permission Key',
            'description' => 'Description',
            'auth_item_group_id' => 'Permission Group',
        ];
    }

    public static function getGroupOptions(): array
    {
        return PermissionGroup::find()
            ->select(['name', 'id'])
            ->orderBy(['name' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public static function getRouteOptions(): array
    {
        if (self::$routeOptions !== null) {
            return self::$routeOptions;
        }

        $controllerPath = \Yii::getAlias('@app/controllers');
        $files = FileHelper::findFiles($controllerPath, ['only' => ['*Controller.php']]);
        $routes = [];

        foreach ($files as $file) {
            $controllerPrefix = self::resolveControllerPrefix($controllerPath, $file);
            if ($controllerPrefix === '') {
                continue;
            }

            $contents = file_get_contents($file);
            if ($contents === false) {
                continue;
            }

            if (!preg_match_all('/function\s+action([A-Z][A-Za-z0-9_]*)\s*\(/', $contents, $matches)) {
                continue;
            }

            foreach ($matches[1] as $actionName) {
                $actionId = self::camelToId((string) $actionName);
                $route = $controllerPrefix . '/' . $actionId;
                $routes[$route] = $route;
            }
        }

        ksort($routes, SORT_NATURAL | SORT_FLAG_CASE);
        self::$routeOptions = $routes;

        return self::$routeOptions;
    }

    private static function resolveControllerPrefix(string $controllerPath, string $file): string
    {
        $relativePath = str_replace($controllerPath . DIRECTORY_SEPARATOR, '', $file);
        $relativePath = str_replace('\\', '/', $relativePath);
        $relativePath = preg_replace('/Controller\.php$/', '', $relativePath) ?? '';

        if ($relativePath === '') {
            return '';
        }

        $segments = explode('/', $relativePath);
        $controllerClass = (string) array_pop($segments);
        $controllerId = self::camelToId($controllerClass);

        $prefix = implode('/', array_filter($segments, static fn(string $part): bool => $part !== ''));

        return $prefix !== '' ? ($prefix . '/' . $controllerId) : $controllerId;
    }

    private static function camelToId(string $value): string
    {
        $value = preg_replace('/(?<!^)[A-Z]/', '-$0', $value) ?? $value;
        return strtolower($value);
    }

    public function getGroup()
    {
        return $this->hasOne(PermissionGroup::class, ['id' => 'auth_item_group_id']);
    }
}
