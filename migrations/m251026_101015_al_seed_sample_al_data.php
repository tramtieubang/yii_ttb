<?php

use yii\db\Migration;

class m251026_101015_al_seed_sample_al_data extends Migration
{
     public function safeUp()
    {
        $now = date('Y-m-d H:i:s');

        // Tạm tắt kiểm tra khóa ngoại
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');

        // Danh sách bảng cần xóa dữ liệu trước khi seed
        $tables = [
            '{{%al_reuse_log}}',
            '{{%al_scrap_aluminum}}',
            '{{%al_cut_groups}}',
            '{{%al_order_details}}',
            '{{%al_orders}}',
            '{{%al_quotations}}',
            '{{%al_pricing_table}}',
            '{{%al_aluminum_materials}}',
            '{{%al_profiles}}',
            '{{%al_systems}}',
            '{{%customers}}',
        ];
        foreach ($tables as $t) {
            $this->execute("TRUNCATE TABLE {$t};");
        }

        // 1️⃣ Seed bảng al_systems
        // Thêm dữ liệu mẫu cho bảng al_systems
        $this->batchInsert('{{%al_systems}}', 
            ['code', 'name', 'brand', 'origin', 'thickness', 'color', 'surface_type', 'description', 'status'], 
            [
                ['XF55', 'Xingfa 55', 'Xingfa', 'Trung Quốc', 1.4, 'Ghi xám', 'Sơn tĩnh điện', 'Dòng nhôm phổ biến cho cửa đi mở quay và cửa sổ', 'active'],
                ['XF63', 'Xingfa 63', 'Xingfa', 'Trung Quốc', 1.6, 'Đen', 'Sơn tĩnh điện', 'Phù hợp cửa trượt, độ dày cao hơn XF55', 'active'],
                ['PMA60', 'PMA 60', 'PMA', 'Việt Nam', 1.2, 'Trắng sứ', 'Sơn tĩnh điện', 'Hệ nhôm sản xuất trong nước, chất lượng ổn định', 'active'],
                ['TP50', 'Topal 50', 'Topal', 'Việt Nam', 1.2, 'Ghi xám', 'Sơn tĩnh điện', 'Dòng nhôm thông dụng dùng cho cửa sổ, cửa đi nhỏ', 'active'],
                ['VP4400', 'Việt Pháp 4400', 'Việt Pháp', 'Việt Nam', 1.4, 'Nâu cà phê', 'Anod', 'Dòng cao cấp của Việt Pháp, bề mặt bóng đẹp', 'active'],
                ['HY75', 'Hyundai 75', 'Hyundai', 'Hàn Quốc', 1.8, 'Bạc ánh kim', 'Anod', 'Nhôm nhập khẩu, chất lượng cao, dùng cho công trình lớn', 'active'],
            ]
        );
        /* $systems = [
            ['XF', 'Xingfa', 'Trung Quốc', 'Hệ nhôm Xingfa phổ biến', 'active', $now, $now],
            ['TP', 'Topal', 'Việt Nam', 'Hệ nhôm Topal của Austdoor', 'active', $now, $now],
            ['PMA', 'PMA', 'Trung Quốc', 'Hệ nhôm PMA giá tốt', 'active', $now, $now],
            ['VP', 'Việt Pháp', 'Việt Nam', 'Hệ nhôm Việt Pháp cao cấp', 'active', $now, $now],
            ['YL', 'Yongli', 'Trung Quốc', 'Hệ nhôm Yongli thông dụng', 'active', $now, $now],
        ];
        $this->batchInsert('{{%al_systems}}',
            ['code', 'name', 'origin', 'description', 'status', 'created_at', 'updated_at'],
            $systems
        ); */

        $systemIds = (new \yii\db\Query())->select('id')->from('{{%al_systems}}')->column();

        // 2️⃣ Seed bảng al_profiles
        $profiles = [];
        $doorTypesList = ['mở quay','trượt','lùa','xếp trượt'];
        for ($i=1; $i<=10; $i++) {
            $profiles[] = [
                'system_id' => $systemIds[array_rand($systemIds)],
                'code' => "PRF" . str_pad($i,3,'0',STR_PAD_LEFT),
                'name' => "Profile mẫu $i",
                'door_types' => implode(',', array_slice($doorTypesList, 0, rand(1, count($doorTypesList)))),
                'length' => 6000,
                'weight_per_meter' => round(0.8 + mt_rand(0,50)/100,2),
                'unit_price' => round(200000 + mt_rand(0,200000),0),
                'image_url' => null,
                'note' => "Profile seed $i",
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        $this->batchInsert('{{%al_profiles}}',
            ['system_id','code','name','door_types','length','weight_per_meter','unit_price','image_url','note','status','created_at','updated_at'],
            $profiles
        );

        // 3️⃣ Seed customers (10 mẫu)
        $customers = [];
        for ($i=1; $i<=10; $i++) {
            $customers[] = [
                "Khách hàng $i",
                "customer{$i}@example.local",
                '0901' . str_pad($i,6,'0',STR_PAD_LEFT),
                "Địa chỉ số $i",
                "Ghi chú KH $i",
                $now,
                $now,
            ];
        }
        $this->batchInsert('{{%customers}}',
            ['name','email','phone','address','note','created_at','updated_at'],
            $customers
        );

        // 4️⃣ Seed materials (10 mẫu)
        $profileIds = (new \yii\db\Query())->select('id')->from('{{%al_profiles}}')->column();
        $materials = [];
        for ($i=1; $i<=10; $i++) {
            $materials[] = [
                $profileIds[array_rand($profileIds)],
                'NHOM' . str_pad($i,3,'0',STR_PAD_LEFT),
                "Thanh nhôm $i",
                6000,
                rand(5, 30),
                rand(6000, 6000*20),
                round(150000 + mt_rand(0,200000),0),
                "Vật liệu seed $i",
                $now,
                $now,
            ];
        }
        $this->batchInsert('{{%al_aluminum_materials}}',
            ['profile_id','code','name','length','stock_quantity','stock_length','unit_price','note','created_at','updated_at'],
            $materials
        );

        // 5️⃣ Seed pricing_table
        $pricing = [
            [null, 'NHOM55', 'Nhôm hệ 55 (m)', 'm', 350000, 0, 10, 'Giá tham khảo'],
            [null, 'KINH08', 'Kính 8mm (m2)', 'm2', 450000, 0, 10, 'Kính cường lực'],
            [null, 'PK01', 'Phụ kiện bộ', 'bộ', 500000, 0, 10, 'Bản lề, khóa'],
            [null, 'LD01', 'Lắp đặt 1 bộ', 'công', 250000, 0, 15, 'Công lắp đặt'],
            [null, 'KG01', 'Keo & gioăng', 'bộ', 150000, 0, 10, 'Vật tư phụ'],
        ];
        $this->batchInsert('{{%al_pricing_table}}',
            ['profile_id','item_code','item_name','unit','base_price','labor_cost','profit_percent','note','created_at','updated_at'],
            array_map(fn($r) => array_merge($r, [$now, $now]), $pricing)
        );

        // 6️⃣ Seed quotations
        $customerIds = (new \yii\db\Query())->select('id')->from('{{%customers}}')->column();
        for ($i=1; $i<=5; $i++) {
            $this->insert('{{%al_quotations}}', [
                'quotation_code' => 'BG-' . date('Ymd') . '-' . str_pad($i,3,'0',STR_PAD_LEFT),
                'customer_id' => $customerIds[array_rand($customerIds)],
                'project_name' => "Công trình mẫu $i",
                'quotation_date' => date('Y-m-d'),
                'subtotal' => 1000000 * $i,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => 1000000 * $i,
                'status' => 'approved',
                'note' => 'Báo giá mẫu',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 7️⃣ Seed orders
        $quotationIds = (new \yii\db\Query())->select('id')->from('{{%al_quotations}}')->column();
        for ($i=1; $i<=5; $i++) {
            $this->insert('{{%al_orders}}', [
                'order_code' => 'ORD-' . date('Ymd') . '-' . str_pad($i,3,'0',STR_PAD_LEFT),
                'customer_id' => $customerIds[array_rand($customerIds)],
                'quotation_id' => $quotationIds[array_rand($quotationIds)],
                'order_date' => date('Y-m-d'),
                'status' => 'pending',
                'total_amount' => 1200000 * $i,
                'description' => 'Đơn hàng mẫu ' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 8️⃣ Seed order_details
        $orderIds = (new \yii\db\Query())->select('id')->from('{{%al_orders}}')->column();
        $materialIds = (new \yii\db\Query())->select('id')->from('{{%al_aluminum_materials}}')->column();
        for ($i=1; $i<=10; $i++) {
            $this->insert('{{%al_order_details}}', [
                'order_id' => $orderIds[array_rand($orderIds)],
                'material_id' => $materialIds[array_rand($materialIds)],
                'cut_length' => rand(500,2500),
                'quantity' => rand(1,10),
                'unit_price' => round(150000 + mt_rand(0,200000),0),
                'amount' => round(150000 + mt_rand(0,200000),0),
                'note' => 'Chi tiết mẫu ' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 9️⃣ Seed cut_groups
        for ($i=1; $i<=10; $i++) {
            $this->insert('{{%al_cut_groups}}', [
                'order_id' => $orderIds[array_rand($orderIds)],
                'material_id' => $materialIds[array_rand($materialIds)],
                'cut_length' => rand(1200,2500),
                'quantity' => rand(2,8),
                'waste_length' => rand(20,300),
                'total_used_length' => rand(2000,12000),
                'note' => 'Nhóm cắt mẫu ' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 🔟 Seed scrap_aluminum & reuse_log
        $cutGroupIds = (new \yii\db\Query())->select('id')->from('{{%al_cut_groups}}')->column();
        for ($i=1; $i<=10; $i++) {
            $this->insert('{{%al_scrap_aluminum}}', [
                'cut_group_id' => $cutGroupIds[array_rand($cutGroupIds)],
                'material_id' => $materialIds[array_rand($materialIds)],
                'remaining_length' => rand(50,500),
                'weight' => round(rand(200,1500)/1000,3),
                'is_reused' => rand(0,1),
                'note' => 'Nhôm vụn mẫu ' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $scrapIds = (new \yii\db\Query())->select('id')->from('{{%al_scrap_aluminum}}')->column();
        for ($i=1; $i<=10; $i++) {
            $this->insert('{{%al_reuse_log}}', [
                'scrap_id' => $scrapIds[array_rand($scrapIds)],
                'used_in_cut_group_id' => $cutGroupIds[array_rand($cutGroupIds)],
                'reuse_length' => rand(20,300),
                'quantity' => rand(1,3),
                'note' => 'Tái sử dụng mẫu ' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Bật lại FK
        $this->execute('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');
        foreach ([
            '{{%al_reuse_log}}',
            '{{%al_scrap_aluminum}}',
            '{{%al_cut_groups}}',
            '{{%al_order_details}}',
            '{{%al_orders}}',
            '{{%al_quotations}}',
            '{{%al_pricing_table}}',
            '{{%al_aluminum_materials}}',
            '{{%al_profiles}}',
            '{{%al_systems}}',
            '{{%customers}}',
        ] as $t) {
            $this->truncateTable($t);
        }
        $this->execute('SET FOREIGN_KEY_CHECKS=1;');
    }

}
