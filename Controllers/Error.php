<?php

class Errors extends Controllers {
    public function __construct() {

        parent::__construct();
    }

    public function home($params) {
        $this->views->getView($this, "error");
    }
}

$notFound = new Errors();
$notFound->home(null);

?>