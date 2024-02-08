<?php

use Composer\Semver\Semver;
use Kirby\Cms\App as Kirby;
use Kirby\Data\Yaml;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Toolkit\A;

// Validate Kirby version
if (Semver::satisfies(Kirby::version() ?? '0.0.0', '~4.0') === false) {
  throw new Exception('The language selector plugin requires Kirby 4');
}

Kirby::plugin('junohamburg/language-selector', [
  'options' => [
    'allowDelete' => true,
  ],
  'hooks' => require __DIR__ . '/config/hooks.php',
  'api' => require __DIR__ . '/config/api.php',
  'translations' => A::keyBy(A::map(
    Dir::read(__DIR__ . '/translations'),
    function ($file) {
      $translations = Yaml::decode(F::read(__DIR__ . '/translations/' . $file));
      $translations['lang'] = F::name($file);

      // Add plugin prefix to translations
      $pluginTranslations = [];

      foreach ($translations as $key => $value) {
        $pluginTranslations['junohamburg.language-selector.' . $key] = $value;
      }

      return $pluginTranslations;
    }
  ), 'junohamburg.language-selector.lang')
]);
