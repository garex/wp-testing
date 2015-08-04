<?php

class AddSectionDescription extends Ruckusing_Migration_Base
{
    public function up()
    {
        $this->add_column(WPT_DB_PREFIX . 'sections', 'section_description', 'mediumtext');
    }

    public function down()
    {
        $this->remove_column(WPT_DB_PREFIX . 'sections', 'section_description');
    }
}
