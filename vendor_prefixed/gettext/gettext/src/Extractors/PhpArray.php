<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 14-November-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityView\Gettext\Extractors;

use BadMethodCallException;
use GravityKit\GravityView\Gettext\Translations;
use GravityKit\GravityView\Gettext\Utils\MultidimensionalArrayTrait;

/**
 * Class to get gettext strings from php files returning arrays.
 */
class PhpArray extends Extractor implements ExtractorInterface
{
    use MultidimensionalArrayTrait;

    /**
     * {@inheritdoc}
     */
    public static function fromFile($file, Translations $translations, array $options = [])
    {
        foreach (static::getFiles($file) as $file) {
            static::fromArray(include($file), $translations);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        throw new BadMethodCallException('PhpArray::fromString() cannot be called. Use PhpArray::fromFile()');
    }
}
