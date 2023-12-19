<?php

namespace Spark\Framework\Support;

use RuntimeException;

class Glob
{
    public const GLOB_ERR = 0x01;
    public const GLOB_MARK = 0x02;
    public const GLOB_NOSORT = 0x04;
    public const GLOB_NOCHECK = 0x08;
    public const GLOB_NOESCAPE = 0x10;
    public const GLOB_BRACE = 0x20;
    public const GLOB_ONLYDIR = 0x40;

    /** @var int[] */
    public static array $flagsMap = [
        self::GLOB_MARK => \GLOB_MARK,
        self::GLOB_NOSORT => \GLOB_NOSORT,
        self::GLOB_NOCHECK => \GLOB_NOCHECK,
        self::GLOB_NOESCAPE => \GLOB_NOESCAPE,
        self::GLOB_BRACE => \GLOB_BRACE,
        self::GLOB_ONLYDIR => \GLOB_ONLYDIR,
        self::GLOB_ERR => \GLOB_ERR,
    ];

    /**
     * Searches for files and directories that match a given pattern.
     *
     * @param string $pattern
     *  The pattern to search for. The pattern should follow glob pattern rules.
     * @param int $flags
     *  [Optional] Additional flags to modify the behavior of the glob. Default is 0.
     *  If provided, the flags will be combined with bitwise OR operation.
     *  Possible flag values can be found in the static::$flagsMap property.
     *
     * @return array
     *  An array containing the matched files and directories.
     *
     * @throws \RuntimeException
     *  If the glob operation fails.
     */
    public static function glob(string $pattern, int $flags = 0): array
    {
        $globFlags = 0;

        if ($flags > 0) {
            foreach (static::$flagsMap as $internal => $flag) {
                if (($flags & $internal) > 0) {
                    $globFlags |= $flag;
                }
            }
        }

        $res = \glob($pattern, $globFlags);

        if ($res === false) {
            throw new RuntimeException("glob('{$pattern}', {$globFlags}) failed");
        }

        return $res;
    }
}
