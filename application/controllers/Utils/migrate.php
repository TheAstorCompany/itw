<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migrate extends CI_Controller 
{

    public function lang($param = null) {
            echo $param;
    }
	
    public function latest() {
        $this->load->library('lang', 'lang');
        $this->load->library('migration');

        echo "*** Start migration at ".date('m-d-Y, H:i:s')."<br/>";
        echo '<span style="display:none">[START]</span>';

        if (!$this->migration->latest()) {
            echo '<span style="display:none">[ERROR]</span>';
            show_error($this->migration->error_string());
        }

        echo '<span style="display:none">[DONE]</span>';
        echo "<br/>*** Migration complete successfully at ".date('m-d-Y, H:i:s')."<br/>";
    }
}