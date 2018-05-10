<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 12.12.2017
 * Time: 19:24
 */

class LoginController extends Controller {

    function process($params)
    {
        // Hlavička stránky
        $this->header['title'] = 'Sign in';
        $this->header['subtitle'] = 'MetaMask required';
        // Nastavení šablony
        $this->view = 'login';

        $_SESSION['description'] = "LoginController";

        if (isset($_SESSION['user_id'])) {
            $this->redirect('intro');
        }

        $this->checkLogin();

        if ($_POST) {
            $this->processMain();

            if ($_POST['metamask'] == "undefined")
                $this->addMessage("Please log in to your MataMask account you registered with on this website.");
            else {
                $user = User::logIn($_POST['metamask']);
                $_SESSION['user_id'] = $user['users_id'];
                $_SESSION['username'] = $user['username'];

                $this->redirect("intro");
            }
        }
    }

    function clearController() {
    }
}