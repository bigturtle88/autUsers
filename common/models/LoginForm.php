<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{

    const BRUTEFORCE_COUNT = 3;
    const BRUTEFORCE_TIME_PERIOD =  120;
    const BRUTEFORCE_TIME_BAN = 300;

    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;
    private $time_ban;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {

                $this->writeAntiBruteforceCount();

                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {


            return false;
        }
    }

    /**
 * Finds user by [[username]]
 *
 * @return User|null
 */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Increase count
     *
     * @return integer
     */
    protected function writeAntiBruteforceCount()
    {
        $user = $this->getUser();

        if($user->anti_bruteforce_time + self::BRUTEFORCE_TIME_PERIOD > time()) {

            $user->anti_bruteforce_count = $user->anti_bruteforce_count + 1;
        }
        else{

            $this->writeAntiBruteforceTime();
            $user->anti_bruteforce_count = 1;

        }
        $user->save();

        return  $user->anti_bruteforce_count;
    }
    /**
     * Increase time
     *
     * @return integer
     */
    protected function writeAntiBruteforceTime()
    {
        $user = $this->getUser();

        $user->anti_bruteforce_time = time() ;
        $user->save();

        return  $user->anti_bruteforce_time;
    }
    /**
     * Brute force filter
     *
     * @return true|false
     */
    public function antiBruteforceFilter()
    {
        $user = $this->getUser();

        if( $user->anti_bruteforce_count === self::BRUTEFORCE_COUNT){

          if ( $user->anti_bruteforce_time + self::BRUTEFORCE_TIME_BAN > time() ){

              $this->time_ban =  ($user->anti_bruteforce_time + self::BRUTEFORCE_TIME_BAN) - time();

              return $this->time_ban;

          }
            else {

                $user->anti_bruteforce_count = 0;
                $user->anti_bruteforce_time = 0;
                $user->save();
            }
       }
        return  true;
    }

}
