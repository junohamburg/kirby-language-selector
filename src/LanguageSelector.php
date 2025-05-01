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

	public function __construct(
		ModelWithContent $model
	) {
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
        'hasTranslation' => $this->model->version('latest')->exists($option['code']),
    ]);
		return array_values($options);
	}

	public function props(): array
	{
		return [
			...ViewButton::props(),
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

        // Create dropdown options
        $options = A::filter(
            $this->languages(),
            fn ($language) => $language['default'] === false
        );

        // Do not show options dropdown if
        // there are no translations for any non-default language
        $optionsWithTranslations = A::filter(
            $options,
            fn ($language) => $language['hasTranslation'] === true
        );

        if (count($optionsWithTranslations) === 0) {
            return [];
        }

        $options = A::map($options, fn ($language) => [
            'click'    => $language['code'],
            'disabled' => $language['hasTranslation'] === false,
            'icon'     => 'trash',
            'text'     => I18n::template(
                'junohamburg.language-selector.delete',
                ['language' => $language['text']]
            ),
        ]);

        return array_values($options);
    }
}
