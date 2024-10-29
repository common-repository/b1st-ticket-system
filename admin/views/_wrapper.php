<?php
?>
<style>
    .bootstrap .container {
        margin-top: 30px;
        background-color: <?php echo $config->backgroundcolor ?>;
        color: <?php echo $config->inputtextcolor ?>;
        padding: 10px;
    }
     .bootstrap .container-fluid {
        margin-top: 30px;
        background-color: <?php echo $config->backgroundcolor ?>;
        color: <?php echo $config->inputtextcolor ?>;
        padding: 10px;
    }

    .test {
        background-color: <?php echo $config->loadercolor ?>;
    }

    .bootstrap input, .bootstrap textarea, .bootstrap select, .bootstrap button {
        background-color: <?php echo $config->inputcolor ?>;
        color: <?php echo $config->inputtextcolor ?>;
    }

    .bootstrap h3, .bootstrap .contact-result, .bootstrap th {
        color: <?php echo $config->titlecolor ?>;
    }

    .bootstrap legend {
        color: <?php echo $config->titlecolor ?>;
    }

    .bootstrap .breadcrumb {
        background-color: <?php echo $config->inputcolor ?>;
        color: <?php echo $config->inputtextcolor ?>;
    }

    .bootstrap .breadcrumb a {
        color: <?php echo $config->inputtextcolor ?>;
        text-shadow: none;
    }

    .bootstrap textarea {
        box-sizing: border-box; /* For IE and modern versions of Chrome */
        -moz-box-sizing: border-box; /* For Firefox                          */
        -webkit-box-sizing: border-box;
        resize: none;
        width: 100%;
    }
    .bootstrap legend {
        color: <?php echo $config->titlecolor ?>;
    }

    .bootstrap .opened {
        display: block;
        width: 10px;

        transform: rotate(135deg);
        transform-origin: center center 0;
    }

    .bootstrap #uploadFrame {
        display: none;
    }


    .bootstrap .description {
        border-bottom: 1px solid white;
    }

    .bootstrap .description2 {
        border-bottom: 1px solid white;
        padding:6px 0;
    }

    .bootstrap .description div {
        height: 34px;
        line-height: 34px;
        text-align: center;
    }
	.avgrund-popin {
		background-color: <?php echo $config->backgroundcolor ?> !important;
		color: <?php echo $config->inputtextcolor ?> !important;
	}
</style>