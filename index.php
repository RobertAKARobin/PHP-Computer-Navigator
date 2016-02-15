<?php
  $ip = $_SERVER["REMOTE_ADDR"];
  $log = "suspicious.txt";

  try{
    if($ip !== "::1" && $ip !== "127.0.0.1"){
      throw new Exception('');
    }
  }catch (Exception $e){
    echo "You're not authorized to be on my server! I've logged your IP address.";
    if(filesize($log) < 1 * 1024){
      $data = $ip . "," . $_SERVER["REQUEST_TIME"];
      file_put_contents($log, $data . PHP_EOL, FILE_APPEND);
    }
    http_response_code(403);
    die();
  }

  $path = preg_replace("/\/$/", "", $_SERVER["REQUEST_URI"]);
  if(is_dir($path)){
    $entries = scandir($path);
    $output = "";
    for($x = 0; $x < count($entries); $x++){
      $entry	= $entries[$x];
      $output .= "<li><a href='http://localhost$path/$entry'>$entry</a></li>\r\n";
    }
  }else{
    $info = pathinfo(parse_url($path)["path"]);
    $dir = $info["dirname"];
    $extension = $info["extension"];
    $filename = $info["basename"];
    $mimes = array(
      "html"  => "text/html",
      "php"   => "text/html",
      "md"    => "text/markdown",
      "css"   => "text/css",
      "jpg"   => "image/jpeg",
      "jpeg"  => "image/jpeg",
      "gif"   => "image/gif",
      "png"   => "image/png",
      "js"    => "application/javascript",
      "json"  => "application/json"
    );
    if($extension === "php"){
      chdir($dir);
      require($filename);
      die();
    }else if(!file_exists("$dir/$filename")){
      http_response_code(404);
      die("404");
    }else if(array_key_exists($extension, $mimes)){
      header("Content-Type: $mimes[$extension]");
    }else{
      header("Content-Type: text/plain");
    }
    die(file_get_contents($path));
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Robin's Localhost</title>
    <style>
      *
      {
      font-family:monospace;
      }
      li
      {
      font-size:18px;
      line-height:1.4em;
      }
    </style>
  </head>
  <body>

    <?php
      echo "<h1><a href='/'>Main</a> &mdash; Dir: $path</h1>";
    ?>

    <ol>
      <?php
        echo $output;
      ?>
    </ol>

  </body>
</html>
