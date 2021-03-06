<?php
/**
 * Flitch
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mail@dasprids.de so I can send you a copy immediately.
 *
 * @category   Flitch
 * @package    Flitch_Rule
 * @subpackage Whitespace
 * @copyright  Copyright (c) 2011 Ben Scholzen <mail@dasprids.de>
 * @license    New BSD License
 */

namespace Flitch\Rule\Whitespace;

use Flitch\Rule\AbstractRule,
    Flitch\File\File;

/**
 * Indentation rule.
 *
 * @category   Flitch
 * @package    Flitch_Rule
 * @subpackage Whitespace
 * @copyright  Copyright (c) 2011 Ben Scholzen <mail@dasprids.de>
 * @license    New BSD License
 */
class Indentation extends AbstractRule
{
    /**
     * Indentation style, can be "space" or "tab".
     *
     * @var string
     */
    protected $indentStyle = 'space';

    /**
     * Number of indentation characters per level.
     *
     * @var integer
     */
    protected $indentCount = 4;

    /**
     * Set indentation style.
     *
     * @param  string $style
     * @return Indentation
     */
    public function setIndentStyle($style)
    {
        $style = strtolower($style);

        switch ($style) {
            case 'space':
            case 'tab':
                $this->indentStyle = $style;
                break;
        }

        return $this;
    }

    /**
     * Set indentation count.
     *
     * @param  integer $count
     * @return Indentation
     */
    public function setIndentCount($count)
    {
        $this->indentCount = max(1, (int) $count);
        return $this;
    }

    /**
     * check(): defined by Rule interface.
     *
     * @see    Rule::check()
     * @param  File  $file
     * @return void
     */
    public function check(File $file)
    {
        $indentation = str_repeat(($this->indentStyle === 'space' ? ' ' : "\t"), $this->indentCount);

        $file->rewind();

        while (true) {
            $token = $file->current();
            $level = $token->getLevel();

            $file->next();
            if ($file->current()->getType() === '}' || $file->current()->getType() === ')') {
                $level--;
            }
            $file->prev();

            $expectedIndentation = str_repeat($indentation, $level);
            $actualIndentation   = $token->getTrailingWhitespace();

            if ($expectedIndentation !== $actualIndentation) {
                $this->addViolation($file, $token, $column, $message);
            }

            if (!$file->seekNextLine()) {
                return;
            }
        }
    }
}
