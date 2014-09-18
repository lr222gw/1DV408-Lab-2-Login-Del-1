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

    public function doLogin()
    {
        if($this->model->isLoggedIn())
        {
            if($this->view->didUserLogout())
            {
                $this->model->logout();

                //view logged out with message
                $this->view->setMessage("successfulLogout");
                return $this->view->getLoginHTML();
            }
            else
            {
                //view logged in
                return $this->view->getLoggedInHTML();
            }
        }
        else
        {
            /*if($this->view->cookiesExist())
            {
                if($this->model->login($this->view->getUsernameCookie(), $this->view->getPasswordCookie()))
                {
                    //view logged in with success cookie message
                    return $this->view->getLoggedInHTML();
                }
                else
                {

                }

            }*/
            if($this->view->didUserLogin())
            {
                $username = $this->view->getUsername();
                $password = $this->view->getPassword();

                if(empty($username))
                {
                    $this->view->setMessage("missingUsername");
                    return $this->view->getLoginHTML();
                }
                if(empty($password))
                {
                    $this->view->setMessage("missingPassword");
                    return $this->view->getLoginHTML();
                }
                if($this->model->login($username, $password))
                {
                    //view logged in with success message
                    $this->view->setMessage("successfulLogin");
                    return $this->view->getLoggedInHTML();
                }
                else
                {
                    //view logged out with the right failure message
                    $this->view->setMessage("wrongPassOrUser");
                    return $this->view->getLoginHTML();
                }

            }
            else
            {
                //view logged out
                return $this->view->getLoginHTML();
            }
        }
    }
}