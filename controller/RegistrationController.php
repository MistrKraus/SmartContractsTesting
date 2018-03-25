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
        $this->header['title'] = 'Register';
        $this->header['subtitle'] = 'MetaMast required';
        // Nastavení šablony
        $this->view = 'registration';
        $_SESSION['description'] = "RegistrationController";

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
                return;
            }
            $userName = $_POST['userName'];
            $passW = $_POST['passW'];
            $options = ['cost' => 12,];
            $passW = password_hash($passW, PASSWORD_BCRYPT, $options);

            $passWord = User::getUserPassword($userName);

            if (strlen($passWord["password"]) > 0) {
//                $this->addMessage("Toto uživatelské jméno je bohužel obsazené.");
                $this->data['error'][0] = "Uživatelské jméno je obsazené";
                return;
            }

            // zaregustruje uživatele
            User::registerUser($userName, $passW);

            $_SESSION['user_id'] = Db::getLastId();
            $_SESSION['user_position'] = 2;
            $_SESSION['user_name'] = $userName;

//            $this->addMessage("Uživatel $userName úspěšně zaregistrován!");

            // přesměruje na původní adresu
            $this->redirectBack();
        }
    }

    // zkontroluje vstup
    function testPost() {
        $isOk = true;

        if (!(isset($_POST['userName']) && !empty($_POST['userName']))) {
//            $this->addMessage("'Uživatelské jméno' není vyplněné!");
            $this->data['error'][0] = "Povinné pole";
            $isOk = false;
        }

        if (!(isset($_POST['passW']) && !empty($_POST['passW']))) {
//            $this->addMessage("'Heslo' není vyplněné!");
            $this->data['error'][1] = "Povinné pole";
            $isOk = false;
        }

        if (!(isset($_POST['passWA']) && !empty($_POST['passWA']))) {
//            $this->addMessage("'Heslo znovu' není vyplněné");
            $this->data['error'][2] = "Povinné pole";
            $isOk = false;
        }

        if ($isOk) {
            if ($_POST['passW'] != $_POST['passWA']) {
//                $this->addMessage("Hesla se neshodují!");
                $this->data['error'][2] = "Hesla se neshodují";
                return false;
            }
        }

        $_SESSION['userName'] = $_POST['userName'];
        $_SESSION['passW'] = $_POST['passW'];
        $_SESSION['passWA'] = $_POST['passWA'];

        return $isOk;
    }

    // vymaže data ze Session
    function clearController() {
        unset($_SESSION['userName']);
        unset($_SESSION['passW']);
        unset($_SESSION['passWA']);
    }
}