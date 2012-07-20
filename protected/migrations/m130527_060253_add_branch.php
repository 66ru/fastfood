<?php

class m130527_060253_add_branch extends CDbMigration
{
	public function up()
	{
        $this->addColumn('Nodes','branch','varchar(255)');
	}

	public function down()
	{
        $this->dropColumn('Nodes','branch');
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