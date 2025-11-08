<?php

use app\custom\PermissionHelper;
use yii\helpers\Html;
?>

<?php if (
	PermissionHelper::check('/invoice/default') 
): ?>
    <li class="slide">
        <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
            <span class="side-menu__label"><i class="fe fe-file-text"></i> Hóa đơn </span><i class="angle bi bi-caret-right"></i>
        </a>

        <ul class="slide-menu" data-menu="hd">
            <li class="panel sidetab-menu">
                <div class="panel-body tabs-menu-body p-0 border-0">
                    <div class="tab-content">
                        <div class="tab-pane active" id="side7">
                            <ul class="sidemenu-list">
                                <?php if (PermissionHelper::check('/invoice/default')): ?>
                                    <li>
                                        <?= Html::a('<i class="fe fe-file-plus"></i> Hóa đơn', ['/invoice/default', 'menu'=>'hd1'], ['class' => 'slide-item', 'data-menu' => 'hd1']) ?>
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