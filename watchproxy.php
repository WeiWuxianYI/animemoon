<?php
    $furl=trim($_GET["url"]);
    $raw = file_get_contents($furl);
    $raw = str_replace("alert(","isNull(", $raw);
    $raw = str_replace("window.open","isNull", $raw);
    $raw = str_replace("prompt(","isNull(", $raw);
    $raw = str_replace("Confirm: (","isNull(", $raw);
    $raw = preg_replace('!<div\s+id="overlay">.*?</div>!is', '', $raw);
    $replaceThis="<head>";
    $replaceString="<head><base href='".$furl."/'>";
    $raw=str_replace($replaceThis,$replaceString,$raw);
    $raw = preg_replace('/<table style=\"text-align:center;\" width=\"100%\">.*<\/table>/','',$raw);
    echo $raw;
