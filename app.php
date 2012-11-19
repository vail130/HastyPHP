<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
<?

$favicon = '';
$title = '';
$name = '';
$description = '';
$keywords = '';
$url = $SETTINGS['BASE_URL'];
$thumbnail = '';
$appleIcon = '';
$appleIcon72 = '';
$appleIcon114 = '';

?>
  <!-- Basic Page Needs
  ================================================== -->
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
  <title><? echo $title; ?></title>
  <meta name="author" content="<? echo $name; ?>">
  <meta name='description' content='<? echo $description; ?>' />
  <meta name='keywords' content='<? echo $keywords; ?>' />

  <meta property="og:site_name" content="<? echo $name; ?>" />
  <meta property="og:title" content="<? echo $title; ?>" />
  <meta property="og:url" content="<? echo $url; ?>" />
  <meta property="og:image" content="<? echo $thumbnail; ?>" />
  <meta property="og:description" content="<? echo $description; ?>" />

  <meta itemprop="name" content="<? echo $name; ?>">
  <meta itemprop="description" content="<? echo $description; ?>">
  <meta itemprop="image" content="<? echo $thumbnail; ?>">


  <!-- Mobile Specific Metas
 ================================================== -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="HandheldFriendly" content="true" />

  <!-- CSS
 ================================================== -->
  <style type='text/css'>

    <?
    ob_start();

    try {
      lessc::ccompile($SETTINGS['PATH'].'styles/main.less', $SETTINGS['PATH'].'styles/main.css');
    } catch (exception $ex) {
      exit('lessc fatal error:<br />'.$ex->getMessage());
    }

    $css = ob_get_contents();
    ob_end_clean();
    #echo CssMin::minify($css);
    echo $css;

    ?>
  </style>

  <!-- Favicons
  ================================================== -->
  <link rel='shortcut icon' href='<? echo $favicon; ?>' />
  <link rel='icon' href='<? echo $favicon; ?>' />
  <link rel="apple-touch-icon" href="<? echo $appleIcon; ?>">
  <link rel="apple-touch-icon" sizes="72x72" href="<? echo $appleIcon72; ?>">
  <link rel="apple-touch-icon" sizes="114x114" href="<? echo $appleIcon114; ?>">


  <!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

</head>
<body>


<div id="app"></div>

<script type='text/javascript'>

  /*
   window.bootstrappedData = '<?PHP echo json_encode(Item::getRecords()); ?>';
   window.bootstrappedTags = '<?PHP echo json_encode(ItemTag::getUniqueRecords()); ?>';
   window.bootstrappedSession = '<?PHP echo json_encode($_SESSION); ?>';
   */

<?

$min = '';
ob_start();

if(file_exists("{$SETTINGS['PATH']}scripts/main.js")) {
  require("{$SETTINGS['PATH']}scripts/main.js");
}

$min = ob_get_contents();
ob_end_clean();

#echo JSMin::minify($min);
echo $min;

?>
</script>

</body>
</html>
