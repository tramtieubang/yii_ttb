<?php

return [
     
    'categories' => [ 
        'class' => 'app\modules\categories\Module', 
    ],
    'products' => [ 
        'class' => 'app\modules\products\Module', 
    ],
    'home' => [ 
        'class' => 'app\modules\home\Module', 
    ],
    'customers' => [ 
        'class' => 'app\modules\customers\Module', 
    ],
    'units' => [ 
        'class' => 'app\modules\units\Module', 
    ],
    'product_prices_unit' => [ 
        'class' => 'app\modules\product_prices_unit\Module', 
    ],

    'invoice' => [ 
        'class' => 'app\modules\invoice\Module', 
    ],

    // phan quyen
    /* 'user' => [ 
        'class' => 'app\modules\userManagements\user\Module', 
    ],
    'role' => [ 
        'class' => 'app\modules\userManagements\role\Module', 
    ],
    'permissiongroup' => [ 
        'class' => 'app\modules\userManagements\permissionGroup\Module', 
    ],
    'permission' => [ 
        'class' => 'app\modules\userManagements\permission\Module', 
    ],  */

   'user_management' => [
        'class' => 'app\modules\user_management\Module',
    ],

    ///////////
    'alsystems' => [ 
        'class' => 'app\modules\alsystems\Module', 
    ],

    'alprofiles' => [ 
        'class' => 'app\modules\alprofiles\Module', 
    ],

    'alaluminummaterials' => [ 
        'class' => 'app\modules\alaluminummaterials\Module', 
    ],


    ////////////
    'chat' => [ 
        'class' => 'app\modules\chat\Module', 
    ],

];