<?php

class m121215_110935_db_sessions extends CDbMigration
{
	public function up()
	{
		$this->createTable(
			'yii_session',
			array(
				'id'=>'char(32)',
				'expire'=>'integer',
				'data'=>'text'
			)
		);
	}

	public function down()
	{
		$this->dropTable('yii_session');
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