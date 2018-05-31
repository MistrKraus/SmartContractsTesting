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
//                $this->addMessage($_POST['stars']);

                if(isset($_POST['review']) && !empty($_POST['review'])){
                    $desc = $_POST['review'];
                } else {
                    $desc = "No additional text";
                }
//                $this->addMessage($desc);
//                $this->addMessage($_SESSION['bindReviewId']);

                Work::sendReview($_SESSION['bindReviewId'], $_POST['stars'], $desc);
                unset($_SESSION['userReview']);
                unset($_SESSION['fileReview']);
                unset($_SESSION['bindReviewId']);

                $this->redirect('myOffers');
            }
        }
    }

    function clearController() {
        // TODO: Implement clearController() method.
    }
}