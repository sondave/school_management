

<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\helpers\Html;

// Force your AppAsset bundle to render its CSS/JS files onto this layout
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="robots" content="noindex, nofollow">
        <title><?= Html::encode($this->title) ?></title>
		<?php $this->registerCsrfMetaTags() ?>
		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="<?= Yii::$app->request->baseUrl ?>/theme/img/favicon.png">
		<?php $this->head() ?>
		
		
    </head>
    <body class="account-page">

		<?php $this->beginBody() ?>
        <div id="global-loader" >
			<div class="whirly-loader"> </div>
		</div>
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
			<div class="account-content">
				<div class="login-wrapper login-new">
                    <div class="container">
                        
                        <?= $content ?>
                        
                        <div class="my-4 d-flex justify-content-center align-items-center copyright-text">
                            <p>Copyright &copy; <?= date('Y') ?> <a href="https://www.savitech.co.ke" target="_blank">Savitech Solutions</a>. All rights reserved</p>
                        </div>
                    </div>
                </div>
			</div>
        </div>
		<!-- /Main Wrapper -->
		<div class="customizer-links" id="setdata">
			<ul class="sticky-sidebar">
				<li class="sidebar-icons">
					<a href="#" class="navigation-add" data-bs-toggle="tooltip" data-bs-placement="left"
						data-bs-original-title="Theme">
						<i data-feather="settings" class="feather-five"></i>
					</a>
				</li>
			</ul>
		</div>

		<?php $this->endBody() ?>

	
    </body>
</html>
<?php $this->endPage() ?>