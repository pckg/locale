<?php

namespace Pckg\Locale\Console;

use Exception;
use Pckg\Framework\Console\Command;
use Pckg\Locale\Record\Language;
use Symfony\Component\Console\Input\InputArgument;

class PrepareLocalizer extends Command
{

    protected function configure()
    {
        $this->setName('localizer:prepare')
             ->setDescription('Prepare frontend languages')
             ->addArguments([
                                'language' => 'Platforms language',
                            ], InputArgument::REQUIRED);
    }

    public function handle()
    {
        /**
         * Prepare languages to import.
         */
        $toImport = collect(['en', $this->argument('language')])
            ->removeEmpty()
            ->unique()
            ->all();

        /**
         * Currently supported languages.
         */
        $locales = [
            'en' => 'en_GB',
            'sl' => 'sl_SI',
            'hr' => 'hr_HR',
        ];
        $languages = [
            'en' => [
                'en' => 'English',
                'sl' => 'Angleščina',
                'hr' => 'Engleski',
            ],
            'sl' => [
                'en' => 'Slovenian',
                'sl' => 'Slovenski',
                'hr' => 'Slovenski',
            ],
            'hr' => [
                'en' => 'Croatian',
                'sl' => 'Hrvaški',
                'hr' => 'Hrvatski',
            ],
        ];

        /**
         * Check if language is supported.
         */
        foreach ($toImport as $lang) {
            if (array_key_exists($lang, $languages)) {
                continue;
            }

            throw new Exception('Language ' . $lang . ' is not supported');
        }

        /**
         * Create final translations matrix.
         */
        $finalLanguages = [];
        foreach ($toImport as $lang) {
            foreach ($toImport as $lang2) {
                $finalLanguages[$lang][$lang2] = $languages[$lang][$lang2];
            }
        }

        /**
         * Import languages and translations.
         */
        $first = true;
        $only = count($toImport) == 1;
        foreach ($finalLanguages as $langCode => $translations) {
            $language = Language::getOrCreate(['slug' => $langCode]);
            $language->setAndSave([
                                      'frontend' => $only || !$first,
                                      'backend'  => $first,
                                      'default'  => $only || !$first,
                                      'locale'   => $locales[$langCode],
                                  ]);

            $first = false;
        }
        foreach ($finalLanguages as $langCode => $translations) {
            $language = Language::getOrCreate(['slug' => $langCode]);
            foreach ($translations as $transCode => $translation) {
                runInLocale(function () use ($language, $translation) {
                    $language->setAndSave([
                                              'title' => $translation,
                                          ]);
                }, $transCode);
            }
        }

        $this->output('Done');
    }
}
