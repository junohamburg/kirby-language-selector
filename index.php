<?php

use Composer\Semver\Semver;
use Kirby\Cms\App as Kirby;

// Validate Kirby version
if (Semver::satisfies(Kirby::version() ?? '0.0.0', '~5.0') === false) {
  throw new Exception('The language selector plugin requires Kirby 5');
}

load([
  'LanguageSelector' => __DIR__ . '/src/LanguageSelector.php',
]);

Kirby::plugin('junohamburg/language-selector', [
  'options' => [
    'allowDelete' => true,
  ],
  'areas' => [
    'site' => function () {
      if (Kirby::instance()->language() === null) {
        return [];
      }

      return [
        'buttons' => require __DIR__ . '/config/buttons.php',
        'dialogs' => require __DIR__ . '/config/dialogs.php',
      ];
    }
  ],
  'translations' => require __DIR__ . '/config/translations.php',
]);
