<?php

class m130423_084204_create_nodes extends CDbMigration
{
    public function up()
   	{
   		$this->createTable(
   			'Nodes',
   			array(
   				'id'=>'pk',
   				'hostname'=>'varchar(255)',
   				'envId'=>'int(11)',
   				'role'=>'varchar(255)',
   				'lastUpdate'=>'int(11)',
                'needDeploy'=>'int(1)',
   			)
   		);
   	}

   	public function down()
   	{
   		$this->dropTable('Nodes');
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