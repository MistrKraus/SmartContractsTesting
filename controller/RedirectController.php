<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 05.11.2017
 * Time: 15:50
 */

class RedirectController extends Controller {

    /**
     * @var Kontroler
     */
    protected $controller;
    /**
     * @var ajaxovy pozadavek
     */
    private $ajax;

    /**
     * RedirectController constructor.
     * @param $ajax
     */
    public function __construct($ajax) {
        $this->ajax = $ajax;
    }


    // Metoda převede pomlčkovou variantu controlleru na název třídy
    private function dashToCamelNotation($text) {
        $sentence = str_replace('-', ' ', $text);
        $sentence = ucwords($sentence);
        $sentence = str_replace(' ', '', $sentence);
        return $sentence;
    }

    // Naparsuje URL adresu podle lomítek a vrátí pole parametrů
    private function parseURL($url) {
//        // odstranění všeho, co je za otazníkem
        $parsedURL = substr($url, strpos($url, "?") + 1);

        $pole = explode("&", $parsedURL);
        $temp = array();
        $i = 0;
        foreach ($pole as $item) {
            $itemParts = explode("=", $item);
            $temp[$i++] = $itemParts[1];
        }

        return $temp;

//        //var_dump($temp);

        // Naparsuje jednotlivé části URL adresy do asociativního pole
        $parsedURL = parse_url($url);
        // Odstranění počátečního lomítka
        $parsedURL["path"] = ltrim($parsedURL["path"], "/");
        // Odstranění bílých znaků kolem adresy
        $parsedURL["path"] = trim($parsedURL["path"]);
        // Rozbití řetězce podle lomítek
        $splitedPath = explode("/", $parsedURL["path"]);
        array_shift($splitedPath);

        //var_dump($splitedPath);


        return $splitedPath;
    }

    // zpracuje parametry, připraví kontroler a přesměruje
    function process($params) {
        $parsedURL = $this->parseURL($params[0]);

//        $this->addMessage($parsedURL[0]);
        if(empty($parsedURL[0])) {
            $this->redirect("intro");
        }

        if (!$this->ajax)
            $ControllerClass = $this->dashToCamelNotation(array_shift($parsedURL)) . 'Controller';
        else
            $ControllerClass = $this->dashToCamelNotation(array_shift($parsedURL)) . 'AjaxController';

        if (isset($_SESSION['description'])) {
            $fromClass = $_SESSION['description'];
            //echo $fromClass . " " . $ControllerClass . " -> ";
            if (file_exists('controller/' . $fromClass . '.php') && $fromClass != $ControllerClass) {
                $fromController = new $fromClass;
                $fromController->clearController();
                //echo "SESSION vycistena";
            }
        }

        if (file_exists('controller/' . $ControllerClass . '.php')) {
            $this->controller = new $ControllerClass;
        } else $this->redirect('error');

        $this->controller->process($parsedURL);

        // Nastavení proměnných pro šablonu
        $this->data['title'] = $this->controller->header['title'];
        $this->data['subtitle'] = $this->controller->header['subtitle'];
        $this->data['description'] = $this->controller->header['description'];
        $this->data['key_words'] = $this->controller->header['key_words'];
        $this->data['messages'] = $this->getMessages();
        // Nastavení hlavní šablony
        $this->view = 'main';
    }

    function clearController() {

    }
}