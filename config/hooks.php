<?php

use Kirby\Http\Response;
use Kirby\Toolkit\Str;

return [
  // Extend panel fiber response
  'panel.route:after' => function ($route, $path, $method, $response) {
    if (!$this->multilang() || $method !== 'GET') return $response;

    // Find route target for ...
    $pattern = $route->attributes()['pattern'];
    $target = null;

    // Site
    if ($pattern == 'site') {
      $target = $this->site();
    }
    // Page
    elseif ($pattern == 'pages/(:any)') {
      $id = $route->arguments()[0];
      $id = Str::replace($id, '+', '/');
      $target = $this->page($id);
    }
    // File
    elseif ($pattern == 'pages/(:any)/files/(:any)') {
      $id = $route->arguments()[0];
      $id = Str::replace($id, '+', '/');
      $page = $this->page($id);

      $filename = $route->arguments()[1];
      $target = $page->file($filename);
    }
    // User
    elseif ($pattern == 'users/(:any)') {
      $id = $route->arguments()[0];
      $target = $this->user($id);
    }
    // Account
    elseif ($pattern == 'account') {
      $target = $this->user();
    }

    if (!$target) return $response;

    // Use regex to modify $languages array in fiber response,
    // Reference: https://github.com/getkirby/kirby/blob/main/src/Panel/View.php
    $searchPattern = '/(?<="\$languages":).*(?=,"\$menu")/';

    $modifiedBody = preg_replace_callback($searchPattern, function ($match) use ($target) {
      $languages = json_decode($match[0], true);

      // Check if translation exists for each language, update languages array
      foreach ($languages as &$language) {
        $translation = $target->translation($language['code']);
        if (!$translation) continue;

        $language['hasTranslation'] = $translation->exists();
      }

      return json_encode($languages);
    }, $response->body());

    // Add plugin options to $config
    $searchPattern = '/(?<="\$config":).*(?=,"\$system")/';

    $modifiedBody = preg_replace_callback($searchPattern, function ($match) use ($target) {
      $config = json_decode($match[0], true);

      $config['languageSelector'] = [
        'allowDelete' => $this->option('junohamburg.language-selector.allowDelete'),
      ];

      return json_encode($config);
    }, $modifiedBody);

    // Return new response with modified body
    return new Response(
      $modifiedBody,
      $response->type(),
      $response->code(),
      $response->headers(),
      $response->charset()
    );
  }
];
