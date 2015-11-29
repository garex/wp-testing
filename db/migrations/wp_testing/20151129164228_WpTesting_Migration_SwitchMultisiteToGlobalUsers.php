<?php

class WpTesting_Migration_SwitchMultisiteToGlobalUsers extends WpTesting_Migration_Base
{

    public function up()
    {
        if ($this->isSkip()) {
            return;
        }

        $this->switchForeignKey("{$this->globalPrefix}users");
        $this->execute("DROP TABLE IF EXISTS {$this->blogPrefix}users");
    }

    public function down()
    {
        if ($this->isSkip()) {
            return;
        }

        $this->executeSafely("CREATE TABLE {$this->blogPrefix}users LIKE {$this->globalPrefix}users");
        $this->switchForeignKey("{$this->blogPrefix}users");
    }

    /**
     * @return boolean
     */
    private function isSkip()
    {
        return ($this->blogPrefix == $this->globalPrefix);
    }

    private function switchForeignKey($targetTable)
    {
        $this->executeSafely("
            ALTER TABLE {$this->pluginPrefix}passings
                DROP FOREIGN KEY {$this->pluginPrefix}fk_passing_respondent;
            ALTER TABLE {$this->pluginPrefix}passings
                ADD CONSTRAINT {$this->pluginPrefix}fk_passing_respondent
                FOREIGN KEY (respondent_id)
                REFERENCES $targetTable (ID)
                ON DELETE CASCADE
                ON UPDATE CASCADE;
        ");
    }
}
