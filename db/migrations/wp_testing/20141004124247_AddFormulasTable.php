<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_BaseMigration.php';

class AddFormulasTable extends BaseMigration
{
    public function up()
    {
        $table = $this->create_table(WPT_DB_PREFIX . 'formulas', array(
            'id'      => false,
            'options' => $this->get_table_engine_option(),
        ));
        $table->column('formula_id',        'biginteger', array(
            'unsigned'       => true,
            'null'           => false,
            'primary_key'    => true,
            'auto_increment' => true,
        ));
        $table->column('test_id',           'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
        $table->column('result_id',         'biginteger', array(
            'unsigned' => true,
            'null'     => false,
        ));
        $table->column('formula_source',    'text',   array(
            'null' => false,
        ));
        $table->finish();

        $global_prefix = WP_DB_PREFIX;
        $plugin_prefix = WPT_DB_PREFIX;
        $this->execute("
            ALTER TABLE {$plugin_prefix}formulas

            ADD CONSTRAINT {$plugin_prefix}fk_formula_test
            FOREIGN KEY (test_id)
            REFERENCES {$global_prefix}posts (ID)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_formula_test (test_id),

            ADD CONSTRAINT {$plugin_prefix}fk_formula_result
            FOREIGN KEY (result_id)
            REFERENCES {$global_prefix}terms (term_id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
            ADD INDEX fk_formula_result (result_id),

            ADD UNIQUE INDEX uq_formula_test_result (test_id, result_id)
        ");
    }

    public function down()
    {
        $this->drop_table(WPT_DB_PREFIX . 'formulas');
    }
}
