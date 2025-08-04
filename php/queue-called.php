<?php
header('Content-Type: application/json');
require_once 'db.php';

$types = ['S', 'CI', 'FC'];
$out = [];
$latest = null;
$latestTime = 0;

foreach ($types as $t) {
  $path = __DIR__ . "/../tv-trigger/{$t}.txt";
  if (is_readable($path)) {
    $value = trim(file_get_contents($path));
    $out[$t] = $value;

    $mtime = filemtime($path);
    if ($mtime > $latestTime) {
      $latestTime = $mtime;
      $latest = [
        'type' => $t,
        'number' => $value
      ];
    }
  } else {
    $out[$t] = null;
  }
}

$out['latest'] = $latest;

echo json_encode($out);
