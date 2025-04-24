<?php

class Glossary_Settings
{
    private $themes = array(
        'dark' => 'Dark',
        'light' => 'Light',
        'light-border' => 'Light + Border',
        'material' => 'Material',
        'translucent' => 'Translucent',
    );

    private $animations = array(
        'perspective-extreme' => 'Perspective Extreme',
        'perspective-subtle' => 'Perspective Subtile',
        'perspective' => 'Perspective',
        'scale-extreme' => 'Scale Extreme',
        'scale-subtle' => 'Scale Subtile',
        'scale' => 'Scale',
        'shift-away-extreme' => 'Shift Away extreme',
        'shift-away-subtle' => 'Shift Away Subtile',
        'shift-away' => 'Shift Away',
        'shift-toward-extreme' => 'Shift Toward extreme',
        'shift-toward-subtle' => 'Shift Toward Subtile',
        'shift-toward' => 'Shift Toward',
    );

    private $triggers = array(
        'mouseover' => 'Hover',
        'click' => 'Click',
    );

    public function __construct()
    {
        add_action('admin_menu', array($this, 'glossary_settings_page'));
        add_action('admin_init', array($this, 'glossary_settings_init'));
    }

    public function glossary_settings_page()
    {
        add_options_page(
            'Glossary Settings',
            'Glossary Tooltip',
            'manage_options',
            'glossary-tooltip-settings',
            array($this, 'glossary_settings_page_callback')
        );
    }

    public function glossary_settings_page_callback()
    {
?>
        <div class="wrap">
            <h1>Glossary Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('glossary-tooltip-settings-group'); ?>
                <?php do_settings_sections('glossary-tooltip-settings-group'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
    <?php
    }

    public function glossary_settings_init()
    {
        add_settings_section(
            'glossary-settings-section',
            'Glossary Settings',
            array($this, 'glossary_settings_section_callback'),
            'glossary-tooltip-settings-group'
        );

        add_settings_field(
            'glossary_theme',
            'Theme',
            array($this, 'glossary_theme_callback'),
            'glossary-tooltip-settings-group',
            'glossary-settings-section'
        );

        // add_settings_field(
        //     'glossary_custom_theme',
        //     'Custom Theme',
        //     array($this, 'glossary_custom_theme_callback'),
        //     'glossary-tooltip-settings-group',
        //     'glossary-settings-section'
        // );

        add_settings_field(
            'glossary_animation',
            'Animation',
            array($this, 'glossary_animation_callback'),
            'glossary-tooltip-settings-group',
            'glossary-settings-section'
        );

        // add_settings_field(
        //     'glossary_custom_animation',
        //     'Custom Animation',
        //     array($this, 'glossary_custom_animation_callback'),
        //     'glossary-tooltip-settings-group',
        //     'glossary-settings-section'
        // );

        add_settings_field(
            'glossary_trigger',
            'Trigger',
            array($this, 'glossary_trigger_callback'),
            'glossary-tooltip-settings-group',
            'glossary-settings-section'
        );

        add_settings_field(
            'gt_truncate',
            'Truncate Length',
            array($this, 'gt_truncate_callback'),
            'glossary-tooltip-settings-group',
            'glossary-settings-section'
        );

        add_settings_field(
            'gt_show_link',
            'Show Link',
            array($this, 'gt_show_link_callback'),
            'glossary-tooltip-settings-group',
            'glossary-settings-section'
        );

        add_settings_field(
            'gt_shortcode',
            'Shortcode for glossary posts',
            array($this, 'gt_shortcode_callback'),
            'glossary-tooltip-settings-group',
            'glossary-settings-section'
        );

        register_setting('glossary-tooltip-settings-group', 'glossary_theme');
        // register_setting('glossary-tooltip-settings-group', 'glossary_custom_theme');
        register_setting('glossary-tooltip-settings-group', 'glossary_animation');
        // register_setting('glossary-tooltip-settings-group', 'glossary_custom_animation');
        register_setting('glossary-tooltip-settings-group', 'glossary_trigger');
        register_setting('glossary-tooltip-settings-group', 'gt_truncate');
        register_setting('glossary-tooltip-settings-group', 'gt_show_link');
    }

    public function glossary_settings_section_callback()
    {
        echo 'Select your preferred theme, animation, trigger, and other settings for the glossary.';
    }

    public function glossary_theme_callback()
    {
        $theme = get_option('glossary_theme');
    ?>
        <select name="glossary_theme" id="glossary_theme">
            <option value="">Select a theme</option>
            <?php foreach ($this->themes as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($theme, $key); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    <?php
    }

    public function glossary_custom_theme_callback()
    {
        $custom_theme = get_option('glossary_custom_theme');
    ?>
        <input type="text" name="glossary_custom_theme" id="glossary_custom_theme" value="<?php echo esc_attr($custom_theme); ?>" placeholder="Custom theme name">
    <?php
    }

    public function glossary_animation_callback()
    {
        $animation = get_option('glossary_animation');
    ?>
        <select name="glossary_animation" id="glossary_animation">
            <option value="">Select an animation</option>
            <?php foreach ($this->animations as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($animation, $key); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    <?php
    }

    public function glossary_custom_animation_callback()
    {
        $custom_animation = get_option('glossary_custom_animation');
    ?>
        <input type="text" name="glossary_custom_animation" id="glossary_custom_animation" value="<?php echo esc_attr($custom_animation); ?>" placeholder="Custom animation name">
    <?php
    }

    public function glossary_trigger_callback()
    {
        $trigger = get_option('glossary_trigger');
    ?>
        <select name="glossary_trigger" id="glossary_trigger">
            <option value="">Select a trigger</option>
            <?php foreach ($this->triggers as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($trigger, $key); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    <?php
    }

    public function gt_truncate_callback()
    {
        $truncate = get_option('gt_truncate', 250);
    ?>
        <input type="number" name="gt_truncate" id="gt_truncate" value="<?php echo esc_attr($truncate); ?>" placeholder="Enter truncate length">
    <?php
    }

    public function gt_show_link_callback()
    {
        $show_link = get_option('gt_show_link', true);
    ?>
        <select name="gt_show_link" id="gt_show_link">
            <option value="1" <?php selected($show_link, '1'); ?>>True</option>
            <option value="0" <?php selected($show_link, '0'); ?>>False</option>
        </select>
    <?php
    }

    public function gt_shortcode_callback()
    {

    ?>
        <code>[gt_glossary_posts]</code>
<?php
    }
}
