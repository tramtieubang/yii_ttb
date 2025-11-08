<?php

use yii\db\Migration;

/**
 * Tạo các bảng phục vụ chat nội bộ:
 * - chat_rooms
 * - chat_room_members
 * - chat_messages
 */
class m251106_032824_create_table_chats extends Migration
{
    public function safeUp()
    {
        /** -----------------------------
         *  BẢNG PHÒNG CHAT
         * ----------------------------- */
        $this->createTable('{{%chat_rooms}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Tên phòng hoặc người chat'),
            'is_group' => $this->boolean()->defaultValue(false)->comment('Có phải nhóm hay không'),
            'created_by' => $this->integer()->notNull()->comment('Người tạo phòng'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Thời gian tạo'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Phòng chat"');

        $this->createIndex('idx_chat_rooms_created_by', '{{%chat_rooms}}', 'created_by');

        if ($this->db->schema->getTableSchema('{{%user}}', true) !== null) {
            $this->addForeignKey(
                'fk_chat_rooms_user',
                '{{%chat_rooms}}',
                'created_by',
                '{{%user}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }

        /** -----------------------------
         *  BẢNG THÀNH VIÊN PHÒNG
         * ----------------------------- */
        $this->createTable('{{%chat_room_members}}', [
            'room_id' => $this->integer()->notNull()->comment('Phòng chat'),
            'user_id' => $this->integer()->notNull()->comment('Thành viên'),
            'joined_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Thời gian tham gia'),
            'PRIMARY KEY(room_id, user_id)',
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Thành viên chat"');

        $this->addForeignKey(
            'fk_chat_room_members_room',
            '{{%chat_room_members}}',
            'room_id',
            '{{%chat_rooms}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        if ($this->db->schema->getTableSchema('{{%user}}', true) !== null) {
            $this->addForeignKey(
                'fk_chat_room_members_user',
                '{{%chat_room_members}}',
                'user_id',
                '{{%user}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }

        /** -----------------------------
         *  BẢNG TIN NHẮN
         * ----------------------------- */
        
        $this->createTable('{{%chat_messages}}', [
            'id' => $this->primaryKey(),
            'room_id' => $this->integer()->notNull()->comment('ID phòng chat'),
            'sender_id' => $this->integer()->notNull()->comment('ID người gửi tin nhắn'),
            'message' => $this->text()->null()->comment('Nội dung tin nhắn (văn bản)'),
            'file_path' => $this->string(255)->null()->comment('Đường dẫn tệp đính kèm'),
            'original_name' => $this->string(255)->null()->comment('Tên tệp gốc'),
            'file_type' => $this->string(50)->null()->comment('Loại tệp đính kèm (ảnh, video, file, v.v.)'),
            'is_read' => $this->tinyInteger()->defaultValue(0)->comment('Trạng thái tin nhắn: 0=Chưa đọc, 1=Đã đọc'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('Thời gian gửi tin nhắn'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT="Bảng lưu nội dung tin nhắn trong phòng chat"');

        $this->createIndex('idx_chat_messages_room', '{{%chat_messages}}', 'room_id');
        $this->createIndex('idx_chat_messages_sender', '{{%chat_messages}}', 'sender_id');

        $this->addForeignKey(
            'fk_chat_messages_room',
            '{{%chat_messages}}',
            'room_id',
            '{{%chat_rooms}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        if ($this->db->schema->getTableSchema('{{%user}}', true) !== null) {
            $this->addForeignKey(
                'fk_chat_messages_sender',
                '{{%chat_messages}}',
                'sender_id',
                '{{%user}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }
    }

    public function safeDown()
    {
        // Xóa theo thứ tự ngược để tránh lỗi khóa ngoại
        if ($this->db->schema->getTableSchema('{{%user}}', true) !== null) {
            $this->dropForeignKey('fk_chat_messages_sender', '{{%chat_messages}}');
            $this->dropForeignKey('fk_chat_room_members_user', '{{%chat_room_members}}');
            $this->dropForeignKey('fk_chat_rooms_user', '{{%chat_rooms}}');
        }

        $this->dropForeignKey('fk_chat_messages_room', '{{%chat_messages}}');
        $this->dropForeignKey('fk_chat_room_members_room', '{{%chat_room_members}}');

        $this->dropTable('{{%chat_messages}}');
        $this->dropTable('{{%chat_room_members}}');
        $this->dropTable('{{%chat_rooms}}');
    }
}
