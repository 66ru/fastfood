<?php

class m130624_101003_settings extends CDbMigration
{
	public function up()
	{
        $this->execute("
                INSERT INTO `Settings` (`name`, `description`, `type`, `deletable`, `value`) VALUES
                        ('node_timeout', 'Таймаут ноды в секундах, после которого слать уведомление о проблеме', 1, 0, '900')
        ");
	}

	public function down()
	{
        $this->execute("
                DELETE FROM `Settings` WHERE `name` in ('node_timeout')
        ");
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