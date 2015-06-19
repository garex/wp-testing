<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddFieldsTables extends BaseMigration
{

    public function up()
    {
        $this->drop_table(WPT_DB_PREFIX . 'field_values');
        $this->drop_table(WPT_DB_PREFIX . 'fields');

        // Fields
        $table = $this->create_table(WPT_DB_PREFIX . 'fields', array(
            'id'      => false,
            'options' => $this->get_table_engine_option(),
        ));
        $table->column('field_id', 'biginteger', array(
            'unsigned'       => true,
            'null'           => false,
            'primary_key'    => true,
            'auto_increment' => true,
        ));
        $table->column('test_id', 'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
        $table->column('field_title', 'text', array(
            'null' => false,
        ));
        $table->column('field_type', 'string', array(
            'null' => false,
        ));
        $table->column('field_is_required', 'boolean', array(
            'null'    => false,
            'default' => 1,
        ));
        $table->column('field_sort', 'integer', array(
            'null'    => false,
            'default' => 100,
        ));
        $table->column('field_clarification', 'text');
        $table->column('field_list_values', 'text');
        $table->column('field_default_value', 'text');
        $table->finish();

        $global_prefix = WP_DB_PREFIX;
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}fields

            ADD CONSTRAINT fk_field_test
            FOREIGN KEY (test_id)
            REFERENCES {$global_prefix}posts (ID)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_field_test (test_id)
        ");

        // Fields values
        $table = $this->create_table(WPT_DB_PREFIX . 'field_values', array(
            'id'      => false,
            'options' => $this->get_table_engine_option(),
        ));
        $table->column('field_value_id', 'biginteger', array(
            'unsigned'       => true,
            'null'           => false,
            'primary_key'    => true,
            'auto_increment' => true,
        ));
        $table->column('field_id',       'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
        $table->column('passing_id',     'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
        $table->column('field_value',    'text');
        $table->finish();

        $this->execute("
            ALTER TABLE {$plugin_prefix}field_values

            ADD CONSTRAINT fk_field_value_field
            FOREIGN KEY (field_id)
            REFERENCES {$plugin_prefix}fields (field_id)
            ON DELETE RESTRICT
            ON UPDATE CASCADE,
            ADD INDEX fk_field_value_field (field_id),

            ADD CONSTRAINT fk_field_value_passing
            FOREIGN KEY (passing_id)
            REFERENCES {$plugin_prefix}passings (passing_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_field_value_passing (passing_id)
        ");
    }

    public function down()
    {
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE TABLE {$plugin_prefix}field_values;
            TRUNCATE TABLE {$plugin_prefix}fields;
            SET FOREIGN_KEY_CHECKS = 1;
        ");
        $this->drop_table($plugin_prefix . 'field_values');
        $this->drop_table($plugin_prefix . 'fields');
    }
}
