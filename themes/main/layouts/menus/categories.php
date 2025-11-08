<?php

use app\custom\PermissionHelper;
use yii\helpers\Html;
?>

<?php if (
	PermissionHelper::check('categories/default/index') ||
	PermissionHelper::check('customers/default/index') ||
	PermissionHelper::check('units/default/index') 
): ?>
    <li class="slide">
        <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
            <span class="side-menu__label"><i class="fe fe-list"></i> Danh mục </span><i class="angle bi bi-caret-right"></i>
        </a>

        <ul class="slide-menu" data-menu="ns">
            <li class="panel sidetab-menu">
                <div class="panel-body tabs-menu-body p-0 border-0">
                    <div class="tab-content">
                        <div class="tab-pane active" id="side7">
                            <ul class="sidemenu-list">
                                <?php if (PermissionHelper::check('categories/default')): ?>
                                    <li>
                                        <?= Html::a('<i class="fe fe-grid"></i> Danh mục sản phẩm', ['/categories/default', 'menu'=>'ns1'], ['class' => 'slide-item', 'data-menu' => 'ns1']) ?>
                                    </li>
                                <?php endif; ?>
                                <?php if (PermissionHelper::check('customers/default')): ?>
                                    <li>
                                        <?= Html::a('<i class="fe fe-users"></i> Danh mục khách hàng', ['/customers/default', 'menu'=>'ns2'], ['class' => 'slide-item', 'data-menu' => 'ns2']) ?>
                                    </li>
                                <?php endif; ?>
                                <?php if (PermissionHelper::check('units/default')): ?>
                                    <li>
                                        <?= Html::a('<i class="fe fe-slack"></i> Danh mục đơn vị tính', ['/units/default', 'menu'=>'ns3'], ['class' => 'slide-item', 'data-menu' => 'ns3']) ?>
                                    </li>
                                <?php endif; ?>
                                
                            </ul>
                            <div class="menutabs-content px-0">
                                <!-- menu tab here -->
                            </div>
                        </div>
                        <div class="tab-pane" id="side8">
                            <!-- activity here -->
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </li>
<?php endif; ?>