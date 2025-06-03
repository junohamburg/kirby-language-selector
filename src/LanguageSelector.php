<?php

use Kirby\Cms\App;
use Kirby\Cms\ModelWithContent;
use Kirby\Panel\Ui\Buttons\LanguagesDropdown;
use Kirby\Panel\Ui\Buttons\ViewButton;
use Kirby\Toolkit\A;
use Kirby\Toolkit\I18n;

class LanguageSelector extends ViewButton
{
  protected App $kirby;

  public function __construct(ModelWithContent $model) {
    $this->kirby = $model->kirby();

    parent::__construct(
      component: 'k-language-selector',
      model:     $model,
    );
  }

  protected function languages(): array
  {
    $dropdown = new LanguagesDropdown($this->model);
    $options  = $dropdown->options();
    $options  = A::filter($options, fn ($option) => $option !== '-');
    $options  = A::map($options, fn ($option) => [
      ...$option,
      'theme' => $this->theme($option),
      'title' => $this->title($option),
    ]);
    return array_values($options);
  }

  public function props(): array
  {
    return [
      ...parent::props(),
      'dropdown'  => $this->model->panel()->url(true) . '/languages',
      'language'  => $this->kirby->language()->toArray(),
      'languages' => $this->languages(),
      'options'   => $this->options(),
    ];
  }

  /**
  * Returns the options for the translations delete dropdown
  */
  protected function options(): array
  {
    if ($this->kirby->option('junohamburg.language-selector.allowDelete') !== true) {
      return [];
    }

    $version = $this->model->version('latest');

    // Create dropdown options
    $options = A::filter(
      $this->languages(),
      fn ($language) => $language['default'] === false
    );

    $options = A::map($options, fn ($language) => [
      'click'    => $language['code'],
      'disabled' => $version->exists($language['code']) === false,
      'icon'     => 'trash',
      'text'     => I18n::template(
        'junohamburg.language-selector.delete',
        ['language' => $language['text']]
      ),
    ]);

    // Do not show options dropdown if
    // there are no translations for any non-default language
   $enabled = A::filter(
      $options,
      fn ($language) => $language['disabled'] === false
    );

    if (count($enabled) === 0) {
      return [];
    }

    return array_values($options);
  }

  /**
  * Returns the theme for a language button
  */
  protected function theme(array $language): string|null
  {
    if ($language['code'] === $this->kirby->language()->code()) {
      return 'dark';
    }

    if ($this->model->version('latest')->exists($language['code']) === false) {
      return 'empty';
    }

    return null;
  }

  /**
  * Returns the title for a language button
  */
  protected function title(array $language): string
  {
    if ($this->model->version('latest')->exists($language['code']) === false) {
      return I18n::template(
        'junohamburg.language-selector.empty',
        ['language' => $language['text']]
      );
    }

    return $language['text'];
  }
}
