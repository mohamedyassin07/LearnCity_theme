<?php
    class Unikassel_Projects{
        public $fist_step       = 'info';
        public $fist_enabler    = 'organization_management';
        public $guide_lines     = 'guide_lines';
        public $success_factors = 'success_factors';
        public $id;

        public function __construct($id = 0){
            //get_id
            echo get_post_type();

            

            $this->factors_steps();
            add_shortcode('unikassel', array($this,'render') );


            add_filter('acf/load_field/name=dynamic_guide_lines',array($this,'guidelines') );
            add_action('acf/render_field/name=guide_lines_chart', array($this,'dynamic_guide_lines_chart'));
            //add_filter('acf/prepare_field/type=repeater', array($this,'sync_Reqs_from' ) );

            // add_action('acf/include_field_types', 	array($this, 'include_fields')); // v5
            // add_action('acf/register_fields', 		array($this, 'include_fields')); // v4
    
            




            //add_filter('acf/pre_save_post' , array($this,'pre_save_post'), 10, 1 );

            add_action('acf/render_field/name=behilfliche_dokumente', array($this,'documents_boxs'));
            add_action('acf/render_field/name=supporting_documents_contractual', array($this,'documents_boxs'));
            add_action('acf/render_field/name=supporting_documents_mangement', array($this,'documents_boxs'));

            


            $this->debug(); 
        }
        public function get_id($id){
            if(get_post_type() == 'project' && $id > 0){
                return $this->id = $id;
            }else {
                return $this->id = 0;
            }
        }
        
        public function dashboard_url(){
            return get_site_url();
        }

        public function steps($debug = false){
            $steps = array();
            $groups = acf_get_field_groups(array('post_type' => 'project'));
            foreach ($groups as $group) {
                $key = $group['description'] != '' ? $group['description'] : $this->str_key($group['title']);
                $steps[$key] =  array($group['title'], $group['key']);
                if(isset($group['guidelne']) && $group['guidelne'] == 1 ){
                    $steps[$key][] =  1;
                }
            }
            return $steps;
        }

        public function render(){
            $this->id = get_the_id();
            if(isset($_GET['report'])){
                $this->render_report();
            }else {
                $this->render_edit();
            }
        }
        public function render_edit(){
            if(isset($_GET['edit']) && $_GET['edit'] ==  $this->guide_lines ){
                return $this->guide_lines_render();
            }elseif (isset($_GET['edit']) && $_GET['edit'] ==  $this->success_factors) {
                return $this->sucess_factors_render();
            }

            if( get_post_type() == 'project' && empty($this->project_factors()) ){
                $link = $this->dashboard_url().'/?edit='.$this->success_factors.'&project_ref='.$this->id.'&step_ref='. $this->curr_step();
                printf(
                   __('Please Set at least one success Factor for the project <a href="%s">Click Here</a> ' , 'uni')
                    ,$link);
                return;
            }
            $user_id =  get_current_user_id();
            if( $user_id >  0 ){
                $step =  $this->curr_step();

                if($step == 'home'){
                    $data['url'] = get_site_url();
                    $data['steps'] = $this->steps();
                    $args =  array(
                        'post_type'     => 'project',
                        'post_status'   =>'any',
                    );
                    $data['projects'] = get_posts($args);
                    return get_template_part('template-parts/project','home' ,$data);    
                }
                $this->enablers_navigator();
                $args = $this->step_args($step);
                acf_form($args);
                $this->navigator();
                return;
            }else {
                echo "<h2>".__('Login Form','uni')."</h2><style>.entry-content{text-align: center;}</style>";
                $this->custom_loginform();
                return;
            }
        }

        public function report_data(){
            $id = $this->id;
            $factors =  get_field('success_factors', 'option');
            foreach ($factors as $factor) {
                if($this->str_key($factor['name']) == $_GET['report'] ){
                    $key = $factor['key'];
                }
            }


            $data['id'] =  $id;
            $data['orgnisation']['profits'] = get_post_meta($id, 'profits_for_the_organization'.'_'. $key , true);
            $data['orgnisation']['tasks'] = get_post_meta($id, 'tasks_for_the_organization'.'_'. $key , true);

            $data['team']['profits'] = get_post_meta($id, 'profits_for_the_team'.'_'. $key , true);
            $data['team']['preparation'] = get_post_meta($id, 'team_preparation'.'_'. $key , true);

            $data['process']['neue'] = get_post_meta($id, 'neue__angepsste_prozesse'.'_'. $key , true);
            $data['process']['behilfliche_dokumente'] = get_post_meta($id, 'behilfliche_dokumente'.'_'. $key , true);

            return $data;
        }

        public function render_report(){
            $args = $this->report_data();
            pre($args);
            get_template_part('template-parts/project','report',$args);
        }
        public function curr_step(){
            $cur_pst_type =  get_post_type();
            if ($cur_pst_type == 'page' && (!isset($_GET['step']) ||  $_GET['step'] != $this->fist_step    ) ) { // Home template
                return 'home';
            }elseif ($cur_pst_type == 'project' && isset($_GET['step']) && isset($this->steps()[$_GET['step']]) ) { // Editing
                return $_GET['step'];
            }else{
                return $this->fist_step ;
            }
        }
        public function step_args($step){
            $post_type = get_post_type();
            
            if($step ==  $this->fist_step && $post_type == 'page'){
                $args =  array(
                    'post_title' => true,
                    'post_id'       => 'new_post',
                    'new_post'      => array(
                        'post_type'     => 'project',
                        'post_status'   => 'publish'
                    ),
                    'field_groups' => array($this->steps()[$step][1]),
                    'updated_message' => __("Publish", 'uni'),
                );
            }else{
                $args =  array(
                    'post_title' => false,
                    'post_title' => $step == $this->fist_step ? true : false,               
                    'field_groups' => array($this->steps()[$step][1]),
                    'updated_message' => __("Project updated", 'uni'),
                    'label_placement' => 'top',
                );                
            }

            // $is_it_the_current = false;
            // $next = $step;
            // foreach ($this->steps() as $key => $step_data) {
            //     if($is_it_the_current){
            //         $next = $key;
            //         break;
            //     }
            //     if($key == $step){
            //         $is_it_the_current = true;
            //     }
            // }
            // $next = '?step='.$next ;
            // $args['return'] = '%post_url%'.$next;

            $next = '?step='.$step ;
            if(isset($_GET['enabler'])){
                $next .= '&enabler=' . $_GET['enabler'];
            }
            $args['return'] = '%post_url%'.$next;

            $args['html_submit_button'] = '<input type="submit" class="acf-button button button-primary button-large" value="'.__('Save','uni').'" />';
            return $args;
        }

        public function custom_loginform(){
            $args = array(
                'redirect'          => get_site_url(),
                'label_username'    => __( 'User','uni'),
                'label_password'    => __( 'Pass','uni'),
            );
            wp_login_form( $args );
   
        }
        
        function guidelines( $field ) {
            $field['choices'] = array();
            if(isset( $_GET['step'])){
                    $edit_guideline_link =  $this->dashboard_url().'/?edit='.$this->guide_lines.'&project_ref='.get_the_id().'&step_ref='. $_GET['step'];
                    $field['instructions'] ='<div style="{padding-top: 20px;padding-bottom: 20px;}"><a href="'.$edit_guideline_link.'" class="acf-button button button-primary">'.__('Add/Edit Guide Lines' , 'uni').'</a></div>';

                    if( have_rows('guide_lines', 'option') ) {
                        while( have_rows('guide_lines', 'option') ) {
                            the_row();
                            $label = get_sub_field('name');
                            $value = get_sub_field('file')['url'];
                            $field['choices'][ $value ] = $label;
                        }
                    }        
                }            
            return $field;
        }

        public function dynamic_guide_lines_chart($field)
        {
            $guides =  get_field('dynamic_guide_lines');

            $directions =  array('','','','');
            $i = 0;
            foreach ($guides as $guide) {
                $directions[$i] .= '<a target ="_blank" href="'.$guide['value'].'" >'.$guide['label'].'</a>';
                $i = $i<3 ? $i+1 : 0;
            }
            $data['directions'] = $directions;
            $data['width'] = 550;

            
            get_template_part('template-parts/guidelines', 'chart' ,$data);
        }
        public function guide_lines_render(){
            $args = array(
                'post_id'               => 'options',
                'fields'                => array('guide_lines'),
            );
            if(isset($_GET['project_ref']) && isset($_GET['step_ref'])){
                $args['return'] = get_permalink($_GET['project_ref']).'/?step='.$_GET['step_ref'];
                $args['html_submit_button'] ='<input type="submit" class="acf-button button button-primary button-large" value="'.__('Save and back to ','uni') . get_the_title($_GET['project_ref']).'" />';
            }
            acf_form($args);              
        }

        public function sucess_factors_render(){
            $args = array(
                'post_id'               => 'options',
                'fields'                => array('success_factors'),
            );
            if(isset($_GET['project_ref']) && isset($_GET['step_ref'])){
                $args['return'] = get_permalink($_GET['project_ref']).'/?step='.$_GET['step_ref'];
                $args['html_submit_button'] ='<input type="submit" class="acf-button button button-primary button-large" value="'.__('Save and back to ','uni') . get_the_title($_GET['project_ref']).'" />';
            }

            acf_form($args);              
        }

        public function factor_fields($factor_key){
            $field_group_key = 'group_605f83abbedff';
            $fields = acf_get_fields($field_group_key);

            foreach ($fields as $key => $field) {
                $fields[$key]['ID'] .= "_$factor_key";
                $fields[$key]['key'] .= "_$factor_key";
                $fields[$key]['name'] .= "_$factor_key"; 
                $fields[$key]['_name'] .= "_$factor_key";            
            }

            //pre($fields ,  $factor_key);


            return $fields;
        }
        public function enabler_fields($factor_key){

            $fields             = acf_get_fields('group_605f83abbedff');
            $requirments_fields = acf_get_fields('group_6062b0af0bbed');
            $list_fields       = false;
            $new_fields =  array();

            
            foreach ($fields as $key => $field) {
                if($field['type'] == 'tab'){
                    $list_fields =  isset($_GET['enabler']) && $this->str_key($field['label']) == $_GET['enabler'] ?  true : false ;
                    unset($fields[$key]);
                }elseif ($list_fields == true) {
                    if (isset($field['columns']) &&  $field['columns'] == '3/12' && isset($_GET['enabler']) &&  $_GET['enabler'] != 'Projektdokumentationen') {
                        foreach ($requirments_fields as $req_key => $req_field) {
                            // $req_field['ID'] .= "_".$_GET['enabler']."_$factor_key";
                            // $req_field['key'] .= "_".$_GET['enabler']."_$factor_key";
                            // $req_field['name'] .= "_".$_GET['enabler']."_$factor_key";

                            $req_field['ID'] .= "_".$factor_key."_" .$_GET['enabler'];
                            $req_field['key'] .= "_".$factor_key."_" .$_GET['enabler'];
                            $req_field['name'] .= "_".$factor_key."_" .$_GET['enabler'];


                            $new_fields[] = $req_field;
                        }            
                    }
                    $field['ID'] .= "_$factor_key";
                    $field['key'] .= "_$factor_key";
                    $field['name'] .= "_$factor_key"; 
                    $new_fields[] = $field;
                }else{
                    unset($fields[$key]);
                }
            }
            return $new_fields;
        }
        public static function project_factors($id = 0){
            $id = $id > 0 ? $id : (isset($_GET['id']) ?   $_GET['id'] : 0);
            $success_factors =  get_field('success_factors', 'option');
            foreach ($success_factors as $key => $factor) {
                //if( (is_array($factor['projects']) && in_array($id,$factor['projects'])) || (isset($_GET['enabler']) && isset($_GET['step'])) || is_home() ){
                        //pre($factor['projects'] , $factor['name'] );
                    if( (is_array($factor['projects']) && !in_array($id,$factor['projects']))  ){
                        unset($success_factors[$key]);
                    }
            }
            return $success_factors;
        }
        public static function all_factors($id = 0){
            $id = $id > 0 ? $id : (isset($_GET['id']) ?   $_GET['id'] : 0);
            $success_factors =  get_field('success_factors', 'option');
            foreach ($success_factors as $key => $factor) {
                //if( (is_array($factor['projects']) && in_array($id,$factor['projects'])) || (isset($_GET['enabler']) && isset($_GET['step'])) || is_home() ){
                        //pre($factor['projects'] , $factor['name'] );
                    if( (is_array($factor['projects']) && !in_array($id,$factor['projects']))  ){
                        unset($success_factors[$key]);
                    }
            }
            return $success_factors;
        }
        public function factors_steps(){  
            $success_factors = $this->project_factors();

            foreach ($success_factors as $key => $factor) {
                $factor_key = $factor['key'];
                $name       = $factor['name'];
                $fields     = $this->enabler_fields($factor_key);

                acf_add_local_field_group(array(
                    'key' => 'group_unikassel_'.$factor_key,
                    'title' => $name,
                    'fields' => $fields,
                    'location' => array(
                        array(
                            array(
                                'param' => 'post_type',
                                'operator' => '==',
                                'value' => 'project',
                            ),
                        ),
                    ),
                    'menu_order' => 10,
                    'position' => 'normal',
                    'style' => 'default',
                    'label_placement' => 'top',
                    'instruction_placement' => 'label',
                    'hide_on_screen' => array(
                        0 => 'comments',
                        1 => 'featured_image',
                    ),
                    'active' => true,
                    'acfe_display_title' => '',
                    'acfe_autosync' => array(
                        0 => 'json',
                    ),
                    'acfe_form' => 0,
                    'acfe_meta' => '',
                    'acfe_note' => '',
                    'guidelne'  => 1
                ));
            }
        }

        public function debug(){
            $this->steps(true);
        } 

        
        public function navigator(){
            if(!is_singular( 'project' )){
                return ;
            }
            $steps =  $this->steps();
            echo '<div class="acf-tab-wrap -top">';
            echo '<ul class="acf-hl acf-tab-group"><li class="active">';
            foreach ($steps as $key => $step) {
                $class = $this->curr_step() == $key ?  'active'  : '';
                $link =  get_permalink().'?step='.$key;
                if(isset($step[2]) && $step[2] == 1 ){
                    $link .= '&enabler='.$this->fist_enabler;
                }

                echo '<li class = "'.$class.'"><a href="'.$link.'" class="acf-tab-button">'.$step[0].'</a></li>';
            }
            echo '</ul>';
            echo '</div>';
        }     

        public function enablers_navigator(){
            if(!is_singular( 'project' )){
                return ;
            }

            $steps =  $this->enablers();
            if(!isset($_GET['step']) || !isset($_GET['enabler']) || !array_key_exists($_GET['enabler'], $steps ) ){
                return;
            }
            
            echo '<div class="acf-tab-wrap -top">';
            echo '<ul class="acf-hl acf-tab-group"><li class="active">';
            foreach ($steps as $key => $step) {
                $class = $_GET['enabler'] == $key ? 'active':  '' ;
                $link = get_permalink().'?step=' . $_GET['step'].'&enabler='.$key;
                echo '<li class = "'.$class.'"><a href="'.$link.'" class="acf-tab-button">'.$step[0].'</a></li>';
            }
            echo '</ul>';
            echo '</div>';
        }     
        public static function enablers(){
            return array(
                'organization_management'   => array('Organization'),
                'team_management'           => array('Team'),
                'process_management'        => array('Process Management'),
                'contractual_management'    => array('Contractual Management'),
                'knowladage_management'     => array('Knowladage Management'),
                'projektdokumentationen'    => array('Projektdokumentationen'),
            );
        }
        public function sync_Reqs_from($field){
            //return $field;
            $values = array();
            foreach (get_field('success_factors', 'option') as $factor) {
                $factor_key =  $factor['key'];
                foreach ($this->enablers() as $enabler_key => $enabler_value) {
                    if(isset($_GET['enabler']) && $enabler_key  !=  $_GET['enabler']){
                        $curr_field = 'acf-field_605f53a29d2c3_'.$factor['key'].'_'.$enabler_key;
                        $curr_value =  get_field($curr_field);
                        //pre($curr_value , $enabler_value[0] );
                    }
                }
            }
            
            // requirements_for_605f26de9579e_Team_605f26de9579e_Team_0_name 

            //pre(get_field('field_605f53a29d2c3_605f26de9579e_Team')); 
            // pre(get_field('field_605f53a29d2c3_605f26de9579e_Process Management')); 


           if(is_array($field['value']) &&  isset($field['value'])){
            $field['value'][] =  array(
                'field_605f5599aa994' => '7878787878787877',
                'field_6062a7f4ea67c' => '09090909090909',
                'field_605f55aeaa995' => '0',
                'field_6062cb3f63c1b' => 'new message',
            );
           } 
            // pre($field);
            return $field;
        }
        public static function str_key($str){
            return str_replace(' ', '_', strtolower($str));
        }

        public function include_fields(){
            //require UNI_DIR . 'inc/acf_fields/fields/from_table.php';
            // include_once(UNI_DIR . 'inc/requirements_from_table_field.php');
        }

        public static function get_reapeter($cond){
            global $wpdb;
            $cond = "SELECT * FROM  $wpdb->postmeta  WHERE $cond ";
            $results = $wpdb->get_results($cond);
            return $results;
        }

        public static function collect_reqs_for_repaeter($results = array()){
            $collect = array();
            $enablers =  self::enablers();

            foreach ($results as $result) {
                $keys =  explode('_', $result->meta_key);
                if(isset($keys[6])){
                    $id = $keys[0]."_".$keys[1]."_".$keys[2]."_".$keys[3]."_".$keys[4]."_".$keys[5];
                    if($keys[6] == 'name'){
                        $collect[$id]['n'] = $result->meta_value;
                    }elseif ($keys[6] == 'enabler') {
                        $collect[$id]['e'] = $result->meta_value;
                        $collect[$id]['from'] = $keys[3]."_".$keys[4];
                    }elseif ($keys[6] == 'done') {
                        $collect[$id]['d'] = $result->meta_value;
                    }
                }
            }



            foreach ($collect as $key => $item) {                
                if(!isset($item['n']) || trim($item['n']) == '' ||  $item['e'] != $_GET['enabler'] ){
                    unset($collect[$key]);
                }else{
                    //$collect[$key]['e'] = $enablers[$item['e']][0];
                    $collect[$key]['from'] = $enablers[$item['from']][0];
                    $collect[$key]['d'] = isset($item['d']) ? $item['d'] : 0;
                }
            }
            return $collect;
        }

        public function documents_boxs(){
        
            ?>

                <style>
                    .doc-box {
                        width: 50%;
                        display: inline-flex;
                        margin-bottom: 10px;
                        justify-content : center;
                        padding-right :5px

                    }
                    .doc-box a{
                        width: 100%;
                        padding-left:5px;
                        padding-right:5px;
                        text-align: center;
                    }
                </style>
        <?php 
            $boxs = get_field('files') ;
            
            foreach ($boxs as $box) {
                if($box[$_GET['enabler']] == 1){
                    echo '<div class="doc-box"><a class="button" target="_blank" href="'.$box['file']['url'].'" >'.$box['name'].'</a></div>';
                }
            }            
        }
    }
    function run_my_class(){
        global $wp_query;
        return new Unikassel_Projects($wp_query->get_queried_object_id());
    }

    add_action('wp','run_my_class');

    //$groups = acf_get_field_groups(array('post_type' => 'project'));
    //pre( acf_get_fields($groups[3])  , 'sssssssss');
    ?>