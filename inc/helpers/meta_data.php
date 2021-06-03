<?php
class Meta_Data_Debuger {
    public $type;
    public $id;
    public $action;
    public $types = array('user','post','tax');
    public $actions = array(
        'show_user_profile' => 'user',
        'edit_user_profile' => 'user',
    );

    public function __construct()
    {
        // global $pagenow;

        add_action( 'show_user_profile', array($this,'set_render_data'));
        add_action( 'edit_user_profile', array($this,'set_render_data'));
    }
    public function set_render_data($user){
        $this->action = current_action();
        $this->type   = $this->actions[$this->action];
        if ($this->type ==  'user') {
            $this->id = $user->data->ID;
            $data = $this->get_element_data();
            $this->render_data($data);
        }
    }
    public function get_element_data(){
        // maybe_unserialize
        if($this->type == 'user'){
            global $wpdb;
            return $wpdb->get_results( "SELECT * FROM wp_usermeta WHERE user_id = " . $this->id  , 'ARRAY_A');
        }
    }
    public function render_data($data){
        if(!empty($data)){
            echo "<div style='font-size:16px; background-color: white;'><h3>Debug Data</h3><table><tbody>";
            foreach ($data as $meta) {
                echo "<tr><td><b>".$meta['meta_key']."</b></td><td>".$meta['meta_value']."</td></tr>";
            }
            echo "</tbody></table></div>";
        }else {
            echo "<div style='background-color: wheat;'><h3>No Data for this $this->type ($this->id)</h3></div>";
        }
    }    
}
$mata_data_debuger = new Meta_Data_Debuger();
?>