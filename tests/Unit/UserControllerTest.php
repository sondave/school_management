<?php

declare(strict_types=1);

namespace app\tests\Unit;

use app\controllers\UserController;
use Yii;

final class UserControllerTest extends \Codeception\Test\Unit
{
    public function testReplaceTemplateParamsReplacesKnownPlaceholders(): void
    {
        $controller = new class('user', Yii::$app) extends UserController {
            public function invokeReplaceTemplateParams(string $message, array $context): string
            {
                return $this->replaceTemplateParams($message, $context);
            }
        };

        $message = 'Hello {full_name}, your activation password is {activation_password}';
        $context = [
            'full_name' => 'Jane Doe',
            'activation_password' => 'abc123',
        ];

        self::assertSame('Hello Jane Doe, your activation password is abc123', $controller->invokeReplaceTemplateParams($message, $context));
    }
}
