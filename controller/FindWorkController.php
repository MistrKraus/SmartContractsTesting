<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 22.03.2018
 * Time: 19:02
 */

class FindWorkController extends Controller {
    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'Find Work';
        // Podnadpis strán
        $this->header['subtitle'] = 'Choose, bind, correct!';
        // Nastavení šablony
        $this->view = 'findWork';

        $this->loggedOnly();
        $this->checkLogin();

        unset($_SESSION['works']);

        $_SESSION['works'] = array(array());
        $userID = $_SESSION['user_id'];
        $_SESSION['works'] = Work::listWorks($userID);
        if ($_POST) {
            $this->processMain();
            $this->loggedOnly();
            foreach ($_SESSION['works'] as $work) {
                if (isset($_POST["eth" . $work['request_id']]) && $_POST["eth" . $work['request_id']] > 0 && isset($_POST["bind" . $work['request_id']])) {

//                    $this->addMessage("Correct" . " " . $work['request_id'] . " " . $userID . " " . $_POST["eth" . $work['request_id']]);

                    $request_ID = $work['request_id'];
                    $eth = $_POST["eth" . $work['request_id']];
                    Work::createBind($request_ID, $userID, $eth);
                    $this->redirect('findWork');
                }
//                else {
//                    $this->addMessage("Error");
//                }
            }
        }
    }

    function clearController() {
    }
}