<?php

use Kirby\Data\Yaml;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Toolkit\A;

return A::keyBy(A::map(
	Dir::read(dirname(__DIR__) . '/translations'),
	function ($file) {
		$translations = Yaml::decode(F::read(dirname(__DIR__) . '/translations/' . $file));
		$translations['lang'] = F::name($file);

		$pluginTranslations = [];

		foreach ($translations as $key => $value) {
			$pluginTranslations['junohamburg.language-selector.' . $key] = $value;
		}

		return $pluginTranslations;
	}
), 'junohamburg.language-selector.lang');