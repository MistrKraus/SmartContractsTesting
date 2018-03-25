<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 12.12.2017
 * Time: 19:24
 */

class LoginController extends Controller
{
    function process($params)
    {
        // Hlavička stránky
        $this->header['title'] = 'Login';
        $this->header['subtitle'] = 'MetaMast required';
        // Nastavení šablony
        $this->view = 'login';

        $_SESSION['description'] = "LoginController";

        $this->checkLogin();

        if (isset($_SESSION['user_id']))
            $this->redirect('intro');

        if ($_POST) {
            $this->processMain();

            if (!$this->testPost()) {
                return;
            }

            $userName = $_POST['userName'];
            $passW = $_POST['passW'];

            // zkontroluje, zda uživatelské jméno existuje
            $password = User::getUserPassword($userName)['password'];

            if (strlen($password) == 0) {
//                $this->addMessage("Chybné uživatelské jméno!");
                $this->data['error'][0]="Chybné uživatelské jméno";
                return;
            }

            // přihlásí uživatele
            if (password_verify($passW, $password)) {
                $user = User::logIn($userName);

                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_position'] = $user['position_id'];
                $_SESSION['user_name'] = $user['nickname'];

                $this->redirectBack();
            } else {
                //$_SESSION['user_id'] = 0;
//                $this->addMessage("Chybné heslo");
                $this->data['error'][1]="Chybné heslo";
            }
        }
    }

    // Zkontroluje vstupní data
    function testPost() {
        if (isset($_SESSION['user_id'])) {
            return false;
        }

        $isOk = true;

        if (!(isset($_POST['userName']) && !empty($_POST['userName']))) {
//            $this->addMessage("'Uživatelské jméno' není vyplněné!");
            $this->data['error'][0]="Povinné pole";
            $isOk = false;
        }

        if (!(isset($_POST['passW']) && !empty($_POST['passW']))) {
//            $this->addMessage("'Heslo' není vyplněné!");
            $this->data['error'][1]="Povinné pole";
            return false;
        }

        $_SESSION['userName'] = $_POST['userName'];
        $_SESSION['passW'] = $_POST['passW'];

        return $isOk;
    }

    function clearController() {
        unset($_SESSION['userName']);
        unset($_SESSION['passW']);
    }
}