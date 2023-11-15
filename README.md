# Kirby Language Selector

This plugin for **Kirby 4** replaces the default language dropdown with a customized version. It displays the translation state of each language and adds a dropdown for deleting translations.

![Language Selector in Kirby panel]()

![Language Selector states with dropdown]()<br>
Left to right: No translations, some translations, dropdown.

Please note: On small viewports, the default language dropdown is displayed.

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby-language-selector`.

### Git submodule

```
git submodule add https://github.com/junohamburg/kirby-language-selector.git site/plugins/kirby-language-selector
```

### Composer

```
composer require junohamburg/kirby-language-selector
```

## Setup

Install the plugin in a multi-language Kirby site.

## Plugin Translations

For now, the dialog text and tooltips are only translated to English and German. Feel free to add a pull request with a new yml translation file in [this folder](https://github.com/junohamburg/kirby-language-selector/tree/main/translations).

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/junohamburg/kirby-language-selector/issues/new).

## Similar plugins

[https://github.com/Daandelange/k3-translations](https://github.com/Daandelange/k3-translations)<br>
[https://github.com/doldenroller/k3-translation-status](https://github.com/doldenroller/k3-translation-status)<br>
[https://github.com/sietseveenman/kirby3-language-sync](https://github.com/sietseveenman/kirby3-language-sync)<br>

## License

MIT

## Credits

- [JUNO](https://juno-hamburg.com)
