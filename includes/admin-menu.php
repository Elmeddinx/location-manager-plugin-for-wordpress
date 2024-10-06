<?php
// Location Menu
function lm_add_admin_menu() {
    add_menu_page(
        'Locations',
        'Locations',
        'manage_options',
        'lm_location_menu',
        'lm_display_locations',
        'dashicons-location-alt',
        6
    );

    // Employees Menu
    add_menu_page(
        'Employees',
        'Employees',
        'manage_options',
        'lm_employee_list',
        'lm_display_employee_list',
        'dashicons-admin-users',
        7
    );

    add_submenu_page(
        'lm_employee_list',
        'Add New Employee',
        'Add New',
        'manage_options',
        'lm_add_employee',
        'lm_display_employee_form'
    );
}
add_action('admin_menu', 'lm_add_admin_menu');

// Enqueue Media Uploader Scripts
function lm_enqueue_media_uploader($hook) {
    if ($hook != 'toplevel_page_lm_add_employee' && $hook != 'employees_page_lm_add_employee') {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'lm_enqueue_media_uploader');

// Display Locations Management Page
function lm_display_locations() {
    // Mevcut kodunuz burada kalabilir.
    // Gerekli güvenlik önlemlerini (nonce doğrulaması vb.) eklemeyi unutmayın.
}

// Get Locations
function lm_get_locations() {
    global $wpdb;
    $options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'lm_state_%'");
    $locations = [];
    foreach ($options as $option) {
        $state = str_replace('lm_state_', '', $option->option_name);
        $locations[$state] = $option->option_value;
    }
    return $locations;
}

// Employee Form (Add New Employee or Edit Employee)
function lm_display_employee_form() {
    $is_edit = false;
    $employee_data = array(
        'name' => '',
        'title' => '',
        'state' => '',
        'city' => '',
        'image_id' => '',
    );

    if (isset($_GET['edit_employee'])) {
        $is_edit = true;
        $employee_slug = sanitize_title($_GET['edit_employee']);
        $option_name = 'lm_employee_' . $employee_slug;
        $employee_data = get_option($option_name);
        if (!$employee_data) {
            $employee_data = array(
                'name' => '',
                'title' => '',
                'state' => '',
                'city' => '',
                'image_id' => '',
            );
            $is_edit = false; // Çalışan bulunamadı, yeni ekleme olarak işlem yap
        }
    }

    ?>
    <div class="wrap">
        <h1><?php echo $is_edit ? 'Edit Employee' : 'Add New Employee'; ?></h1>
        <!-- Formun action özelliğini güncelledik -->
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('lm_save_employee_nonce', 'lm_employee_nonce'); ?>
            <label for="employee_name">Full Name:</label>
            <input type="text" id="employee_name" name="employee_name" value="<?php echo esc_attr($employee_data['name']); ?>" required /><br/><br/>
            <label for="employee_title">Job Title:</label>
            <input type="text" id="employee_title" name="employee_title" value="<?php echo esc_attr($employee_data['title']); ?>" required /><br/><br/>
            <label for="employee_state">State:</label>
            <select id="employee_state" name="employee_state" required>
                <option value="">Select State</option>
                <?php
                $locations = lm_get_locations();
                foreach ($locations as $state => $cities) {
                    $selected = ($employee_data['state'] == $state) ? 'selected' : '';
                    echo '<option value="' . esc_attr($state) . '" ' . $selected . '>' . ucfirst($state) . '</option>';
                }
                ?>
            </select><br/><br/>
            <label for="employee_city">City:</label>
            <select id="employee_city" name="employee_city">
                <option value="">Select City</option>
                <?php
                if (!empty($employee_data['state'])) {
                    $state = $employee_data['state'];
                    $cities = explode(',', $locations[$state]);
                    foreach ($cities as $city) {
                        $city = trim($city);
                        $selected = ($employee_data['city'] == $city) ? 'selected' : '';
                        echo '<option value="' . esc_attr($city) . '" ' . $selected . '>' . ucfirst($city) . '</option>';
                    }
                }
                ?>
            </select><br/><br/>

            <!-- Media Uploader Field -->
            <label for="employee_image">Profile Image:</label><br/>
            <div id="employee_image_container">
                <?php if (!empty($employee_data['image_id'])): ?>
                    <?php $image_url = wp_get_attachment_url($employee_data['image_id']); ?>
                    <img src="<?php echo esc_url($image_url); ?>" style="max-width: 150px; max-height: 150px;" /><br/>
                <?php endif; ?>
            </div>
            <input type="hidden" id="employee_image_id" name="employee_image_id" value="<?php echo esc_attr($employee_data['image_id']); ?>" />
            <button type="button" id="upload_employee_image_button" class="button"><?php echo empty($employee_data['image_id']) ? 'Upload Image' : 'Change Image'; ?></button>
            <?php if (!empty($employee_data['image_id'])): ?>
                <button type="button" id="remove_employee_image_button" class="button">Remove Image</button>
            <?php endif; ?>
            <br/><br/>

            <?php if ($is_edit): ?>
                <input type="hidden" name="original_name" value="<?php echo esc_attr($employee_data['name']); ?>" />
            <?php endif; ?>
            <input type="hidden" name="action" value="lm_save_employee" />
            <input type="submit" name="lm_save_employee_submit" value="Save Employee" class="button button-primary" />
        </form>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            function loadCities(state, selectedCity = '') {
                var cityDropdown = $('#employee_city');
                cityDropdown.html('<option value="">Loading...</option>');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'get_cities',
                        state: state
                    },
                    success: function (response) {
                        cityDropdown.html(response);
                        if (selectedCity) {
                            cityDropdown.val(selectedCity);
                        }
                    }
                });
            }

            $('#employee_state').on('change', function () {
                var state = $(this).val();
                if (state) {
                    loadCities(state);
                } else {
                    $('#employee_city').html('<option value="">Select City</option>');
                }
            });

            // Sayfa yüklendiğinde eğer state seçiliyse şehirleri yükle
            var initialState = $('#employee_state').val();
            var initialCity = '<?php echo esc_js($employee_data['city']); ?>';
            if (initialState) {
                loadCities(initialState, initialCity);
            }

            // Media Uploader
            var mediaUploader;

            $('#upload_employee_image_button').on('click', function (e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: 'Select Profile Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });

                mediaUploader.on('select', function () {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#employee_image_id').val(attachment.id);
                    $('#employee_image_container').html('<img src="' + attachment.url + '" style="max-width: 150px; max-height: 150px;" /><br/>');
                    $('#remove_employee_image_button').show();
                    $('#upload_employee_image_button').text('Change Image');
                });

                mediaUploader.open();
            });

            $('#remove_employee_image_button').on('click', function (e) {
                e.preventDefault();
                $('#employee_image_id').val('');
                $('#employee_image_container').html('');
                $(this).hide();
                $('#upload_employee_image_button').text('Upload Image');
            });
        });
    </script>
    <?php
}

