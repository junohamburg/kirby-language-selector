<?php

use Kirby\Cms\App;
use Kirby\Cms\Find;
use Kirby\Cms\ModelWithContent;
use Kirby\Toolkit\Escape;
use Kirby\Toolkit\I18n;

// Shared dialog callback functions
$load = function (ModelWithContent $model, string $language) {
  $kirby    = App::instance();
  $language = $kirby->language($language);

  return [
    'component' => 'k-remove-dialog',
    'props' => [
      'text' => I18n::template(
        'junohamburg.language-selector.' . $model::CLASS_ALIAS . '.delete.confirm',
        [
          'language' => Escape::html($language->name()),
          'title'    => match ($model::CLASS_ALIAS) {
            'file'  => $model->filename(),
            'user'  => $model->name() ?? $model->email(),
            default => $model->title(),
          },
        ]
      )
    ]
  ];
};

$submit = function (ModelWithContent $model, string $language) {
  $model->version('latest')->delete($language);

  return [
    'redirect' => $model->panel()->url(true)
  ];
};

// Dialog routes
return [
  'page.translation.delete' => [
    'pattern' => '(pages/.*?)/translation/(:any)',
    'load'    => $loadModel = fn (string $model, string $language) => $load(model: Find::parent($model), language: $language),
    'submit'  => $submitModel = fn (string $model, string $language) => $submit(model: Find::parent($model), language: $language)
  ],
  'page.file.translation.delete' => [
    'pattern' => '(pages/.*?)/files/(:any)/translation/(:any)',
    'load'    => $loadFile = fn (string $model, string $filename, string $language) =>
      $load(model: Find::file($model, $filename), language: $language),
    'submit'  => $submitFile = fn (string $model, string $filename, string $language) =>
      $submit(model: Find::file($model, $filename), language: $language)
  ],
  'site.translation.delete' => [
    'pattern' => '(site)/translation/(:any)',
    'load'    => $loadModel,
    'submit'  => $submitModel
  ],
  'site.file.translation.delete' => [
    'pattern' => '(site)/files/(:any)/translation/(:any)',
    'load'    => $loadFile,
    'submit'  => $submitFile
  ],
  'user.translation.delete' => [
    'pattern' => '(users/.*?)/translation/(:any)',
    'load'    => $loadModel,
    'submit'  => $submitModel
  ],
  'user.file.translation.delete' => [
    'pattern' => '(users/.*?)/files/(:any)/translation/(:any)',
    'load'    => $loadFile,
    'submit'  => $submitFile
  ],
  'account.translation.delete' => [
    'pattern' => '(account)/translation/(:any)',
    'load'    => $loadModel,
    'submit'  => $submitModel
  ],
  'account.file.translation.delete' => [
    'pattern' => '(account)/files/(:any)/translation/(:any)',
    'load'    => $loadFile,
    'submit'  => $submitFile
  ]
];