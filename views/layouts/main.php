
<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\services\SystemNotificationService;
use app\widgets\Alert;
use app\widgets\ToastAlert;
// use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;
YiiAsset::register($this);


// Force your AppAsset bundle to render its CSS/JS files onto this layout
AppAsset::register($this);

$notificationSummary = [
	'unreadCount' => 0,
	'items' => [],
];

if (!Yii::$app->user->isGuest) {
	$notificationSummary = SystemNotificationService::getUnreadSummaryForUser((int) Yii::$app->user->id);
}

$notificationItems = $notificationSummary['items'] ?? [];
$notificationCount = (int) ($notificationSummary['unreadCount'] ?? 0);
$markReadUrl = Url::to(['site/read-notifications']);
$markOneReadUrl = Url::to(['site/read-notification']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="Loans management">
        <meta name="robots" content="noindex, nofollow">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        
        
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="<?= Yii::$app->request->baseUrl ?>/theme/img/favicon.png">
        
        <?php $this->head() ?>
        
    </head>
    <body>

        <?php $this->beginBody() ?>

        <div id="global-loader" >
            <div class="whirly-loader"> </div>
        </div>
        <!-- Main Wrapper -->
        <div class="main-wrapper">

            <!-- Header -->
			<div class="header">

				<!-- Logo -->
				<div class="header-left active">
					<a href="index.html" class="logo logo-normal">
						<img src="<?= Yii::$app->request->baseUrl ?>/theme/img/logo.png" alt="">
					</a>
					<a href="index.html" class="logo logo-white">
						<img src="<?= Yii::$app->request->baseUrl ?>/theme/img/logo-white.png" alt="">
					</a>
					<a href="index.html" class="logo-small">
						<img src="<?= Yii::$app->request->baseUrl ?>/theme/img/logo-small.png" alt="">
					</a>
					<a id="toggle_btn" href="javascript:void(0);">
						<i data-feather="chevrons-left" class="feather-16"></i>
					</a>
				</div>
				<!-- /Logo -->

				<a id="mobile_btn" class="mobile_btn" href="#sidebar">
					<span class="bar-icon">
						<span></span>
						<span></span>
						<span></span>
					</span>
				</a>

				<!-- Header Menu -->
				<ul class="nav user-menu">

					<!-- Search -->
						<li class="nav-item nav-searchinputs">
							<div class="top-nav-search">
								<a href="javascript:void(0);" class="responsive-search">
									<i class="fa fa-search"></i>
								</a>
								<!-- <form action="#" class="dropdown">
									<div class="searchinputs dropdown-toggle" id="dropdownMenuClickable" data-bs-toggle="dropdown" data-bs-auto-close="false">
										<input type="text" placeholder="Search">
										<div class="search-addon">
											<span><i data-feather="x-circle" class="feather-14"></i></span>
										</div>
									</div>
									<div class="dropdown-menu search-dropdown" aria-labelledby="dropdownMenuClickable">
										<div class="search-info">
											<h6><span><i data-feather="search" class="feather-16"></i></span>Recent Searches
											</h6>
											<ul class="search-tags">
												<li><a href="javascript:void(0);">Products</a></li>
												<li><a href="javascript:void(0);">Sales</a></li>
												<li><a href="javascript:void(0);">Applications</a></li>
											</ul>
										</div>
										<div class="search-info">
											<h6><span><i data-feather="help-circle" class="feather-16"></i></span>Help</h6>
											<p>How to Change Product Volume from 0 to 200 on Inventory management</p>
											<p>Change Product Name</p>
										</div>
										<div class="search-info">
											<h6><span><i data-feather="user" class="feather-16"></i></span>Customers</h6>
											<ul class="customers">
												<li><a href="javascript:void(0);">Aron Varu<img src="<?= Yii::$app->request->baseUrl ?>/theme/img/profiles/avator1.jpg" alt="" class="img-fluid"></a></li>
												<li><a href="javascript:void(0);">Jonita<img src="<?= Yii::$app->request->baseUrl ?>/theme/img/profiles/avatar-01.jpg" alt="" class="img-fluid"></a></li>
												<li><a href="javascript:void(0);">Aaron<img src="<?= Yii::$app->request->baseUrl ?>/theme/img/profiles/avatar-10.jpg" alt="" class="img-fluid"></a></li>
											</ul>
										</div>
									</div>
								</form> -->
							</div>
						</li>
						<!-- /Search -->


					

					<!-- <li class="nav-item nav-item-box">
						<a href="email.html">
							<i data-feather="mail"></i>
							<span class="badge rounded-pill">1</span>
						</a>
					</li> -->
					<!-- Notifications -->
					<li class="nav-item dropdown nav-item-box">
						<a href="javascript:void(0);" id="notifications-toggle" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
							<i data-feather="bell"></i>
							<span id="notifications-badge" class="badge rounded-pill<?= $notificationCount > 0 ? '' : ' d-none' ?>"><?= $notificationCount ?></span>
						</a>
						<div class="dropdown-menu notifications">
							<div class="topnav-dropdown-header">
								<span class="notification-title">Notifications</span>
								<a href="javascript:void(0)" id="clear-notifications" class="clear-noti"> Clear All </a>
							</div>
							<div class="noti-content">
								<ul class="notification-list" id="notification-list">
									<?php if (empty($notificationItems)): ?>
										<li class="notification-message notification-empty">
											<a href="javascript:void(0);">
												<div class="media d-flex">
													<div class="media-body flex-grow-1">
														<p class="noti-details">No new notifications.</p>
													</div>
												</div>
											</a>
										</li>
									<?php else: ?>
										<?php foreach ($notificationItems as $item): ?>
											<li class="notification-message" data-unread="1" data-notification-type="<?= Html::encode((string) ($item['type'] ?? '')) ?>">
												<a href="javascript:void(0);">
													<div class="media d-flex">
														<div class="media-body flex-grow-1">
															<p class="noti-details"><span class="noti-title"><?= Html::encode((string) ($item['title'] ?? 'Update')) ?></span> <?= Html::encode((string) ($item['message'] ?? '')) ?></p>
															<p class="noti-time"><span class="notification-time">Unread</span></p>
														</div>
													</div>
												</a>
											</li>
										<?php endforeach; ?>
									<?php endif; ?>
								</ul>
							</div>
							<div class="topnav-dropdown-footer">
								<a href="javascript:void(0);">System activity notifications</a>
							</div>
						</div>
					</li>
					<!-- /Notifications -->

					<li class="nav-item dropdown has-arrow main-drop">
						<a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
							<span class="user-info">
								<span class="user-letter">
									<img src="<?= Yii::$app->request->baseUrl ?>/theme/img/profiles/avator1.jpg" alt="" class="img-fluid">
								</span>
								<span class="user-detail">
									<span class="user-name"><?= Yii::$app->user->identity->username ?? 'Guest' ?></span>
									<span class="user-role">Super Admin</span>
								</span>
							</span>
						</a>
						<div class="dropdown-menu menu-drop-user">
							<div class="profilename">
								<div class="profileset">
									<span class="user-img"><img src="<?= Yii::$app->request->baseUrl ?>/theme/img/profiles/avator1.jpg" alt="">
										<span class="status online"></span></span>
									<div class="profilesets">
										<h6><?= Yii::$app->user->identity->username ?? 'Guest' ?></h6>
										<h5>Super Admin</h5>
									</div>
								</div>
								<hr class="m-0">
								<a class="dropdown-item" href="#"> <i class="me-2" data-feather="user"></i> My
									Profile</a>
								<a class="dropdown-item" href="#"><i class="me-2"
										data-feather="settings"></i> Settings</a>
								<hr class="m-0">
								
								<?= \yii\helpers\Html::a(
									'<img src="' . Yii::getAlias('@web') . '/theme/img/icons/log-out.svg" class="me-2" alt="img">Logout',
									['site/logout'],
									[
										'class' => 'dropdown-item logout pb-0',
										'data-method' => 'post',
									]
								) ?>

										
							</div>
						</div>
					</li>
				</ul>
				<!-- /Header Menu -->

				<!-- Mobile Menu -->
				<div class="dropdown mobile-user-menu">
					<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
						aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="#">My Profile</a>
						<a class="dropdown-item" href="#">Settings</a>
						<a class="dropdown-item" href="<?= Yii::$app->urlManager->createUrl(['site/logout']) ?>" data-method="post">Logout</a>
					</div>
				</div>
				<!-- /Mobile Menu -->
			</div>
			<!-- /Header -->
			
			<!-- Sidebar -->
			<?= $this->render('_sidebar') ?>
			<!-- /Sidebar -->


            <div class="page-wrapper pagehead">
				
                <div class="content">
					
					<?= ToastAlert::widget() ?>

					<?= $content ?>
                    
				</div>
            </div>
        </div>
        <!-- /Main Wrapper -->
		<!-- <div class="customizer-links" id="setdata">
			<ul class="sticky-sidebar">
				<li class="sidebar-icons">
					<a href="#" class="navigation-add" data-bs-toggle="tooltip" data-bs-placement="left"
						data-bs-original-title="Theme">
						<i data-feather="settings" class="feather-five"></i>
					</a>
				</li>
			</ul>
		</div> -->
		
        <?php $this->endBody() ?>
		<style>
			.notification-message.is-processing {
				opacity: 0.6;
				pointer-events: none;
			}

			#clear-notifications.is-processing {
				opacity: 0.6;
				pointer-events: none;
			}
		</style>
		<script>
			window.AppConfig = {
				baseUrl: "<?= Yii::$app->request->baseUrl ?>",
				notificationsReadUrl: "<?= $markReadUrl ?>",
				notificationsReadOneUrl: "<?= $markOneReadUrl ?>"
			};

			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': yii.getCsrfToken()
				}
			});

			(function () {
				var markAllRequestSent = false;
				var $badge = $('#notifications-badge');
				var $notificationList = $('#notification-list');
				var $clearButton = $('#clear-notifications');
				var clearAllDefaultText = $.trim($clearButton.text()) || 'Clear All';
				var markOneRequests = {};

				function clearNotificationsUi() {
					$badge.text('0').addClass('d-none');
					$notificationList.html(
						'<li class="notification-message notification-empty">' +
							'<a href="javascript:void(0);">' +
								'<div class="media d-flex">' +
									'<div class="media-body flex-grow-1">' +
										'<p class="noti-details">No new notifications.</p>' +
									'</div>' +
								'</div>' +
							'</a>' +
						'</li>'
					);
				}

				function getBadgeCount() {
					var parsed = parseInt(($badge.text() || '').trim(), 10);
					return Number.isNaN(parsed) ? 0 : parsed;
				}

				function updateBadgeCount(newCount) {
					if (newCount <= 0) {
						$badge.text('0').addClass('d-none');
						return;
					}

					$badge.text(String(newCount)).removeClass('d-none');
				}

				function markNotificationTypeAsRead(type, $row) {
					if (!type || markOneRequests[type]) {
						return;
					}

					if ($badge.hasClass('d-none') || getBadgeCount() === 0) {
						return;
					}

					markOneRequests[type] = true;
					$row.addClass('is-processing');
					$row.find('.notification-time').text('Marking as read...');

					$.post(window.AppConfig.notificationsReadOneUrl, { type: type })
						.done(function () {
							$row.remove();

							var remaining = getBadgeCount() - 1;
							updateBadgeCount(Math.max(remaining, 0));

							if ($notificationList.find('li[data-notification-type]').length === 0) {
								clearNotificationsUi();
							}
						})
						.fail(function () {
							$row.removeClass('is-processing');
							$row.find('.notification-time').text('Unread');
						})
						.always(function () {
							delete markOneRequests[type];
						});
				}

				function markAllNotificationsAsRead() {
					if (markAllRequestSent) {
						return;
					}

					if ($badge.hasClass('d-none') || getBadgeCount() === 0) {
						return;
					}

					markAllRequestSent = true;
					$clearButton.addClass('is-processing').text('Clearing...');

					$.post(window.AppConfig.notificationsReadUrl)
						.done(function () {
							clearNotificationsUi();
						})
						.always(function () {
							markAllRequestSent = false;
							$clearButton.removeClass('is-processing').text(clearAllDefaultText);
						});
				}

				$notificationList.on('click', 'li[data-notification-type] > a', function (event) {
					event.preventDefault();
					var $row = $(this).closest('li[data-notification-type]');
					markNotificationTypeAsRead(($row.data('notification-type') || '').toString(), $row);
				});

				$clearButton.on('click', function (event) {
					event.preventDefault();
					markAllNotificationsAsRead();
				});
			})();
		</script>
    </body>
</html>
<?php $this->endPage() ?>