<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class widgetlib {

    function __construct() {
        include(APPPATH . '/third_party/Widget.php');
    }

}