// Save employee to the database
function lm_save_employee() {
    // Nonce doğrulaması
    if (!isset($_POST['lm_employee_nonce']) || !wp_verify_nonce($_POST['lm_employee_nonce'], 'lm_save_employee_nonce')) {
        wp_die('Security check failed');
    }

    // Kullanıcının yetkisini kontrol edin
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Log mesajı ekleyelim
    error_log('lm_save_employee fonksiyonu çalıştı');

    // Form verilerini sanitize et
    $employee_name = sanitize_text_field($_POST['employee_name']);
    error_log('Çalışan adı: ' . $employee_name);

    $employee_title = sanitize_text_field($_POST['employee_title']);
    $employee_state = sanitize_text_field($_POST['employee_state']);
    $employee_city = sanitize_text_field($_POST['employee_city']);
    $employee_image_id = isset($_POST['employee_image_id']) ? intval($_POST['employee_image_id']) : '';
    $is_edit = isset($_POST['original_name']);
    $original_name = $is_edit ? sanitize_text_field($_POST['original_name']) : '';

    // Çalışan verilerini bir dizi olarak sakla
    $employee_data = array(
        'name' => $employee_name,
        'title' => $employee_title,
        'state' => $employee_state,
        'city' => $employee_city,
        'image_id' => $employee_image_id,
    );

    // Eşsiz bir option name oluşturmak için sanitize_title kullanıyoruz
    $employee_slug = sanitize_title($employee_name);
    $option_name = 'lm_employee_' . $employee_slug;

    // Log mesajı ekleyelim
    error_log('Option name: ' . $option_name);

    // Eğer düzenleme yapılıyorsa ve isim değişmişse eski kaydı sil
    if ($is_edit && $original_name !== $employee_name) {
        $old_employee_slug = sanitize_title($original_name);
        $old_option_name = 'lm_employee_' . $old_employee_slug;
        delete_option($old_option_name);
        error_log('Eski kayıt silindi: ' . $old_option_name);
    }

    // WordPress options tablosuna kaydet
    $result = update_option($option_name, $employee_data, 'no');
    error_log('update_option result: ' . var_export($result, true));

    // Kaydedilen veriyi hemen geri çağır ve logla
    $saved_data = get_option($option_name);
    error_log('Kaydedilen veri: ' . print_r($saved_data, true));

    // Yeniden yönlendir
    wp_redirect(admin_url('admin.php?page=lm_employee_list'));
    exit;
}
add_action('admin_post_lm_save_employee', 'lm_save_employee');

