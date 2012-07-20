<?php
class Formatter extends CFormatter {
	public $datetimeFormat='d.m.Y H:i:s';

    public function formatBoolean($value){

            return $value ? 'Да' : 'Нет';
    }
}
?>