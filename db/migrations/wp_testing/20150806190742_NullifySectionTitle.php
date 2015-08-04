<?php

class NullifySectionTitle extends Ruckusing_Migration_Base
{

    public function up()
    {
        $this->nullify_section_title(true);
    }

    public function down()
    {
        $this->nullify_section_title(false);
    }

    private function nullify_section_title($to)
    {
        $this->change_column(WPT_DB_PREFIX . 'sections', 'section_title', 'text', array(
            'null' => $to
        ));
    }
}
