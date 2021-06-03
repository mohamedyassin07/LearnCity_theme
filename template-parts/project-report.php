<?php 
    //pre($args);
    // global $post; 
    // $ID = get_the_ID();
    // $post = get_post( $ID, OBJECT );
    //     setup_postdata( $post );



?>
<style>
    fieldset.scheduler-border {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 0 0 1.5em 0 !important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
}

    legend.scheduler-border {
        font-size: 1.2em !important;
        font-weight: bold !important;
        text-align: left !important;
        width:auto;
        padding:0 10px;
        border-bottom:none;
    }
    .scheduler-border div{
        white-space: pre-line;

    }
    .left-border{
        box-shadow: 10px 0 5px -10px #888;
    }

</style>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


<div class="row">
<h1><?= ucfirst(get_the_title()) . ' > ' . str_replace('_', ' ', ucfirst($_GET['report']))     ?></h1> 

</div>
<div class="row" id='report-div'>
    <div class="col-5">
        <div>
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Orgnisation</legend>
                <div class="row">
                    <div class="col-6 left-border">
                        <h6>Profits for the organization</h6>
                        <?= $args['orgnisation']['profits_for_the_organization'] ?>
                    </div>
                    <div class="col-6">
                        <h6>Tasks for the organization</h6>
                        <?= $args['orgnisation']['tasks_for_the_organization'] ?>
                    </div>
                </div>
            </fieldset>
        </div>
        <div>
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Team</legend>
                <div class="row">
                    <div class="col-6 left-border">
                        <h6>Profits for the team</h6>
                        <?= $args['team']['profits_for_the_team'] ?>
                    </div>
                    <div class="col-6">
                        <h6>Team preparation</h6>
                        <?= $args['team']['team_preparation'] ?>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="col-4">
        <div class="col-12">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Process Management</legend>
                <?= $args['process']['neue__angepsste_prozesse'] ?>
            </fieldset>
        </div>
        <div class="col-12">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Contractual Management</legend>
                <?= $args['contractual']['new_agreements'] ?>
            </fieldset>
        </div>

        <div class="col-12">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Knowladage Management</legend>
                <?= $args['knowladage']['new__angepsste_communication_channels'] ?>
            </fieldset>
        </div>

    </div>

    <div class="col-3">
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Projektdokumentationen</legend>
            <div class="col-12">
            <?php 
            $rows =  $args['Projektdokumentationen']['table'];
            //pre($rows);
            foreach ($rows as $row) {
                echo "<p>".$row['t'].' : ' . $row['a'] ."</p>";
            }

          ?>









  









            </div>
        </fieldset>
    </div>
</div>

<a hre='#' id='print'></a>
<script>
    $("#report-div").printElement();
</script>

<?php ?>