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
        setlocale(LC_ALL, "sv");
        $this->date = strftime("%A, den %d %B år %Y. Klockan är [%X] ");
    }

    public function setMessage($message)
    {
        switch($message)
        {
            case "missingUsername":
                $this->message = "Användarnamn saknas";
                break;
            case "missingPassword":
                $this->message = "Lösenord saknas";
                break;
            case "wrongPassOrUser":
                $this->message = "Felaktigt användarnamn och/eller lösenord";
                break;
            case "successfulLogin":
                $this->message = "Inloggning lyckades";
                break;
            case "successfulLogout":
                $this->message = "Du har nu loggat ut";
                break;
            case "successfulLoginWithCheck":
                $this->message = "Inloggning lyckades och vi kommer ihåg dig nästa gång";
                break;
            case "successfulLoginWithCookies":
                $this->message = "Inloggning lyckades via cookies";
                break;
            case "failedLoginWithCookies":
                $this->message = "Felaktig information i cookie";
                break;
        }
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

    public function cookiesExist()
    {
        //if(isset($_COOKIE['']))
    }
}
