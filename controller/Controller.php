<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 05.11.2017
 * Time: 14:00
 */

abstract class Controller {

    // Pole, jehož indexy jsou poté viditelné v šabloně jako běžné proměnné
    protected $data = array();
    // Název šablony bez přípony
    protected $view = "";
    // Hlavička HTML stránky
    protected $header = array('title' => '', 'key_words' => '', 'description' => '');

    // Ošetří proměnnou pro výpis do HTML stránky
    private function avoidError($x = null) {
        if (!isset($x))
            return null;
        elseif (is_string($x))
            return htmlspecialchars($x, ENT_QUOTES);
        elseif (is_array($x)) {
            foreach($x as $k => $v) {
                $x[$k] = $this->avoidError($v);
            }
            return $x;
        }
        else
            return $x;
    }

    // Vyrenderuje pohled
    public function buildView() {
        if ($this->view) {
            extract($this->avoidError($this->data));
            extract($this->data, EXTR_PREFIX_ALL, "");
            require("view/" . $this->view . ".phtml");
        }
    }

    // Přidá zprávu pro uživatele
    public function addMessage($message) {
        if (isset($_SESSION['messages']))
            $_SESSION['messages'][] = $message;
        else
            $_SESSION['messages'] = array($message);
    }

    // Vrátí zprávy pro uživatele
    public static function getMessages() {
        if (isset($_SESSION['messages'])) {
            $messages = $_SESSION['messages'];
            unset($_SESSION['messages']);
            return $messages;
        }
        else
            return array();
    }

    // Přesměruje na dané URL
    public function redirect($url) {
        header("Location: ?Controller=$url");
        header("Connection: close");
        exit;
    }

    // Přesměruje na předchozí URL
    public function redirectBack() {
        if (isset($_SESSION['fromUrl'])) {
            $url = $_SESSION['fromUrl'];
            unset($_SESSION['fromUrl']);
            $this->redirect($url);
        } else {
            $this->redirect('intro');
        }
    }

    // Přesměruje na login a zapíše do původní URL
    public function redirectToLogin($fromUrl) {
        $_SESSION['fromUrl'] = $fromUrl;
        $this->redirect('login');
    }

    // Přesměruje na registraci a zapíše původní URL
    public function redirectToRegistration($fromUrl) {
        $_SESSION['fromUrl'] = $fromUrl;
        $this->redirect('registration');
    }

    // Odhlásí uživatele a přesměruje na původní URL
    public function logoutAndRedirect() {
        $this->logout();
        $this->redirectBack();
    }

    // Odhlásí uživatele
    public function logout() {
        unset($_SESSION['user_id']);
//        unset($_SESSION['user_position']);
        unset($_SESSION['user_name']);
    }

    // zpracuje POST z kostry html
//    public function processMain() {
//        $fromUrl = $_SESSION['fromUrl'];
//
//        if (isset($_POST['login'])) {
//            if (isset($_SESSION['user_id'])) {
//                return;
//            }
//
//            $this->redirectToLogin($fromUrl);
//        }
//
//        if (isset($_POST['registration'])) {
//            if (isset($_SESSION['user_id'])) {
//                return;
//            }
//
//            $this->redirectToRegistration($fromUrl);
//        }
//
//        if (isset($_POST['logout'])) {
//            $_SESSION['fromUrl'] = $fromUrl;
//            $this->logoutAndRedirect();
//        }
//    }

    // zkontroluje, zda přihlášený uživatel existuje, popřípadě jej odhlásí
    public function checkLogin() {
        if (isset($_SESSION['user_id'])) {
            $user = User::getUser($_SESSION['user_id']);

            if (empty($user)) {
                $this->logout();
            }
        }
    }

    public function loggedOnly() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
    }

    // Hlavní metoda controlleru
    abstract function process($params);

    // Vycisti session controlleru
    abstract function clearController();

}