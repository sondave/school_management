<?php
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
														
                <!-- <li class="submenu-open">
                    <h6 class="submenu-hdr">User Management</h6>		
                    <ul>
                        <li><a href="users.html"><i data-feather="user-check"></i><span>Users</span></a></li>
                        <li><a href="roles-permissions.html"><i data-feather="shield"></i><span>Roles & Permissions</span></a></li>
                        <li><a href="delete-account.html"><i data-feather="lock"></i><span>Delete Account Request</span></a></li>
                    
                    </ul>
                </li> -->

                <li class="submenu-open">
                    <!-- <h6 class="submenu-hdr">Settings</h6>		 -->
                    <ul>
                        <li>
                            <a href="<?= \yii\helpers\Url::to(['/']) ?>" ><i data-feather="grid"></i><span>Dashboard</span> </a>
                        </li>

                        <li>
                            <a href="<?= \yii\helpers\Url::to(['lead/index']) ?>" ><i data-feather="user-plus"></i><span>Leads</span> </a>
                        </li>
                        
                        <li class="submenu">
                            <a href="javascript:void(0);"><i data-feather="users"></i><span>Customers</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="<?= \yii\helpers\Url::to(['customer/index', 'scope' => 'all']) ?>">All Customers</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['customer/index', 'scope' => 'active']) ?>">Active Customers</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['customer/index', 'scope' => 'pending']) ?>">Pending Payments</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['customer/index', 'scope' => 'banned']) ?>">Banned Customers</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?= \yii\helpers\Url::to(['parents/index']) ?>"><i data-feather="users"></i><span>Parents</span></a>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><i data-feather="book-open"></i><span>Teachers</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="<?= \yii\helpers\Url::to(['teachers/index']) ?>">Teachers</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['teachers/class-teachers/index']) ?>">Class Teachers</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="javascript:void(0);"><i data-feather="dollar-sign"></i><span>Loans</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="<?= \yii\helpers\Url::to(['loan/create']) ?>">Disburse New</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['loan/index', 'scope' => 'all']) ?>">All Loans</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['loan/index', 'scope' => 'pending']) ?>">Pending Approval</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['loan/index', 'scope' => 'disbursed']) ?>">Disbursed</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['loan/index', 'scope' => 'partially-paid']) ?>">Partially Paid</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['loan/index', 'scope' => 'fully-paid']) ?>">Fully Paid</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['loan/index', 'scope' => 'written-off']) ?>">Written Off Loans</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="<?= \yii\helpers\Url::to(['transactions/index']) ?>"><i data-feather="repeat"></i><span>Transactions</span></a>
                        </li>

                        
                        
                        

                        <li class="submenu">
                            <a href="javascript:void(0);"><i data-feather="bell"></i><span>Notifications</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li class="submenu submenu-two"><a href="javascript:void(0);">SMS Notifications<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <!-- <li><a href="video-call.html">Send SMS</a></li> -->
                                        <li><a href="<?= \yii\helpers\Url::to(['notifications/sms-notification/index']) ?>">SMS Notifications</a></li>
                                    </ul>
                                </li>
                                <li class="submenu submenu-two"><a href="javascript:void(0);">Email Notifications<span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <!-- <li><a href="#">Send Email</a></li> -->
                                        <li><a href="<?= \yii\helpers\Url::to(['notifications/email-notification/index']) ?>">Email Notifications</a></li>
                                    </ul>
                                </li>
                            </ul>
                            
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);"><i data-feather="user-check"></i><span>User Management</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="<?= \yii\helpers\Url::to(['user/index']) ?>">Users</a></li>
                            </ul>
                        </li>

                        <li class="submenu">
                            <a href="javascript:void(0);"><i data-feather="file-text"></i><span>Reports</span><span class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="<?= \yii\helpers\Url::to(['reports/index']) ?>">Reports Home</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['reports/portfolio']) ?>">Portfolio Summary</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['reports/repayments']) ?>">Repayments Ledger</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['reports/overdue']) ?>">Overdue Loans</a></li>
                            </ul>
                        </li>



                        <li class="submenu">
                            <a href="javascript:void(0);"><i data-feather="settings"></i><span>Settings</span><span class="menu-arrow"></span></a>
                            <ul>
            
                                <!-- <li><a href="<?= \yii\helpers\Url::to(['settings/general/index']) ?>">General</a></li> -->
                                <li><a href="<?= \yii\helpers\Url::to(['settings/school-info/index']) ?>">School Info</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['settings/subjects/index']) ?>">Subjects</a></li>
                                <li><a href="<?= \yii\helpers\Url::to(['settings/lookup-values/index']) ?>">Lookup Values</a></li>


                                <li class="submenu submenu-two"><a href="javascript:void(0);" class=" subdrop"><span>Academic Years</span><span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="<?= \yii\helpers\Url::to(['settings/academic-years/index']) ?>">Academic Years</a></li>
                                        <li><a href="<?= \yii\helpers\Url::to(['settings/terms/index']) ?>">Terms</a></li>
                                    </ul>
                                </li>

                                <li class="submenu submenu-two"><a href="javascript:void(0);" class=" subdrop"><span>Grade Settings</span><span class="menu-arrow inside-submenu"></span></a>
                                    <ul>
                                        <li><a href="<?= \yii\helpers\Url::to(['settings/grades/index']) ?>">Grades</a></li>
                                        <li><a href="<?= \yii\helpers\Url::to(['settings/streams/index']) ?>">Streams</a></li>
                                        <li><a href="<?= \yii\helpers\Url::to(['settings/grade-streams/index']) ?>">Grade Streams</a></li>
                                        <li><a href="<?= \yii\helpers\Url::to(['settings/grade-subjects/index']) ?>">Grade Subjects</a></li>
                                    </ul>
                                </li>

                            </ul>
                        </li>

                        
                        <li>
                            <a href="<?= Yii::$app->urlManager->createUrl(['site/logout']) ?>" data-method="post"><i data-feather="log-out"></i><span>Logout</span> </a>
                        </li>
                    
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>