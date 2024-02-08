<?php

use Kirby\Filesystem\F;
use Kirby\Toolkit\Str;

return [
  // Routes for deleting translations
  'routes' => function ($kirby) {
    return [
      // Site
      [
        'pattern' => 'translation/delete/site',
        'method' => 'DELETE',
        'action'  => function () use ($kirby) {
          $language = $kirby->request()->body()->toArray()['language'];
          $site = $kirby->site();

          $contentFile = $site->translation($language)->contentFile();
          F::remove($contentFile);

          return true;
        }
      ],
      // Page
      [
        'pattern' => 'translation/delete/pages/(:any)',
        'method' => 'DELETE',
        'action'  => function ($id) use ($kirby) {
          $language = $kirby->request()->body()->toArray()['language'];
          $id = Str::replace($id, '+', '/');
          $page = $kirby->page($id);

          $contentFile = $page->translation($language)->contentFile();
          F::remove($contentFile);

          return true;
        }
      ],
      // File
      [
        'pattern' => 'translation/delete/pages/(:any)/files/(:any)',
        'method' => 'DELETE',
        'action'  => function ($id, $filename) use ($kirby) {
          $language = $kirby->request()->body()->toArray()['language'];
          $id = Str::replace($id, '+', '/');
          $page = $this->page($id);

          $file = $page->file($filename);

          $contentFile = $file->translation($language)->contentFile();
          F::remove($contentFile);

          return true;
        }
      ],
      // User
      [
        'pattern' => 'translation/delete/users/(:any)',
        'method' => 'DELETE',
        'action'  => function ($id) use ($kirby) {
          $language = $kirby->request()->body()->toArray()['language'];
          $user = $this->user($id);

          $contentFile = $user->translation($language)->contentFile();
          F::remove($contentFile);

          return true;
        }
      ],
      // Account
      [
        'pattern' => 'translation/delete/account',
        'method' => 'DELETE',
        'action'  => function () use ($kirby) {
          $language = $kirby->request()->body()->toArray()['language'];
          $user = $this->user();

          $contentFile = $user->translation($language)->contentFile();
          F::remove($contentFile);

          return true;
        }
      ]
    ];
  }
];
