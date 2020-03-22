<?php declare(strict_types=1);

namespace Swoft\Console\Advanced\Formatter;

use Swoft\Console\Advanced\MessageFormatter;
use Swoft\Console\Helper\Show;
use Swoft\Stdlib\Helper\Str;
use function array_merge;
use function ceil;
use function str_pad;

/**
 * Class Title
 * @package Swoft\Console\Advanced\Formatter
 */
class Title extends MessageFormatter
{
    /**
     * @param string $title The title text
     * @param array  $opts
     */
    public static function show(string $title, array $opts = []): void
    {
        $opts = array_merge([
            'width'      => 80,
            'char'       => self::CHAR_EQUAL,
            'titlePos'   => self::POS_LEFT,
            'indent'     => 2,
            'showBorder' => true,
        ], $opts);

        // list($sW, $sH) = Helper::getScreenSize();
        $width     = (int)$opts['width'];
        $char      = trim($opts['char']);
        $indent    = (int)$opts['indent'] >= 0 ? $opts['indent'] : 2;
        $indentStr = Str::pad(self::CHAR_SPACE, $indent, self::CHAR_SPACE);

        $title   = ucwords(trim($title));
        $tLength = Str::len($title);
        $width   = $width > 10 ? $width : 80;

        // title position
        if ($tLength >= $width) {
            $titleIndent = Str::pad(self::CHAR_SPACE, $indent, self::CHAR_SPACE);
        } elseif ($opts['titlePos'] === self::POS_RIGHT) {
            $titleIndent = Str::pad(self::CHAR_SPACE, ceil($width - $tLength) + $indent, self::CHAR_SPACE);
        } elseif ($opts['titlePos'] === self::POS_MIDDLE) {
            $titleIndent = Str::pad(self::CHAR_SPACE, ceil(($width - $tLength) / 2) + $indent, self::CHAR_SPACE);
        } else {
            $titleIndent = Str::pad(self::CHAR_SPACE, $indent, self::CHAR_SPACE);
        }

        $titleLine = "$titleIndent<bold>$title</bold>\n";
        $border    = $indentStr . str_pad($char, $width, $char);

        Show::write($titleLine . $border);
    }
}
