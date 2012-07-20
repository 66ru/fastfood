<?php

class m130422_104635_create_environment extends CDbMigration
{
    public function up()
   	{
   		$this->createTable(
   			'Environment',
   			array(
   				'id'=>'pk',
   				'name'=>'varchar(255)',
   				'envName'=>'varchar(255)',
   				'schedule'=>'varchar(255)',
   				'adminMail'=>'varchar(255)',

   			)
   		);
   	}

   	public function down()
   	{
   		$this->dropTable('Environment');
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