<?php
Yii::import('lib.redactorJS.widgets.redactorjs.Redactor');

class RedactorJSBoot extends Redactor
{
    public $attributeName;
    public $form;
    public $lang = 'ru';
	public $label;

    public function run()
    {
        $this->attribute = $this->attributeName;
		$this->label = $this->form->labelEx($this->model, $this->attribute);
		echo CHtml::label($this->label, $this->attributeName);
        parent::run();
    }
}
