<div class= 'guidelines-chart-div'>



<style>
    .guidelines-chart-div{
        display: grid; 
        margin: auto; 
        width: 420px; 
        height: <?= $args['width'] ?>px; 
    }
    fieldset{
        border: 6px solid transparent; 
        border-top-color: black; 
        box-sizing: border-box; 
        grid-area: 1 / 1; 
        padding: 20px; 
        width: <?= $args['width'] ?>px; 
    }
    legend a{
        background-color: black;
        padding : 3px;
        color : white;
        margin-right: 5px;
        font-size: 15px ;
    }

    .d2{ grid-area: 1 / 2; }

    fieldset:nth-of-type(2){ transform: rotate(90deg); }
    fieldset:nth-of-type(3){ transform: rotate(180deg); }
    fieldset:nth-of-type(4){ transform: rotate(-90deg); }
    fieldset:nth-of-type(6){ transform: rotate(90deg); }
    fieldset:nth-of-type(7){ transform: rotate(180deg); }
    fieldset:nth-of-type(8){ transform: rotate(-90deg); }

    legend{
        font: 9pt/0 'Averia Serif Libre';
        padding: 0 4px; 
    }

    .d1 legend{ margin-left: auto;}
    fieldset:nth-of-type(3)>legend{ transform: rotate(180deg); } 
    fieldset:nth-of-type(7)>legend{ transform: rotate(180deg); } 

    .guidelines-chart-div {
        user-select: none;
        -webkit-user-select: none;
    }
</style>



<fieldset class=d1><legend><?= $args['directions'][0] ?></legend></fieldset>
<fieldset class=d1><legend><?= $args['directions'][1] ?></legend></fieldset>
<fieldset class=d1><legend><?= $args['directions'][2] ?></legend></fieldset>
<fieldset class=d1><legend><?= $args['directions'][3] ?></legend></fieldset>


</div>