<?php

class m121127_073605_create_settings extends CDbMigration
{
	public function up()
	{
		$this->createTable('Settings', array(
			'id' => 'pk',
			'name' => 'varchar(255) NOT NULL',
			'description' => 'text',
			'type' => 'int(1) NOT NULL',
			'deletable' => 'int(1) NOT NULL DEFAULT 1',
			'value' => 'text',
		));
	}

	public function down()
	{
		$this->dropTable('Settings');
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