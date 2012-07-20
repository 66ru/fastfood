<?php

class m121111_160500_create_static_images extends CDbMigration
{
    public function up()
   	{
           $this->createTable('static_images', array(
               'id' => 'pk',
               'uid' => 'string NOT NULL',
           ));
   	}

   	public function down()
   	{
           $this->dropTable('static_images');
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