// Delete employee function
function lm_delete_employee() {
    // Nonce doğrulaması
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'lm_delete_employee_nonce')) {
        wp_die('Security check failed');
    }

    // Kullanıcının yetkisini kontrol edin
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    if (isset($_GET['employee'])) {
        $employee_slug = sanitize_title($_GET['employee']);
        $option_name = 'lm_employee_' . $employee_slug;
        delete_option($option_name);
    }

    wp_redirect(admin_url('admin.php?page=lm_employee_list'));
    exit;
}
add_action('admin_post_lm_delete_employee', 'lm_delete_employee');

// Employee List with Filter
function lm_display_employee_list() {
    global $wpdb;

    // Silme işlemini buradan kaldırıyoruz
    /*
    if (isset($_GET['delete_employee'])) {
        $employee_slug = sanitize_title($_GET['delete_employee']);
        $option_name = 'lm_employee_' . $employee_slug;
        delete_option($option_name);
        wp_redirect(admin_url('admin.php?page=lm_employee_list'));
        exit;
    }
    */

    ?>
    <div class="wrap">
        <h1>Saved Employees</h1>
        <a href="<?php echo admin_url('admin.php?page=lm_add_employee'); ?>" class="page-title-action">Add New</a>
        <form method="get" action="">
            <input type="hidden" name="page" value="lm_employee_list" />
            <label for="filter_state">Filter by State:</label>
            <select id="filter_state" name="filter_state">
                <option value="">All States</option>
                <?php
                $locations = lm_get_locations();
                foreach ($locations as $state => $cities) {
                    $selected = (isset($_GET['filter_state']) && $_GET['filter_state'] == $state) ? 'selected' : '';
                    echo '<option value="' . esc_attr($state) . '" ' . $selected . '>' . ucfirst($state) . '</option>';
                }
                ?>
            </select>
            <label for="filter_city">Filter by City:</label>
            <select id="filter_city" name="filter_city">
                <option value="">All Cities</option>
                <?php
                if (isset($_GET['filter_state']) && !empty($_GET['filter_state'])) {
                    $filter_state = sanitize_text_field($_GET['filter_state']);
                    if (isset($locations[$filter_state])) {
                        $cities = explode(',', $locations[$filter_state]);
                        foreach ($cities as $city) {
                            $city = trim($city);
                            $selected = (isset($_GET['filter_city']) && $_GET['filter_city'] == $city) ? 'selected' : '';
                            echo '<option value="' . esc_attr($city) . '" ' . $selected . '>' . ucfirst($city) . '</option>';
                        }
                    }
                }
                ?>
            </select>
            <input type="submit" value="Filter" class="button" />
        </form>

        <?php
        // Çalışanları al
        $employee_options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'lm_employee_%'");

        $employees = array();
        foreach ($employee_options as $option) {
            $employee_data = maybe_unserialize($option->option_value); // Eski kayıtlar için gerekli olabilir
            if (is_array($employee_data)) {
                $employees[] = $employee_data;
            }
        }

        // Filtreleme
        $filtered_employees = array();
        foreach ($employees as $employee_data) {
            $include = true;

            if (isset($_GET['filter_state']) && !empty($_GET['filter_state'])) {
                if ($employee_data['state'] != $_GET['filter_state']) {
                    $include = false;
                }
            }
            if (isset($_GET['filter_city']) && !empty($_GET['filter_city'])) {
                if ($employee_data['city'] != $_GET['filter_city']) {
                    $include = false;
                }
            }

            if ($include) {
                $filtered_employees[] = $employee_data;
            }
        }
        ?>

        <table class="wp-list-table widefat fixed striped">
            <thead><tr><th>Profile Image</th><th>Full Name</th><th>Job Title</th><th>State</th><th>City</th><th>Actions</th></tr></thead>
            <tbody>
                <?php
                if (!empty($filtered_employees)) {
                    foreach ($filtered_employees as $employee_data) {
                        $employee_slug = sanitize_title($employee_data['name']);
                        echo '<tr>';
                        echo '<td>';
                        if (!empty($employee_data['image_id'])) {
                            $image_url = wp_get_attachment_url($employee_data['image_id']);
                            echo '<img src="' . esc_url($image_url) . '" style="max-width: 50px; max-height: 50px;" />';
                        } else {
                            echo 'No Image';
                        }
                        echo '</td>';
                        echo '<td>' . esc_html($employee_data['name']) . '</td>';
                        echo '<td>' . esc_html($employee_data['title']) . '</td>';
                        echo '<td>' . esc_html(ucfirst($employee_data['state'])) . '</td>';
                        echo '<td>' . esc_html(ucfirst($employee_data['city'])) . '</td>';
                        echo '<td>
                            <a href="' . admin_url('admin.php?page=lm_add_employee&edit_employee=' . urlencode($employee_slug)) . '" class="button">Edit</a>';
                        $delete_url = wp_nonce_url(
                            admin_url('admin-post.php?action=lm_delete_employee&employee=' . urlencode($employee_slug)),
                            'lm_delete_employee_nonce'
                        );
                        echo ' <a href="' . esc_url($delete_url) . '" class="button button-danger" onclick="return confirm(\'Are you sure you want to delete this employee?\');">Delete</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6">No employees found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            function loadCities(state, selectedCity = '') {
                var cityDropdown = $('#filter_city');
                cityDropdown.html('<option value="">Loading...</option>');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'get_cities',
                        state: state
                    },
                    success: function (response) {
                        cityDropdown.html(response);
                        if (selectedCity) {
                            cityDropdown.val(selectedCity);
                        }
                    }
                });
            }

            $('#filter_state').on('change', function () {
                var state = $(this).val();
                if (state) {
                    loadCities(state);
                } else {
                    $('#filter_city').html('<option value="">All Cities</option>');
                }
            });

            // Sayfa yüklendiğinde eğer state seçiliyse şehirleri yükle
            var initialState = $('#filter_state').val();
            var initialCity = '<?php echo isset($_GET['filter_city']) ? esc_js($_GET['filter_city']) : ''; ?>';
            if (initialState) {
                loadCities(initialState, initialCity);
            }
        });
    </script>
    <?php
}

// `lm_get_cities_by_state()` fonksiyonunu BURADAN KALDIRIYORUZ
// Bu fonksiyon `ajax-handler.php` dosyasında tanımlı ve tekrar tanımlanmasına gerek yok.
?>
