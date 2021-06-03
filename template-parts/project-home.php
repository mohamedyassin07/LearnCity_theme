<style>
    .project{
        background-color: #F5F5F5 ;
        margin-bottom: 10px;
        padding: 10px;
    }
    .project h3{
        font-weight: bold ;
    }
    .project >p >a{
        padding-right: 10px;
        font-size: 18px;
    } 
</style>

<?php
    $projects = $args['projects'];
    if(!empty($projects)){
        $create_new_project_text =  __('Create A New Project' ,'uni');
        $html =  '<h2>'.__('Current Projects' ,'uni').'</h2>';
        foreach ($projects as $key => $project) {
            $link = get_permalink($project->ID);
            $factors = Unikassel_Projects::project_factors($project->ID);
            
            //$html .= '<li>'.$project->post_title.' : <a href="'.$link.'">'.__('Edit','uni').'</a> || <a href="'.$link.'">'.__('Reports','uni').'</a> </li>';
            $html .= '<div class="project">';
            $html .= '<h3><a href="'.$link.'?step=info'.'">'.$project->post_title.'</a></h3>';
            // $html .= '<div class="project"><h4><a href="'.$link.'">'.$project->post_title.'</a></h4></div>';
            if(!empty($factors)){
                $html .= '<p>'.__('Reports : ');
                foreach ($factors as $key => $factor) {
                    $html .= '<a href="'.$link.'?report='.Unikassel_Projects::str_key($factor['name']).'">'.$factor['name'] .',</a>'; 
                }
                $html .= '</p>';    
            }
            
            $html .= '</div>';
        }
        $html .=  '';
    }else {
        $create_new_project_text =  __('Create Your First Project' ,'uni');
        $html ='';
    }

?>
<p style="text-align: center;"><span style="font-size: 24pt;"><a href="<?= $args['url'].'?step=info'; ?>"><?= $create_new_project_text; ?></a></span></p>
<?= $html; ?>