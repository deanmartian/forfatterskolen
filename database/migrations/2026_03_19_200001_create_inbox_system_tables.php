<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (!Schema::hasTable('inbox_conversations')) {
            DB::statement("CREATE TABLE inbox_conversations (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                subject varchar(500) DEFAULT NULL,
                customer_email varchar(255) NOT NULL,
                customer_name varchar(255) DEFAULT NULL,
                user_id int(10) unsigned DEFAULT NULL,
                assigned_to int(10) unsigned DEFAULT NULL,
                status varchar(20) NOT NULL DEFAULT 'open',
                priority varchar(20) NOT NULL DEFAULT 'normal',
                category varchar(100) DEFAULT NULL,
                tags longtext DEFAULT NULL,
                inbox varchar(100) DEFAULT 'post@forfatterskolen.no',
                source varchar(50) NOT NULL DEFAULT 'email',
                helpwise_id varchar(255) DEFAULT NULL,
                external_id varchar(255) DEFAULT NULL,
                is_spam tinyint(1) NOT NULL DEFAULT 0,
                is_starred tinyint(1) NOT NULL DEFAULT 0,
                snoozed_until timestamp NULL DEFAULT NULL,
                first_response_at timestamp NULL DEFAULT NULL,
                resolved_at timestamp NULL DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY inbox_conversations_customer_email_index (customer_email),
                KEY inbox_conversations_user_id_index (user_id),
                KEY inbox_conversations_assigned_to_index (assigned_to),
                KEY inbox_conversations_status_index (status),
                KEY inbox_conversations_helpwise_id_index (helpwise_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('inbox_messages')) {
            DB::statement("CREATE TABLE inbox_messages (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                conversation_id int(10) unsigned NOT NULL,
                type varchar(20) NOT NULL DEFAULT 'reply',
                direction varchar(20) NOT NULL DEFAULT 'inbound',
                from_email varchar(255) DEFAULT NULL,
                from_name varchar(255) DEFAULT NULL,
                to_email varchar(255) DEFAULT NULL,
                cc text DEFAULT NULL,
                bcc text DEFAULT NULL,
                subject varchar(500) DEFAULT NULL,
                body longtext DEFAULT NULL,
                body_plain text DEFAULT NULL,
                body_html longtext DEFAULT NULL,
                sent_by_user_id int(10) unsigned DEFAULT NULL,
                is_ai_draft tinyint(1) NOT NULL DEFAULT 0,
                is_draft tinyint(1) NOT NULL DEFAULT 0,
                ai_confidence decimal(3,2) DEFAULT NULL,
                message_id_header varchar(500) DEFAULT NULL,
                in_reply_to varchar(500) DEFAULT NULL,
                attachments longtext DEFAULT NULL,
                metadata longtext DEFAULT NULL,
                sent_at timestamp NULL DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY inbox_messages_conversation_id_index (conversation_id),
                KEY inbox_messages_type_index (type),
                KEY inbox_messages_direction_index (direction)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('inbox_comments')) {
            DB::statement("CREATE TABLE inbox_comments (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                conversation_id int(10) unsigned NOT NULL,
                user_id int(10) unsigned NOT NULL,
                body text NOT NULL,
                mentioned_user_ids longtext DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY inbox_comments_conversation_id_index (conversation_id),
                KEY inbox_comments_user_id_index (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('inbox_assignments')) {
            DB::statement("CREATE TABLE inbox_assignments (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                conversation_id int(10) unsigned NOT NULL,
                assigned_by int(10) unsigned DEFAULT NULL,
                assigned_to int(10) unsigned NOT NULL,
                note text DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY inbox_assignments_conversation_id_index (conversation_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('inbox_auto_replies')) {
            DB::statement("CREATE TABLE inbox_auto_replies (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                trigger_type varchar(50) NOT NULL DEFAULT 'new_conversation',
                trigger_conditions longtext DEFAULT NULL,
                reply_template text NOT NULL,
                is_active tinyint(1) NOT NULL DEFAULT 1,
                use_ai tinyint(1) NOT NULL DEFAULT 0,
                inbox varchar(100) DEFAULT NULL,
                send_delay_minutes int NOT NULL DEFAULT 0,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('inbox_canned_responses')) {
            DB::statement("CREATE TABLE inbox_canned_responses (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                title varchar(255) NOT NULL,
                shortcut varchar(100) DEFAULT NULL,
                body text NOT NULL,
                category varchar(100) DEFAULT NULL,
                created_by int(10) unsigned DEFAULT NULL,
                usage_count int NOT NULL DEFAULT 0,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('inbox_canned_responses');
        Schema::dropIfExists('inbox_auto_replies');
        Schema::dropIfExists('inbox_assignments');
        Schema::dropIfExists('inbox_comments');
        Schema::dropIfExists('inbox_messages');
        Schema::dropIfExists('inbox_conversations');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
