<?php 
class InformationController {
    function returnPolicy() {
        require ABSPATH_SITE . 'view/information/returnPolicy.php';
    }

    function paymentPolicy() {
        require ABSPATH_SITE . 'view/information/paymentPolicy.php';
    }

    function deliveryPolicy() {
        require ABSPATH_SITE . 'view/information/deliveryPolicy.php';
    }


}
