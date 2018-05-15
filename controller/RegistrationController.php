<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 12.12.2017
 * Time: 19:24
 */

class RegistrationController extends Controller {
    function process($params)
    {
        // Hlavička stránky
        $this->header['title'] = 'Sign up';
        $this->header['subtitle'] = 'MetaMask required';
        // Nastavení šablony
        $this->view = 'registration';
        $_SESSION['description'] = "RegistrationController";

        if (isset($_SESSION['user_id'])) {
            $this->redirect('intro');
        }

        $this->checkLogin();

        // pokud je uživatel přihlášený, je přesměrován na úvodní stranu
        if (isset($_SESSION['user_id'])) {
            $this->redirect('intro');
        }

        // zpracuje vstup
        if ($_POST) {
            $this->processMain();

            // zkonroluje správnost vstupu
            if (!$this->testPost()) {
                $this->addMessage($this->data['error'][0]);
                return;
            }

            $userName = $_POST['userName'];
            $mail = $_POST['mail'];
            $mm = $_POST['metamask'];

            settype($mm, "string");

            // zaregustruje uživatele
            User::registerUser($userName, $mail, $mm);

            $_SESSION['user_id'] = Db::getLastId();
            $_SESSION['username'] = $userName;

            // přesměruje na původní adresu
            $this->redirectBack();
        }
    }

    // zkontroluje vstup
    function testPost() {
        $isOk = true;

        if (!(isset($_POST['userName']) && !empty($_POST['userName']))) {
//            $this->addMessage("'Uživatelské jméno' není vyplněné!");
            $this->data['error'][0] = "Required field";
            $isOk = false;
        } else {
            $_SESSION['regUsername'] = $_POST['userName'];
        }

        if (!(isset($_POST['mail']) && !empty($_POST['mail']))) {
//            $this->addMessage("'Heslo znovu' není vyplněné");
            $this->data['error'][1] = "Required field";
            $isOk = false;
        } else {
            $_SESSION['mail'] = $_POST['mail'];
        }

        if (!(isset($_POST['metamask']) && !empty($_POST['metamask']))) {
            $this->data['error'][3] = "MetaMask required";
            return false;
        }

        $mm = $_POST['metamask'];
        settype($mm, "string");

        if ($mm!='undefined' && strlen($mm)==42) {
            $user = User::logIn($mm);
            if (strlen($user['users_id']) > 0) {
                $this->data['error'][3] = "Ethereum address already registered!";
                return false;
            }
        } else {
            return false;
        }

        return $isOk;
    }

    // vymaže data ze Session
    function clearController() {
        unset($_SESSION['regUsername']);
        unset($_SESSION['mail']);
    }
}