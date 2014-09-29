<?php

class LoginModel
{
    private $actualUsername;
    private $actualPassword;
    private $sessionID;
    private $sessionUser;
    private $sessionUserIP;
    private $sessionUserAgent;

    public function __construct()
    {
        $this->actualUsername = "Admin";
        $this->actualPassword = "Password";
        $this->sessionID = "isloggedin";
        $this->sessionUser = "sessionuser";
        $this->sessionUserIP = "sessionuserip";
        $this->sessionUserAgent = "sessionuseragent";
    }

    //Generates new session ID, minor session hijack protection
    public function refreshSession()
    {
        session_regenerate_id();
    }


    public function isLoggedIn($userIP, $userAgent)
    {
        if(isset($_SESSION[$this->sessionID]) && $_SESSION[$this->sessionID] === true
        && $_SESSION[$this->sessionUser] === $this->actualUsername
        && $_SESSION[$this->sessionUserIP] === $userIP
        && $_SESSION[$this->sessionUserAgent] === $userAgent)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getSessionUsername()
    {
        return $_SESSION[$this->sessionUser];
    }

    public function logout()
    {
        unset($_SESSION[$this->sessionID]);
        unset($_SESSION[$this->sessionUser]);
        session_destroy();
    }

    public function setSession($username, $userIP, $userAgent)
    {
        $_SESSION[$this->sessionID] = true;
        $_SESSION[$this->sessionUser] = $username;
        $_SESSION[$this->sessionUserIP] = $userIP;
        $_SESSION[$this->sessionUserAgent] = $userAgent;
    }

    public function login($username, $password, $userIP, $userAgent)
    {
        if($username == $this->actualUsername && $password == $this->actualPassword)
        {
            $this->setSession($username, $userIP, $userAgent);

            return true;
        }
        else
        {
            return false;
        }
    }

    public function loginWithCookies($usernameCookie, $tokenPassCookie, $userIP, $userAgent)
    {
        if($usernameCookie == $this->actualUsername && $tokenPassCookie == $this->getTokenPass()
        && $this->getCookieExpiration() > time())
        {
            $this->setSession($usernameCookie, $userIP, $userAgent);

            return true;
        }
        else
        {
            return false;
        }
    }

    public function saveCookieExpiration($time)
    {
        file_put_contents("CookieExpiration", $time);
    }

    public function getCookieExpiration()
    {
        return file_get_contents("CookieExpiration");
    }

    //send tokenpass from file back
    public function getTokenPass()
    {
        return file_get_contents("CookieToken");
    }

    //create random tokenpass instead of password in cookie, save to file, send back.
    public function createTokenPass()
    {
        $token = sha1(rand().microtime());

        file_put_contents("CookieToken", $token);

        return $token;
    }

    public function checkValidRegistrationData($userDetails){
        $username = $userDetails["username"];
        $password = $userDetails["password"];
        $rpassword = $userDetails["repeatpassword"];
        $arrWithErrorMessages = array();

        if(strlen($username) < 3){
            array_push($arrWithErrorMessages, "Användarnamnet har för få tecken. Minst 3 tecken");
        }else if(true){

        }

        if(strlen($password) < 6){
            array_push($arrWithErrorMessages, "Lösenordet har för få tecken. Minst 6 tecken");
        }else if($password !== $rpassword){

            array_push($arrWithErrorMessages, "Lösenorden matchar inte");

        }

        if(count($arrWithErrorMessages) == 0){
            return true;
        }else{

            return $arrWithErrorMessages;
        }

    }

}