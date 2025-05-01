<?php

use Kirby\Data\Yaml;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Toolkit\A;

$translations = [];
$root         = dirname(__DIR__) . '/translations';

foreach (Dir::read($root) as $file) {
	$code        = F::name($file);
	$translation = F::read($root . '/' . $file);
	$translation = Yaml::decode($translation);

  // add prefix to keys
	$translations[$code] = array_combine(
    A::map(
      array_keys($translation),
      fn ($key) => 'junohamburg.language-selector.' . $key
    ),
    $translation
  );
}

return $translations;