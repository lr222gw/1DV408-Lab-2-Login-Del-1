<?php

require_once("LoginView.php");
require_once("LoginModel.php");

class LoginController
{
    private $view;
    private $model;

    public function __construct()
    {
        $this->model = new LoginModel();
        $this->view = new LoginView($this->model);
    }

    //Create cookies, also used to create new cookies after logging in with the previous ones
    public function refreshCookies($username)
    {
        $expiration = $this->view->createCookies($username, $this->model->createTokenPass());
        $this->model->saveCookieExpiration($expiration);
    }

    public function doLogin()
    {
        //Minor session hijack protection
        $this->model->refreshSession();


        if($this->model->isLoggedIn($this->view->getUserIP(), $this->view->getUserAgent()))
        {
            if($this->view->didUserLogout())
            {
                $this->model->logout();

                if($this->view->cookiesExist())
                {
                    $this->view->deleteCookies();
                }

                //view logged out with message
                $this->view->setMessage("Du har nu loggat ut");
                return $this->view->getLoginHTML();
            }
            else
            {
                return $this->view->getLoggedInHTML();
            }
        }
        else
        {
            if($this->view->cookiesExist())
            {
                if($this->model->loginWithCookies($this->view->getUsernameCookie(), $this->view->getTokenPassCookie(),
                    $this->view->getUserIP(), $this->view->getUserAgent()))
                {
                    //view logged in with success cookie message
                    $this->refreshCookies($this->view->getUsernameCookie());
                    $this->view->setMessage("Inloggning lyckades via cookies");
                    return $this->view->getLoggedInHTML();
                }
                else
                {
                    $this->view->deleteCookies();
                    $this->view->setMessage("Felaktig information i cookie");
                    return $this->view->getLoginHTML();
                }

            }


            if($this->view->didUserLogin())
            {

                $username = $this->view->getUsername();
                $password = $this->view->getPassword();

                if(empty($username))
                {
                    $this->view->setMessage("Användarnamn saknas");
                    return $this->view->getLoginHTML();
                }
                if(empty($password))
                {
                    $this->view->setMessage("Lösenord saknas");
                    return $this->view->getLoginHTML();
                }
                if($this->model->login($username, $password, $this->view->getUserIP(), $this->view->getUserAgent()))
                {
                    //view logged in with success message
                    $this->view->setMessage("Inloggning lyckades");

                    if($this->view->didUserSelectAutoLogin())
                    {
                        $expiration = $this->view->createCookies($username, $this->model->createTokenPass());
                        $this->model->saveCookieExpiration($expiration);
                        $this->view->setMessage("Inloggning lyckades och vi kommer ihåg dig nästa gång");
                    }

                    return $this->view->getLoggedInHTML();
                }
                else
                {
                    //view logged out with the right failure message
                    $this->view->setMessage("Felaktigt användarnamn och/eller lösenord");
                    return $this->view->getLoginHTML();
                }

            }
            else
            {

                //om användaren inte loggat in så har den atningen tryckt att den ska registrera sig eller så ska logga in rutan visas...
                if($this->view->didUserPressRegister()){

                    if($this->view->didUserTryToRegister()){ // om användaren tryckt på registrera knappen... (som skickar uppgifter..)
                        //så ska uppgifterna kontrolleras

                        $userDetails = $this->view->getRegistrationDetailFromForm();

                        $possbleMessage = $this->model->checkValidRegistrationData($userDetails); //returnerar svarsmeddelanden...
                        if($possbleMessage === true){
                            //Då det inte gjordes några felmeddelanden så ska vi registrera användaren
                            $this->model->registerUser($userDetails);
                            $this->view->setMessage("Registrering av ny användare lyckades");

                        }else{
                            //om valdiationen ej var rätt så ska felmeddelanden visas

                            $this->view->setMessage($possbleMessage);
                        }


                    }


                    return $this->view->getRegisterForm();
                }


                //view logged out
                return $this->view->getLoginHTML();
            }
        }
    }
}