<?php

class LoginForm extends CFormModel
{
    public $email;
    public $apartment;
    public $houseId;
    public $password;
    public $rememberMe;

    /**
     * @var UserIdentity
     */
    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            array('email, password', 'required', 'on'=>'login,register'),
            array('email', 'email'),
            array('apartment', 'safe'),
            array('email', 'uniqueEmail', 'on'=>'register'),
            array('houseId', 'required', 'on'=>'register,settings'),
            array('rememberMe', 'boolean'),
            array('password', 'authenticate', 'on'=>'login'),
        );
    }

    public function uniqueEmail($attribute,$params)
    {
        $user = User::model();
        $user->email = $this->$attribute;
        $items = $user->search();
        if ($items->itemCount)
            $this->addError($attribute,'Такой email уже зарегистрирован');
    }

    public function attributeLabels()
    {
        return array(
            'email'=>'E-mail',
            'password'=>'Пароль',
            'rememberMe'=>'Запомнить меня',
            'houseId'=>'Дом',
            'apartment'=>'Квартира',
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute,$params)
    {
        if(!$this->hasErrors())
        {
            $this->_identity=new UserIdentity($this->email,$this->password);
            if(!$this->_identity->authenticate())
                $this->addError('password','Неверная комбинация email/пароль.');
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login()
    {
        if($this->_identity===null)
        {
            $this->_identity=new UserIdentity($this->email,$this->password);
            $this->_identity->authenticate();
        }
        if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
        {
            $duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
            return true;
        }
        else
            return false;
    }
}
