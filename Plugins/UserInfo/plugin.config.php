<?php

declare(strict_types=1);

use Laminas\I18n\Translator\Loader\PhpArray;

return [
	'translator' => [
		'translation_file_patterns' => [
			[
				'type' => PhpArray::class,
				'filename' => 'en_US.php',
				'base_dir' => __DIR__ . '/../langs',
				'pattern'  => '%s.php',
			],
		],
		'translation_files' => [
			[
				'type' => PhpArray::class,
				'filename' => __DIR__ . '/../langs/en_US.php'
			],
		],
	],
];
