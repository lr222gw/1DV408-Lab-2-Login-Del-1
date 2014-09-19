<?php

class LoginView
{
    private $date;
    private $model;
    private $message;

    public function __construct(LoginModel $model)
    {
        $this->model = $model;
        $this->message= "";

        //These time settings works on the webhost, but on local the Days are in english.
        date_default_timezone_set("Europe/Stockholm");
        setlocale(LC_TIME, 'sv_SE');

        //Make sure åäö works for days
        $day = utf8_encode(strftime("%A"));

        $this->date = strftime($day.", den %d %B år %Y. Klockan är [%X] ");
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getUserAgent()
    {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    public function getUserIP()
    {
        return $_SERVER["REMOTE_ADDR"];
    }

    public function getUserName()
    {
        if(!empty($_POST['username']))
        {
            return trim($_POST['username']);
        }
        else
        {
            return "";
        }
    }

    public function getPassword()
    {
         return trim($_POST['password']);
    }

    public function getUsernameCookie()
    {
        return $_COOKIE['username'];
    }

    public function getTokenPassCookie()
    {
        return $_COOKIE['tokenpass'];
    }

    public function didUserLogout()
    {
        return isset($_GET['logout']);
    }

    public function didUserLogin()
    {
        return isset($_POST['login']);
    }

    public function didUserSelectAutoLogin()
    {
        return isset($_POST['autologin']);
    }

    public function getLoginHTML()
    {
        $ret = "    <h1>Laborationskod km222ew</h1>
                    <h2>Du är inte inloggad</h2>
                    <p>{$this->message}</p>
                    <form action='?login' method='post'>
                        <fieldset>
                            <legend>Login - Skriv in användarnamn och lösenord</legend>
                            <label for='UserNameID'>Användarnamn :</label>
                            <input type='text' size='20' name='username' id='UserNameID' value='{$this->getUserName()}'>
                            <label for='PasswordID'>Lösenord :</label>
                            <input type='password' size='20' name='password' id='PasswordID' value>
                            <label for='AutologinID'>Håll mig inloggad :</label>
                            <input type='checkbox' name='autologin' id='AutologinID'>
                            <input type='submit' name='login' value='Logga in'>
                        </fieldset>
                    </form>

                    <p>{$this->date}</p>
                 ";

        return $ret;
    }

    public function getLoggedInHTML()
    {
        $ret = "<h1>Laborationskod km222ew</h1>
                <h2>{$this->model->getSessionUsername()} är inloggad</h2>
                <p>{$this->message}</p>
                <p><a href='?logout'>Logga ut</a></p>
                <p>{$this->date}</p>
                ";

        return $ret;
    }

    public function createCookies($username, $tokenPass)
    {
        $time = time()+60;

        setcookie("username", $username, $time);
        setcookie("tokenpass", $tokenPass, $time);

        return $time;

    }

    public function deleteCookies()
    {
        setcookie("username", "", time()-1);
        setcookie("tokenpass","", time()-1);
    }

    public function cookiesExist()
    {
        if(isset($_COOKIE['username']) === true && isset($_COOKIE["tokenpass"]) === true)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
