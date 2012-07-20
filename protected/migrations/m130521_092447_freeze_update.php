<?php

class m130521_092447_freeze_update extends CDbMigration
{
	public function up()
	{
        $this->addColumn('Nodes','freezeDeploy','tinyint(1) not null default 0');
	}

	public function down()
	{
        $this->dropColumn('Nodes','freezeDeploy');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}