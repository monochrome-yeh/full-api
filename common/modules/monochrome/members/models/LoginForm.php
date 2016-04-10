<?php

namespace common\modules\monochrome\members\models;

use \Yii;
use yii\base\Model;
use common\modules\monochrome\members\Members;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $reCAPTCHA; //google reCAPTCHA

    protected $_user = false;

    const STATUS_UNACTIVE = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @return array the validation rules.
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
            ['reCAPTCHA', 'safe'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Members::t('app', 'Account'),
            'password' => Members::t('app', 'Password'),
            'rememberMe' => Members::t('app', 'Remember Me'),        
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
            if (!$user) {
                $this->addError($attribute, Yii::t('common/app', 'Incorrect username or password.'));
            } else {
                if (!$user->validatePassword($this->password)) {
                    $user->login_fail++;

                    if ($user->login_fail <= Yii::$app->getModule('members')->login_fail) {
                        $this->addError($attribute, Yii::t('common/app', 'Incorrect username or password.'));
                    } else {
                        $user->status = self::STATUS_UNACTIVE;
                        $this->send_login_fail_to_much($user);
                    }

                    $this->addError($attribute, Yii::t('common/app', 'Incorrect username or password.'));
                } else {
                    $user->login_fail = 0;
                }

                $user->save(true, ['login_fail', 'status']);
            }
        }
    }

    protected function send_login_fail_to_much($user)
    {
        $ip = Yii::$app->request->getUserIp();
        $info = [];
        $info['userAgent'] = Yii::$app->request->getUserAgent();
        $info['userHost'] = Yii::$app->request->getUserHost();

        Yii::$app->mailer->compose(Members::getEmailTemplate('login_fail_too_much'), [
            'ip' => $ip,
            'info' => $info,
        ])
        ->setFrom(Yii::$app->params['adminEmail'])
        ->setTo([$user->email])
        ->setSubject('帳號嚐試登入多次，已遭鎖定')
        ->send();
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->recaptcha() && $this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? Yii::$app->user->rememberMeTime : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne([
                'status' => self::STATUS_ACTIVE,
                'login.normal.account' => $this->username
            ]);
        }

        return $this->_user;
    }

    private function recaptcha()
    {
        if (Yii::$app->getModule('members')->google['recaptcha']['enable']) {
            if (!empty($_POST['g-recaptcha-response'])) {
                $recaptcha = $_POST['g-recaptcha-response'];
                $google_url = "https://www.google.com/recaptcha/api/siteverify";
                $secret = Yii::$app->getModule('members')->google['recaptcha']['secret'];
                $ip = $_SERVER['REMOTE_ADDR'];
                $url = $google_url."?secret=".$secret."&response=".$recaptcha."&remoteip=".$ip;
                $res = $this->getCurlData($url);
                $res = json_decode($res, true);
                // reCaptcha success check
                if ($res['success']) {
                    return true;
                }
            }
            $this->addError('reCAPTCHA', Yii::t('common/app', 'Please re-enter your reCAPTCHA.'));
            return false;
        }

        return true;              
    }

    private function getCurlData($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        //curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
        $curlData = curl_exec($curl);
        curl_close($curl);
        return $curlData;
    }    
}
