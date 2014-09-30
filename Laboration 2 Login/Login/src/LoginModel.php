<?php
require_once("FileMaster.php");
class LoginModel
{
    private $actualUsername;
    private $actualPassword;
    private $sessionID;
    private $sessionUser;
    private $sessionUserIP;
    private $sessionUserAgent;
    private $fileMaster;

    public function __construct()
    {
        $this->actualUsername = "Admin";
        $this->actualPassword = "Password";
        $this->sessionID = "isloggedin";
        $this->sessionUser = "sessionuser";
        $this->sessionUserIP = "sessionuserip";
        $this->sessionUserAgent = "sessionuseragent";
        $this->fileMaster = new FileMaster();
    }

    //Generates new session ID, minor session hijack protection
    public function refreshSession()
    {
        session_regenerate_id();
    }


    public function isLoggedIn($userIP, $userAgent)
    {
        if(isset($_SESSION[$this->sessionID]) && $_SESSION[$this->sessionID] === true
        && $_SESSION[$this->sessionUserIP] === $userIP
        && $_SESSION[$this->sessionUserAgent] === $userAgent) // Tog bort denna && $_SESSION[$this->sessionUser] === $this->actualUsername, den behövs ej...
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

    public function userDoesExist($usernameToCheck){
        //hämtar ner array med användarnamn
        $users = $this->fileMaster->getUserOrPasswordList("username");

        for($i = 0; $i < count($users); $i++){
             if(trim($users[$i]) === $usernameToCheck ){
                 return true;
             }
        }
        return false;
    }

    public function login($username, $password, $userIP, $userAgent)
    {
        //hämtar ner array med användarnamn
        $users = $this->fileMaster->getUserOrPasswordList("username");


        if($this->userDoesExist($username)){

            //Hämtar ner (det krypterade) lösenorden
            $passList = $this->fileMaster->getUserOrPasswordList("password");
            $myRegEx = '/^'.$username.':.*/'; //regulärt uttryck för att hitta användarens lösenord

            for($j=0;$j < count($passList);$j++){
                $passList[$j]; //användarnamnet + lösenordet...

                if(preg_match($myRegEx,trim($passList[$j]))== 1){
                    //Om reg. matchar användarnamnet så har vi hittat lösenordet

                    //Tar bort den delen som identiferar vems lösenord det är (så bara lösenordet är kvar..)
                    $onlyPass = str_replace($username.":", "" , $passList[$j]);
                    $onlyPass = trim($onlyPass);


                    if(false){
                        //om vi loggar in genom kakor så ska vi inte hasha lösenordet, bara jämföra...
                        //TODO: login trough cookies..

                        if($password == $onlyPass){
                            return true;
                        }else{
                            //om detta ej stämmer så är det något fel på kakan
                            //den är troligtvis manipulerad, vi ska ta bort allt då...(görs i kakmetoden)
                            return false;
                        }

                    }else{
                        // om $loginTroughCookies inte används, så är det en vanlig inloggning

                        if($password == $onlyPass){
                            $this->setSession($username, $userIP, $userAgent);
                            return true;
                        }
                    }
                }
            }
        }
        /*
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
        */

    }

    public function loginWithCookies($usernameCookie, $tokenPassCookie, $userIP, $userAgent)
    {
        $userNameList = $this->fileMaster->getUserOrPasswordList("username");
        for($i = 0; $i < count($userNameList); $i++){

            if($usernameCookie == trim($userNameList[$i]) && $tokenPassCookie == $this->getTokenPass()
                && $this->getCookieExpiration() > time()){

                $this->setSession($usernameCookie, $userIP, $userAgent);

                return true;
            }

        }

        return false;

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

    public function checkForInvalidChars($stringToCheckMeanCharsIn){

        if(preg_match("/[^-a-z0-9_]/i", $stringToCheckMeanCharsIn)){
            return true;
        }
        return false;
    }
    public function registerUser($userDetails)
    {
        $username = $userDetails["username"];
        $password = $userDetails["password"];
        $writeToUser = fopen(FileMaster::$usersFile,"a");

        fwrite($writeToUser, $username . "\n");

        $writeToUserPass = fopen(FileMaster::$usersPassFile,"a");
        fwrite($writeToUserPass, $username . ":" . $password . "\n");

    }

    public function checkValidRegistrationData($userDetails){
        $username = $userDetails["username"];
        $password = $userDetails["password"];
        $rpassword = $userDetails["repeatpassword"];

        $arrWithErrorMessages = array();
        if($this->userDoesExist($username)){
            array_push($arrWithErrorMessages, "Användarnamnet är redan upptaget");

        }else if($this->checkForInvalidChars($username)){
            array_push($arrWithErrorMessages, "Användarnamnet innehåller ogiltiga tecken");

        }else if(strlen($username) < 3){
            array_push($arrWithErrorMessages, "Användarnamnet har för få tecken. Minst 3 tecken");

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