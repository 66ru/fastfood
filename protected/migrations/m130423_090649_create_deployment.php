<?php

class m130423_090649_create_deployment extends CDbMigration
{
    public function up()
   	{
   		$this->createTable(
   			'Deployment',
   			array(
   				'id'=>'pk',
   				'nodeId'=>'varchar(255)',
   				'timeStarted'=>'int(11)',
   				'timeFinished'=>'int(11)',
   			)
   		);
   	}

   	public function down()
   	{
   		$this->dropTable('Deployment');
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