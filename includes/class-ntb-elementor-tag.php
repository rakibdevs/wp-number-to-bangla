<?php

/**
 * The "Number to Bangla" Elementor dynamic tag.
 *
 * Only loaded when Elementor is active (see NTB_Elementor::register_tags()),
 * since it extends an Elementor base class.
 *
 * @package NumberToBangla
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

class NTB_Elementor_Tag extends \Elementor\Core\DynamicTags\Tag
{
    /**
     * @return string
     */
    public function get_name()
    {
        return 'ntb-convert';
    }

    /**
     * @return string
     */
    public function get_title()
    {
        return __('Number to Bangla', 'number-to-bangla');
    }

    /**
     * @return string
     */
    public function get_group()
    {
        return \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY;
    }

    /**
     * @return array
     */
    public function get_categories()
    {
        return [\Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY];
    }

    /**
     * Register the tag's editor controls.
     */
    protected function register_controls()
    {
        $this->add_control(
            'value',
            [
                'label' => __('Value', 'number-to-bangla'),
                'type'  => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'format',
            [
                'label'   => __('Format', 'number-to-bangla'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'number',
                'options' => [
                    'number'        => __('Bangla number', 'number-to-bangla'),
                    'word'          => __('Bangla word', 'number-to-bangla'),
                    'money'         => __('Money (Taka)', 'number-to-bangla'),
                    'comma'         => __('Comma (lakh)', 'number-to-bangla'),
                    'percentage'    => __('Percentage', 'number-to-bangla'),
                    'month'         => __('Gregorian month', 'number-to-bangla'),
                    'bengali-month' => __('Bengali month', 'number-to-bangla'),
                    'season'        => __('Season', 'number-to-bangla'),
                    'day'           => __('Weekday', 'number-to-bangla'),
                    'date'          => __('Date', 'number-to-bangla'),
                    'bengali-date'  => __('Bengali calendar date', 'number-to-bangla'),
                    'week'          => __('Week number', 'number-to-bangla'),
                    'time'          => __('Time', 'number-to-bangla'),
                    'duration'      => __('Duration (seconds)', 'number-to-bangla'),
                    'age'           => __('Age (birth date)', 'number-to-bangla'),
                    'ordinal'       => __('Ordinal', 'number-to-bangla'),
                    'parse'         => __('Parse (Bangla to English)', 'number-to-bangla'),
                ],
            ]
        );

        $this->add_control(
            'words',
            [
                'label'     => __('Spell out in words', 'number-to-bangla'),
                'type'      => \Elementor\Controls_Manager::SWITCHER,
                'default'   => '',
                'condition' => [
                    'format' => ['percentage', 'time'],
                ],
            ]
        );

        $this->add_control(
            'date_format',
            [
                'label'     => __('Date format', 'number-to-bangla'),
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => 'j F, Y',
                'condition' => [
                    'format' => ['date', 'bengali-date'],
                ],
            ]
        );

        $this->add_control(
            'prefix',
            [
                'label' => __('Prefix', 'number-to-bangla'),
                'type'  => \Elementor\Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'suffix',
            [
                'label' => __('Suffix', 'number-to-bangla'),
                'type'  => \Elementor\Controls_Manager::TEXT,
            ]
        );
    }

    /**
     * Output the converted value.
     */
    public function render()
    {
        $value = $this->get_settings('value');
        if ($value === '' || $value === null) {
            return;
        }

        $output = ntb_convert(
            $value,
            $this->get_settings('format'),
            [
                'words'       => (bool) $this->get_settings('words'),
                'date_format' => $this->get_settings('date_format'),
            ]
        );

        if ($output === false) {
            return;
        }

        $prefix = ntb_clean_affix($this->get_settings('prefix'));
        $suffix = ntb_clean_affix($this->get_settings('suffix'));

        echo esc_html($prefix . $output . $suffix);
    }
}
