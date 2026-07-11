<?php
namespace app\widgets;

use Yii;
use yii\bootstrap5\Widget;
use yii\helpers\Html;

class ToastAlert extends Widget
{
    public function run()
    {
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();

        if (empty($flashes)) {
            return '';
        }

        // 1. Fixed positioning container for standard stacking behavior
        echo Html::beginTag('div', [
            'class' => 'toast-container position-fixed top-0 end-0 p-3',
            'style' => 'z-index: 1090; margin-top: 20px;'
        ]);

        foreach ($flashes as $type => $flash) {
            // 2. Map flash keys directly to your template's background colors
            $bgClass = 'bg-info';
            $title = 'Notification';

            if ($type === 'success') {
                $bgClass = 'bg-success';
                $title = 'Success';
            } elseif ($type === 'error' || $type === 'danger') {
                $bgClass = 'bg-danger';
                $title = 'Error';
            } elseif ($type === 'warning') {
                $bgClass = 'bg-warning';
                $title = 'Warning';
            }

            $messages = is_array($flash) ? $flash : [$flash];

            foreach ($messages as $message) {
                ?>
                <div class="toast colored-toast <?= $bgClass ?> text-fixed-white show shadow-lg custom-system-toast" 
                     role="alert" 
                     aria-live="assertive" 
                     aria-atomic="true" 
                     style="min-width: 300px; margin-bottom: 10px; display: block; opacity: 1;">
                     
                    <div class="toast-header <?= $bgClass ?> text-fixed-white border-0">
                        <strong class="me-auto"><?= Html::encode($title) ?></strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    
                    <div class="toast-body">
                        <?= Html::encode($message) ?>
                    </div>
                </div>
                <?php
            }

            // Remove from session cache so messages don't double loop repeat
            $session->removeFlash($type);
        }

        echo Html::endTag('div');

        // 4. Smooth Javascript Auto-Dismiss Rule
        $this->view->registerJs("
            setTimeout(function() {
                $('.custom-system-toast').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 4500);
        ");
    }
}