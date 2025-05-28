<?php

use Kirby\Cms\ModelWithContent;

return [
  'languages' => fn (ModelWithContent $model) => new LanguageSelector($model),
];