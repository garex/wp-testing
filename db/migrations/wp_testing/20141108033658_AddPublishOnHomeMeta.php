<?php

class AddPublishOnHomeMeta extends BaseMigration
{
    public function up()
    {
        $this->add_meta('wpt_publish_on_home', 1);
    }

    public function down()
    {
        $this->remove_meta('wpt_publish_on_home');
    }
}
