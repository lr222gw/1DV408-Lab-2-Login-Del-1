<?php
require_once("Date.php");
class LoginView
{
    private $date;
    private $model;
    private $message;
    private $Dateobj;

    public function __construct(LoginModel $model)
    {
        $this->model = $model;
        $this->message= "";
        $this->Dateobj= new Date();

        //These time settings works on the webhost, but on local the Days are in english.
            //^Fixed with my function getSwedishWeekNames....
        date_default_timezone_set("Europe/Stockholm");
        setlocale(LC_TIME, 'sv_SE');

        //Make sure åäö works for days
        $day = utf8_encode(strftime("%A"));

        $day = $this->Dateobj->getSwedishWeekNames($day);
        utf8_encode($day);


        $this->date = strftime($day.", den %d %B år %Y. Klockan är [%X] ");
    }


    public function setMessage($message)
    {

         if(gettype($message) === "array"){
             $longstring ="";
             for($i=0; $i < count($message);$i++){
                 $longstring .= $message[$i] . ". <br>";
             }
             $message = $longstring;
        }

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

    public function didUserPressRegister()
    {
        if(isset($_POST['register']) || isset($_GET['register'])){
            return true;
        }else{
            return false;
        }
    }
    public function didUserTryToRegister(){
        return isset($_POST["regist"]);
    }

    public function getLoginHTML()
    {

        $ret = "    <h1>Laborationskod km222ew -> lr22gw labb4 </h1>
                    <h2>Du är inte inloggad</h2>
                    <p>{$this->message}</p>
                    <form action='?login' method='post' id='loginform'>
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
                    <form action='?register' method='post'>
                        <input type='submit' name='register' value='Till Registrering'>
                    </form>


                    <p>{$this->date}</p>
                 ";

        return $ret;
    }
    public function getRegisterForm(){

        $ret = "    <h1>Laborationskod km222ew -> lr22gw labb4 </h1>
                    <h2>Du är inte inloggad, Registrera användare</h2>
                    <a href='{$_SERVER["PHP_SELF"]}'>Tillbaka</a>
                    <p>{$this->message}</p>
                    <form action='?register' method='post' id='regform'>
                        <fieldset>
                            <legend>Registrera - Fyll i användarnamn och lösenord</legend>
                            <label for='UserNameID'>Användarnamn :</label>
                            <input type='text' size='20' name='username' id='UserNameID' value='{$this->getUserName()}'>
                            <label for='PasswordID'>Lösenord :</label>
                            <input type='password' size='20' name='password' id='PasswordID' value>
                            <label for='RepeatPasswordID'>Repetera Lösenord :</label>
                            <input type='password' size='20' name='repeatpassword' id='RepeatPasswordID' value>
                            <input type='submit' name='regist' value='Registrera'>
                        </fieldset>
                    </form>

                    <p>{$this->date}</p>
                 ";

        return $ret;

    }
    public function getRegistrationDetailFromForm()
    {
        $ArrToReturn = array();
        $ArrToReturn["password"] = $_POST["password"];
        $ArrToReturn["repeatpassword"] = $_POST["repeatpassword"];
        $ArrToReturn["username"] = $_POST["username"];
        return $ArrToReturn;
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
