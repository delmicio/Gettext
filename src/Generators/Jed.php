<?php

namespace Gettext\Generators;

use Gettext\Translations;

class Jed extends Generator implements GeneratorInterface
{
    public static $options = JSON_PRETTY_PRINT;

    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations)
    {
        $domain = $translations->getDomain() ?: 'messages';

        return json_encode([
            $domain => [
                '' => [
                    'domain' => $domain,
                    'lang' => $translations->getLanguage() ?: 'en',
                    'plural-forms' => $translations->getHeader('Plural-Forms') ?: 'nplurals=2; plural=(n != 1);',
                ]
            ] + self::buildMessages($translations)
        ], static::$options);
    }

    /**
     * Generates an array with all translations.
     * 
     * @param Translations $translations
     *
     * @return array
     */
    private static function buildMessages(Translations $translations)
    {
        $pluralForm = $translations->getPluralForms();
        $pluralLimit = is_array($pluralForm) ? ($pluralForm[0] - 1) : null;
        $messages = [];
        $context_glue = '\u0004';

        foreach ($translations as $translation) {
            $key = ($translation->hasContext() ? $translation->getContext().$context_glue : '').$translation->getOriginal();

            if ($translation->hasPluralTranslations()) {
                $message = $translation->getPluralTranslations($pluralLimit);
                array_unshift($message, $translation->getTranslation());
            } else {
                $message = [$translation->getTranslation()];
            }

            $messages[$key] = $message;
        }

        return $messages;
    }
}
