<?php

class WpTesting_Migration_CleanUpExperimentalMigrations extends WpTesting_Migration_Base
{
    public function up()
    {
        $this->execute('
            DELETE FROM ' . RUCKUSING_TS_SCHEMA_TBL_NAME . '
            WHERE version IN (
                20140831002425,
                20140831142817,
                20140831155940,
                20140913080127
            )
        ');
    }

    public function down()
    {
        // nothing here as it's cleanup of something that should not be here :(
    }
}
