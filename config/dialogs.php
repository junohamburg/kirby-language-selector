<?php

use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Cms\Find;
use Kirby\Cms\ModelWithContent;
use Kirby\Cms\Page;
use Kirby\Cms\Site;
use Kirby\Cms\User;
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
          'title'    => match (true) {
            $model instanceof Site => $model->title(),
            $model instanceof Page => $model->title(),
            $model instanceof File => $model->filename(),
            $model instanceof User => $model->name() ?? $model->email(),
            default => throw new Exception('Invalid model type'),
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
  'page.translation.delete' => $forModel = [
    'pattern' => '(pages/.*?)/translation/(:any)',
    'load'    => fn (string $model, string $language) =>
      $load(model: Find::parent($model), language: $language),
    'submit'  => fn (string $model, string $language) =>
      $submit(model: Find::parent($model), language: $language)
  ],
  'page.file.translation.delete' => $forFile = [
    'pattern' => '(pages/.*?)/files/(:any)/translation/(:any)',
    'load'    => fn (string $model, string $filename, string $language) =>
      $load(model: Find::file($model, $filename), language: $language),
    'submit'  => fn (string $model, string $filename, string $language) =>
      $submit(model: Find::file($model, $filename), language: $language)
  ],
  'site.translation.delete' => [
    ...$forModel,
    'pattern' => '(site)/translation/(:any)'
  ],
  'site.file.translation.delete' => [
    ...$forFile,
    'pattern' => '(site)/files/(:any)/translation/(:any)'
  ],
  'user.translation.delete' => [
    ...$forModel,
    'pattern' => '(users/.*?)/translation/(:any)'
  ],
  'user.file.translation.delete' => [
    ...$forFile,
    'pattern' => '(users/.*?)/files/(:any)/translation/(:any)'
  ],
  'account.translation.delete' => [
    ...$forModel,
    'pattern' => '(account)/translation/(:any)'
  ],
  'account.file.translation.delete' => [
    ...$forFile,
    'pattern' => '(account)/files/(:any)/translation/(:any)'
  ]
];