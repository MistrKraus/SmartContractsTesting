<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 25.03.2018
 * Time: 11:49
 */

class SendReviewController extends Controller {

    function process($params) {
        $bind = Work::getBindById($_SESSION['bindReviewId']);

        // Hlavička stránky
        $this->header['title'] = 'Sent Review';
        // Podnadpis strán
        $this->header['subtitle'] = $bind['user'] . " - " . $bind['title'];
        // Nastavení šablony
        $this->view = 'sendReview';

        $this->loggedOnly();
        $this->checkLogin();

        if ($_POST) {
            $this->processMain();
            $this->loggedOnly();


            if(isset($_POST['submitReview']) && isset($_POST['stars'])){
//                $this->addMessage($_POST['stars'] . " ... " . $_POST['review'] . " ... " . $_SESSION['bindReview']);
                if(isset($_POST['review'])){
                    $desc = $_POST['review'];
                } else {
                    $desc = "No additional text";
                }
                Work::sendReview($_SESSION['bindReview'],$_POST['stars'], $desc);
                $_SESSION['userReview'] = "";
                $_SESSION['fileReview'] = "";
                $_SESSION['bindReview'] = "";
            }
        }
    }

    function clearController() {
        // TODO: Implement clearController() method.
    }
}