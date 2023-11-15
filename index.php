<?php

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
