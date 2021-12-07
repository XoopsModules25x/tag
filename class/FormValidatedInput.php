<?php declare(strict_types=1);

namespace XoopsModules\Tag;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Tag form element for form input
 *
 * @copyright       {@link https://sourceforge.net/projects/xoops/ The XOOPS Project}
 * @license         {@link https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU GPL 2}
 * @author          Kazumi Ono (AKA onokazu) https://www.myweb.ne.jp/, https://jp.xoops.org/
 * @author          ZySpec <zyspec@yahoo.com>
 * @since           2.33
 */

/**
 * A simple HTML5 type validated input field
 */
class FormValidatedInput extends \XoopsFormText
{
    /**
     * Initial type
     *
     * @var string
     */
    private $_type;
    /**
     * Valid HTML Type array
     *
     * @var string
     */
    private $_htmlTypes;

    /**
     * Constructor
     *
     * @param string $caption   Caption
     * @param string $name      "name" attribute
     * @param int    $size      Size
     * @param int    $maxlength Maximum length of text
     * @param string $value     Initial text
     */
    public function __construct($caption, $name, $size, $maxlength, $value = '', string $type = 'text')
    {
        $this->_htmlTypes = [
            'color',
            'date',
            'datetime',
            'datetime-local',
            'email',
            'month',
            'number',
            'range',
            'search',
            'tel',
            'text',
            'time',
            'url',
            'week',
        ];
        $this->setCaption($caption);
        $this->setName($name);
        $this->_size      = (int)$size;
        $this->_maxlength = (int)$maxlength;
        $this->setValue($value);
        $this->setType($type);
    }

    /**
     * Get type information value
     *
     * @return string containing type
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * Get HTML types supported
     *
     * @return string|array containing HTML type(s) supported
     */
    public function getHtmlTypes()
    {
        return $this->_htmlTypes;
    }

    /**
     * Set initial text value
     *
     * @param string|array $value is string, set value; value is array then keys are ('type', 'min', 'max')
     */
    public function setType($value): void
    {
        if (isset($value)) {
            if (\is_array($value)) {
                $value       = isset($value['type']) ? \mb_strtolower($value['type']) : 'text';
                $this->_type = \in_array($value, $this->_htmlTypes, true) ? $value : 'text';
                if (\in_array(
                    $value['type'],
                    [
                        'number',
                        'date',
                        'range',
                    ],
                    true
                )) {
                    if (isset($value['min'])) {
                        $this->setExtra('min=' . $value['min']);
                    }
                    if (isset($value['max'])) {
                        $this->setExtra('max=' . $value['max']);
                    }
                }
            } else {
                $value       = isset($value) ? \mb_strtolower($value) : 'text';
                $this->_type = \in_array($value, $this->_htmlTypes, true) ? $value : 'text';
            }
        } else {
            $this->_type = 'text';
        }
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML <input>
     */
    public function render(): string
    {
        $myClasses = $this->getClass();
        $classes   = $myClasses ? " class='{$myClasses}'" : '';

        return "<input type='" . $this->_type . "' name='" . $this->getName() . "' title='" . $this->getTitle() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "'" . $classes . $this->getExtra() . '>';
    }
}
