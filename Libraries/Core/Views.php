<?php

class Views {
    function  getView($controller, $view, $data = "") {
        $controller = get_class($controller);
        if($controller == "Home"){
            $view = "Views/" .$view.".php";
        }else{
            $view = "Views/" .$controller."/".$view.".php";
        }

        if (is_readable($view)) {
            require_once $view;
        } else {
            http_response_code(404);
            echo "View not found: " . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            exit;
        }
    }
}

?>
