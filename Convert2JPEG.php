<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>untitled</title>
	<link rel="stylesheet" type="text/css" href="./Convert2JPEG.css" />
</head>
<body>
<?php
// $file: Convert2JPEG.php $timestamp: 2012/09/22 @ 12:48

// For Platypus $argv contains:
// [0] - Absolute to the running script
// [1...n] - Absolute path to each dropped file
// var_dump($argv);


// find the files dropped onto the Platypus app icon:
$droppedFiles = array_slice($argv,1);

// Correct type images for sips
$accepted = array('jpeg', 'jpg', 'tiff', 'tif', 'png', 'gif', 'jp2', 'pict', 'bmp', 'qtif', 'psd', 'sgi', 'tga', 'pdf');
define('WIDTH_HEIGHT', 1024);
define('QUALITY', 'high');

if (!empty($droppedFiles)) {
  // echo '<h1>This application will convert:</h1>';
  // echo '<ul>';
  // foreach($accepted as $extension) {
  //   echo '<li>*.' . $extension . '</li>';
  // }
  // echo '</ul>';
  echo '<strong>Image(s) in progress...</strong></br />';
  $results = array();
  foreach ($droppedFiles as $image) {
    $infos = pathinfo($image);
    if(!in_array($infos['extension'], $accepted)) {
      $results[]['file']  = $infos['basename'];
      $results[]['error'] = true;
    } else {
      exec('/usr/bin/sips --getProperty all "' . $image . '"', $tmp);
      $properties = array();
      foreach ($tmp as $key => $value) {
        if($key == 0 ) { $properties['path'] = $value; }
        else {
          $foo = explode(': ', $value);
          $properties[trim($foo[0])] = trim($foo[1]);
        }
      }
      echo '<h5>Creating Jpeg from ' .  $infos["basename"] . '</h5>';
      echo '<pre>';
      foreach($properties as $key => $propterty) {
        echo '<b>' . $key . ':</b> ' . $propterty . '<br />';
      }
      echo '<b>result:</b> ' . SipsCommand($image, $infos);
      echo '</pre>';
      echo '<hr />';
      $results[]['file']  = $infos['filename'] . '.jpg';
      $results[]['error'] = false;
    }
  }
  
  echo '<table width=100%>';
  echo '<tr><th scope="col">#id</th><th scope="col">File</th><th scope="col">Result</th></tr>';
  foreach ($results as $key => $result) {
    echo '<tr>';
    echo '<td>' . $key . '</td>';
    echo '<td>' . $result['file'] . '</td>';
    echo '<td>√</td>';
    echo '</tr>';
  }
  echo '</table>';
  // var_dump($results);
  
  
} else {
  echo '<p class="warning">Please drag\'n drop images or Menu » File » Open...</p>';
  exit;
}

function SipsCommand($path, $infos) {
  // https://developer.apple.com/library/mac/#documentation/Darwin/Reference/ManPages/man1/sips.1.html
  $cmd = 'sips -s format jpeg --resampleHeightWidthMax ' . WIDTH_HEIGHT
         . ' -m "./sRGB.icc" --deleteColorManagementProperties'
         . ' -s formatOptions ' . QUALITY  . ' '
         . '"' . escapeshellcmd($path) . '" '
         . ' --out "' . $infos['dirname'] . '/' . $infos['filename'] . '.jpg"';
         
  // return echo $cmd . PHP_EOL;
  return exec($cmd);
}

?>
</body>
</html>