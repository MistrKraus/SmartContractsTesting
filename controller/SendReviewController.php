<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 25.03.2018
 * Time: 11:49
 */

class SendReviewController extends Controller {

    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'Sent Review';
        // Podnadpis strán
        $this->header['subtitle'] = 'insert [name] - [filename]';      //TODO insert name and filename
        // Nastavení šablony
        $this->view = 'sendReview';

        $this->loggedOnly();
        $this->checkLogin();

        if ($_POST) {
            $this->processMain();
            $this->loggedOnly();
        }

    }

    function clearController() {
        // TODO: Implement clearController() method.
    }
}