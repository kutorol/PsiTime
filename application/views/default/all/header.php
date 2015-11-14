<!DOCTYPE html>
<html lang="<?=$segment?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?=strip_tags(stripcslashes($title))?></title>

    <!-- Bootstrap -->
    <link href="<?=base_url()?>css/bootstrap.css" rel="stylesheet">
    <link href="<?=base_url()?>css/style.css" rel="stylesheet">
    <!--Преобразуем стандартный select-->
    <link href="<?=base_url()?>css/bootstrap-select.min.css" rel="stylesheet">

    <?php if(isset($attachUploadSripts)):?>
        <link href="<?=base_url()?>js/upload/jquery.fileupload.css" rel="stylesheet" />
    <?php endif;?>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <?php if(isset($useCheckbox)):?>
        <link rel="stylesheet" href="<?=base_url()?>css/bootstrap-checkbox.css">
    <?php endif;?>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

    <!--MULTI TAGS INPUT JQUERY-->
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css">
    <link href="<?=base_url()?>css/jquery.tagit.css" rel="stylesheet" type="text/css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->



</head>

<body>

<div id="prel" style="display: none">
    <div id="preloader_3"></div>
</div>
<script>
    var base_url = '<?=$startUrl?>';
    var base_url_start = '<?=base_url();?>';
    //json translate
    var jsLang = JSON.parse('<?=json_encode($js)?>');
    //html response
    var jsLangAdditional = "<?=$languages_desc[0]['titleErrorMessage'][$segment]?>";
</script>