<?php

class LoginModel
{
    private $actualUsername;
    private $actualPassword;
    private $sessionID;
    private $sessionUser;


    public function __construct()
    {
        $this->actualUsername = "Admin";
        $this->actualPassword = "Password";
        $this->sessionID = "isloggedin";
        $this->sessionUser = "sessionuser";
    }

    public function isLoggedIn()
    {
        if(isset($_SESSION[$this->sessionID]) && $_SESSION[$this->sessionID] === true)
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
        session_destroy();
    }

    public function login($username, $password)
    {
        if($username == $this->actualUsername && $password == $this->actualPassword)
        {
            $_SESSION[$this->sessionID] = true;
            $_SESSION[$this->sessionUser] = $username;

            return true;
        }
        else
        {
            return false;
        }
    }

}