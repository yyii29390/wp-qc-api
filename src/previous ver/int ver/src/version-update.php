<?php
class Quadcell_API_Updater {
    private $current_version;
    private $update_url;
    private $plugin_slug;

    public function __construct($current_version, $update_url, $plugin_slug) {
        $this->current_version = $current_version;
        $this->update_url = $update_url;
        $this->plugin_slug = $plugin_slug;

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
    }

    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $remote_version = $this->get_remote_version();
        if (version_compare($this->current_version, $remote_version, '<')) {
            $plugin_data = $this->get_plugin_data();
            $plugin_slug = plugin_basename($this->plugin_slug);
            $transient->response[$plugin_slug] = (object) [
                'slug' => $plugin_slug,
                'new_version' => $remote_version,
                'url' => $this->update_url,
                'package' => $this->get_remote_package(),
            ];
        }

        return $transient;
    }

    public function plugin_info($res, $action, $args) {
        if ($action !== 'plugin_information' || $args->slug !== plugin_basename($this->plugin_slug)) {
            return $res;
        }

        $plugin_data = $this->get_plugin_data();
        $res = (object) [
            'name' => $plugin_data['Name'],
            'slug' => plugin_basename($this->plugin_slug),
            'version' => $this->get_remote_version(),
            'author' => $plugin_data['Author'],
            'homepage' => $plugin_data['PluginURI'],
            'short_description' => $plugin_data['Description'],
            'sections' => [
                'description' => $plugin_data['Description'],
            ],
            'download_link' => $this->get_remote_package(),
        ];

        return $res;
    }

    private function get_remote_version() {
        $response = wp_remote_get($this->update_url . '/version.php');
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        return wp_remote_retrieve_body($response);
    }

    private function get_remote_package() {
        return $this->update_url . '/download.php';
    }

    private function get_plugin_data() {
        return get_plugin_data($this->plugin_slug);
    }
}
?>
