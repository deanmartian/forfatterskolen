<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to avoid MariaDB ghost foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (!Schema::hasTable('ad_campaigns')) {
            DB::statement("CREATE TABLE ad_campaigns (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                account_id int(10) unsigned NOT NULL,
                platform varchar(30) NOT NULL DEFAULT 'facebook',
                external_id varchar(255) DEFAULT NULL,
                name varchar(255) NOT NULL,
                objective varchar(50) NOT NULL DEFAULT 'leads',
                status varchar(50) NOT NULL DEFAULT 'draft',
                daily_budget decimal(10,2) DEFAULT NULL,
                total_budget decimal(12,2) DEFAULT NULL,
                spent_total decimal(12,2) NOT NULL DEFAULT 0,
                automation_level varchar(30) DEFAULT NULL,
                landing_page varchar(255) DEFAULT NULL,
                product_reference varchar(255) DEFAULT NULL,
                targeting longtext DEFAULT NULL,
                tracking longtext DEFAULT NULL,
                platform_meta longtext DEFAULT NULL,
                ai_brief text DEFAULT NULL,
                ai_notes text DEFAULT NULL,
                created_by int(10) unsigned DEFAULT NULL,
                published_at timestamp NULL DEFAULT NULL,
                paused_at timestamp NULL DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_campaigns_account_id_index (account_id),
                KEY ad_campaigns_external_id_index (external_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_ad_sets')) {
            DB::statement("CREATE TABLE ad_ad_sets (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                campaign_id int(10) unsigned NOT NULL,
                external_id varchar(255) DEFAULT NULL,
                name varchar(255) NOT NULL,
                status varchar(50) NOT NULL DEFAULT 'draft',
                daily_budget decimal(10,2) DEFAULT NULL,
                targeting longtext DEFAULT NULL,
                platform_meta longtext DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_ad_sets_campaign_id_index (campaign_id),
                KEY ad_ad_sets_external_id_index (external_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_creatives')) {
            DB::statement("CREATE TABLE ad_creatives (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                name varchar(255) DEFAULT NULL,
                platform varchar(30) NOT NULL DEFAULT 'universal',
                headlines longtext DEFAULT NULL,
                descriptions longtext DEFAULT NULL,
                primary_text text DEFAULT NULL,
                cta varchar(255) DEFAULT NULL,
                display_url varchar(255) DEFAULT NULL,
                final_url varchar(255) DEFAULT NULL,
                asset_ids longtext DEFAULT NULL,
                variant_of int(10) unsigned DEFAULT NULL,
                generation int NOT NULL DEFAULT 1,
                performance_score decimal(5,2) DEFAULT NULL,
                status varchar(30) NOT NULL DEFAULT 'draft',
                ai_metadata longtext DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_creatives_variant_of_index (variant_of)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_ads')) {
            DB::statement("CREATE TABLE ad_ads (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                ad_set_id int(10) unsigned NOT NULL,
                creative_id int(10) unsigned DEFAULT NULL,
                external_id varchar(255) DEFAULT NULL,
                name varchar(255) NOT NULL,
                status varchar(50) NOT NULL DEFAULT 'draft',
                platform_meta longtext DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_ads_ad_set_id_index (ad_set_id),
                KEY ad_ads_creative_id_index (creative_id),
                KEY ad_ads_external_id_index (external_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_creative_variants')) {
            DB::statement("CREATE TABLE ad_creative_variants (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                creative_id int(10) unsigned NOT NULL,
                parent_id int(10) unsigned DEFAULT NULL,
                experiment_id int(10) unsigned DEFAULT NULL,
                generation int NOT NULL DEFAULT 1,
                variant_label varchar(255) DEFAULT NULL,
                performance_score decimal(5,2) DEFAULT NULL,
                metrics_snapshot longtext DEFAULT NULL,
                status varchar(30) NOT NULL DEFAULT 'testing',
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_creative_variants_creative_id_index (creative_id),
                KEY ad_creative_variants_parent_id_index (parent_id),
                KEY ad_creative_variants_experiment_id_index (experiment_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        // Operations tables from migration 000003
        if (!Schema::hasTable('ad_metric_snapshots')) {
            DB::statement("CREATE TABLE ad_metric_snapshots (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                level varchar(20) NOT NULL,
                reference_id int(10) unsigned NOT NULL,
                campaign_id int(10) unsigned DEFAULT NULL,
                date date NOT NULL,
                impressions int NOT NULL DEFAULT 0,
                clicks int NOT NULL DEFAULT 0,
                spend decimal(10,2) NOT NULL DEFAULT 0,
                conversions int NOT NULL DEFAULT 0,
                cpa decimal(10,2) DEFAULT NULL,
                roas decimal(8,2) DEFAULT NULL,
                ctr decimal(6,4) DEFAULT NULL,
                cpc decimal(8,2) DEFAULT NULL,
                cpm decimal(8,2) DEFAULT NULL,
                leads int NOT NULL DEFAULT 0,
                revenue decimal(12,2) NOT NULL DEFAULT 0,
                platform_data longtext DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_metric_snapshots_level_ref_date (level, reference_id, date),
                KEY ad_metric_snapshots_campaign_id_index (campaign_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_rules')) {
            DB::statement("CREATE TABLE ad_rules (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                metric varchar(255) NOT NULL,
                operator varchar(5) NOT NULL,
                threshold decimal(12,4) NOT NULL,
                min_data_points int NOT NULL DEFAULT 0,
                min_spend_threshold decimal(10,2) DEFAULT NULL,
                evaluation_window_days int NOT NULL DEFAULT 7,
                action varchar(255) NOT NULL,
                risk_level varchar(20) NOT NULL DEFAULT 'medium',
                auto_apply tinyint(1) NOT NULL DEFAULT 0,
                scope varchar(20) NOT NULL DEFAULT 'campaign',
                campaign_id int(10) unsigned DEFAULT NULL,
                priority int NOT NULL DEFAULT 50,
                is_active tinyint(1) NOT NULL DEFAULT 1,
                description text DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_rule_runs')) {
            DB::statement("CREATE TABLE ad_rule_runs (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                rule_id int(10) unsigned NOT NULL,
                triggered tinyint(1) NOT NULL DEFAULT 0,
                target_type varchar(255) DEFAULT NULL,
                target_id int(10) unsigned DEFAULT NULL,
                actual_value decimal(12,4) DEFAULT NULL,
                result varchar(255) DEFAULT NULL,
                details longtext DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_rule_runs_rule_id_index (rule_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_ai_decisions')) {
            DB::statement("CREATE TABLE ad_ai_decisions (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                decision_type varchar(255) NOT NULL,
                confidence decimal(3,2) DEFAULT NULL,
                reasoning_summary text NOT NULL,
                risk_level varchar(20) NOT NULL DEFAULT 'medium',
                requires_approval tinyint(1) NOT NULL DEFAULT 1,
                proposed_action longtext NOT NULL,
                context_data longtext DEFAULT NULL,
                status varchar(30) NOT NULL DEFAULT 'pending',
                campaign_id int(10) unsigned DEFAULT NULL,
                rule_id int(10) unsigned DEFAULT NULL,
                executed_at timestamp NULL DEFAULT NULL,
                execution_result longtext DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_ai_decisions_campaign_id_index (campaign_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_approval_requests')) {
            DB::statement("CREATE TABLE ad_approval_requests (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                decision_id int(10) unsigned NOT NULL,
                action_payload longtext NOT NULL,
                ai_summary text NOT NULL,
                status varchar(30) NOT NULL DEFAULT 'pending',
                approved_by int(10) unsigned DEFAULT NULL,
                approved_at timestamp NULL DEFAULT NULL,
                reviewer_notes text DEFAULT NULL,
                execution_result longtext DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_approval_requests_decision_id_index (decision_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_action_logs')) {
            DB::statement("CREATE TABLE ad_action_logs (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                action_type varchar(255) NOT NULL,
                target_type varchar(255) DEFAULT NULL,
                target_id int(10) unsigned DEFAULT NULL,
                payload longtext DEFAULT NULL,
                result longtext DEFAULT NULL,
                triggered_by varchar(20) NOT NULL DEFAULT 'system',
                user_id int(10) unsigned DEFAULT NULL,
                rule_id int(10) unsigned DEFAULT NULL,
                decision_id int(10) unsigned DEFAULT NULL,
                status varchar(20) NOT NULL DEFAULT 'success',
                error_message text DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_action_logs_target_index (target_type, target_id),
                KEY ad_action_logs_action_type_index (action_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_experiments')) {
            DB::statement("CREATE TABLE ad_experiments (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                hypothesis text DEFAULT NULL,
                campaign_id int(10) unsigned DEFAULT NULL,
                status varchar(30) NOT NULL DEFAULT 'draft',
                winner_variant_id int(10) unsigned DEFAULT NULL,
                started_at date DEFAULT NULL,
                ended_at date DEFAULT NULL,
                results_summary longtext DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_experiments_campaign_id_index (campaign_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_experiment_variants')) {
            DB::statement("CREATE TABLE ad_experiment_variants (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                experiment_id int(10) unsigned NOT NULL,
                creative_id int(10) unsigned DEFAULT NULL,
                ad_id int(10) unsigned DEFAULT NULL,
                label varchar(255) DEFAULT NULL,
                metrics longtext DEFAULT NULL,
                is_winner tinyint(1) NOT NULL DEFAULT 0,
                is_control tinyint(1) NOT NULL DEFAULT 0,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_experiment_variants_experiment_id_index (experiment_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_asset_links')) {
            DB::statement("CREATE TABLE ad_asset_links (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                asset_path varchar(255) NOT NULL,
                asset_type varchar(30) NOT NULL DEFAULT 'image',
                tags longtext DEFAULT NULL,
                product_reference varchar(255) DEFAULT NULL,
                campaign_id int(10) unsigned DEFAULT NULL,
                creative_id int(10) unsigned DEFAULT NULL,
                performance_score decimal(5,2) DEFAULT NULL,
                relevance_score decimal(5,2) DEFAULT NULL,
                metadata longtext DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('ad_sync_runs')) {
            DB::statement("CREATE TABLE ad_sync_runs (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                account_id int(10) unsigned DEFAULT NULL,
                platform varchar(30) NOT NULL,
                type varchar(30) NOT NULL,
                status varchar(20) NOT NULL DEFAULT 'running',
                records_synced int NOT NULL DEFAULT 0,
                details longtext DEFAULT NULL,
                error_message text DEFAULT NULL,
                started_at timestamp NULL DEFAULT NULL,
                completed_at timestamp NULL DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY ad_sync_runs_account_id_index (account_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        // Helpwise tables
        if (!Schema::hasTable('helpwise_conversations')) {
            DB::statement("CREATE TABLE helpwise_conversations (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                helpwise_id varchar(255) NOT NULL,
                inbox varchar(255) DEFAULT NULL,
                inbox_id varchar(255) DEFAULT NULL,
                subject varchar(255) DEFAULT NULL,
                customer_email varchar(255) DEFAULT NULL,
                customer_name varchar(255) DEFAULT NULL,
                user_id int(10) unsigned DEFAULT NULL,
                status varchar(20) NOT NULL DEFAULT 'open',
                assigned_to varchar(255) DEFAULT NULL,
                tags longtext DEFAULT NULL,
                raw_payload longtext DEFAULT NULL,
                helpwise_created_at timestamp NULL DEFAULT NULL,
                helpwise_closed_at timestamp NULL DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY helpwise_conversations_helpwise_id_unique (helpwise_id),
                KEY helpwise_conversations_customer_email_index (customer_email),
                KEY helpwise_conversations_user_id_index (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('helpwise_messages')) {
            DB::statement("CREATE TABLE helpwise_messages (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                helpwise_message_id varchar(255) DEFAULT NULL,
                conversation_id int(10) unsigned NOT NULL,
                direction varchar(20) NOT NULL DEFAULT 'inbound',
                from_email varchar(255) DEFAULT NULL,
                from_name varchar(255) DEFAULT NULL,
                to_email varchar(255) DEFAULT NULL,
                subject text DEFAULT NULL,
                body longtext DEFAULT NULL,
                body_plain text DEFAULT NULL,
                attachments longtext DEFAULT NULL,
                channel varchar(255) DEFAULT NULL,
                raw_payload longtext DEFAULT NULL,
                message_at timestamp NULL DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY helpwise_messages_helpwise_message_id_index (helpwise_message_id),
                KEY helpwise_messages_conversation_id_index (conversation_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!Schema::hasTable('helpwise_webhook_logs')) {
            DB::statement("CREATE TABLE helpwise_webhook_logs (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                event_type varchar(255) NOT NULL,
                payload longtext NOT NULL,
                status varchar(20) NOT NULL DEFAULT 'received',
                error_message text DEFAULT NULL,
                ip_address varchar(255) DEFAULT NULL,
                created_at timestamp NULL DEFAULT NULL,
                updated_at timestamp NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY helpwise_webhook_logs_event_type_index (event_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Handled by other migrations
    }
};
