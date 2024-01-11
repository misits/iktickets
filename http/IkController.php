<?php

namespace Iktickets\http;

use \WP_Error;
use \WP_REST_Response;
use \WP_Query;
use \Exception;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

abstract class IkController
{
    const PROJECT_NAME = "iktickets";
    const WP_OPTION_EVENTS = 'iktickets_events';
    const WP_OPTION_THEME = 'iktickets_color_theme';
    const API_URL = 'https://etickets.infomaniak.com/api/shop';
    const API_KEY = '';
    const APP_TOKEN = '';
    const CURRENCY_CHF = "1";
    const CURRENCY_EUR = "2";
    const LANG_FR = "fr_FR";
    const LANG_EN = "en_GB";
    const LANG_DE = "de_DE";
    const COUNTRIES = array(
        "ALBANIA", "ALGERIA", "AMERICAN SAMOA", "ANDORRA", "ANGOLA", "ANGUILLA", "ANTIGUA AND BARBUDA",
        "ARGENTINA", "ARMENIA", "ARUBA", "AUSTRALIA", "AUSTRIA", "AZERBAIJAN", "BAHAMAS", "BAHRAIN",
        "BANGLADESH", "BARBADOS", "BELARUS", "BELGIUM", "BELIZE", "BENIN", "BERMUDA", "BHUTAN",
        "BOLIVIA", "BOSNIA AND HERZEGOWINA", "BOTSWANA", "BRAZIL", "BRUNEI DARUSSALAM", "BULGARIA",
        "BURKINA FASO", "BURUNDI", "CAMBODIA", "CAMEROON", "CANADA", "CAPE VERDE", "CAYMAN ISLANDS",
        "CENTRAL AFRICAN REPUBLIC", "CHAD", "CHILE", "CHINA", "COLOMBIA", "COMOROS", "CONGO",
        "COOK ISLANDS", "COSTA RICA", "COTE D'IVOIRE", "CROATIA", "CUBA", "CYPRUS", "CZECH REPUBLIC",
        "DENMARK", "DJIBOUTI", "DOMINICA", "DOMINICAN REPUBLIC", "ECUADOR", "EGYPT", "EL SALVADOR",
        "EQUATORIAL GUINEA", "ERITREA", "ESTONIA", "ETHIOPIA", "FALKLAND ISLANDS (MALVINAS)",
        "FAROE ISLANDS", "FIJI", "FINLAND", "FRANCE", "FRENCH GUIANA", "GABON", "GAMBIA", "GEORGIA",
        "GERMANY", "GHANA", "GIBRALTAR", "GREECE", "GREENLAND", "GRENADA", "GUADELOUPE", "GUAM",
        "GUATEMALA", "GUINEA", "GUINEA-BISSAU", "GUYANA", "HAITI", "HONDURAS", "HONG KONG", "HUNGARY",
        "ICELAND", "INDIA", "INDONESIA", "IRAN (ISLAMIC REPUBLIC OF)", "IRAQ", "IRELAND", "ISRAEL",
        "ITALY", "JAMAICA", "JAPAN", "JORDAN", "KAZAKHSTAN", "KENYA", "KIRIBATI",
        "KOREA DEMOC. PEOPLE'S REP. OF", "KOREA REPUBLIC OF", "KUWAIT", "KYRGYZSTAN",
        "LAO PEOPLE'S DEMOC. REP.", "LATVIA", "LEBANON", "LESOTHO", "LIBERIA", "LIBYAN ARAB JAMAHIRIYA",
        "LIECHTENSTEIN", "LITHUANIA", "LUXEMBOURG", "MACAU", "MACEDONIA REPUBLIC OF", "MADAGASCAR",
        "MALAWI", "MALAYSIA", "MALDIVES", "MALI", "MALTA", "MARSHALL ISLANDS", "MARTINIQUE", "MAURITANIA",
        "MAURITIUS", "MEXICO", "MICRONESIA FEDER. STATES OF", "MOLDOVA REPUBLIC OF", "MONACO", "MONGOLIA",
        "MONTSERRAT", "MOROCCO", "MOZAMBIQUE", "MYANMAR", "NAMIBIA", "NAURU", "NEPAL", "NETHERLANDS",
        "NETHERLANDS ANTILLES", "NEW CALEDONIA", "NEW ZEALAND", "NICARAGUA", "NIGER", "NIGERIA", "NIUE",
        "NORTHERN MARIANA ISLANDS", "NORWAY", "OMAN", "PAKISTAN", "PALAU", "PANAMA", "PAPUA NEW GUINEA",
        "PARAGUAY", "PERU", "PHILIPPINES", "POLAND", "PORTUGAL", "PUERTO RICO", "QATAR", "ILE DE LA REUNION",
        "ROMANIA", "RUSSIAN FEDERATION", "RWANDA", "SAINT KITTS AND NEVIS", "CONGO DR", "SAN MARINO",
        "SAO TOME AND PRINCIPE", "SAUDI ARABIA", "SENEGAL", "SEYCHELLES", "SIERRA LEONE", "SINGAPORE",
        "SLOVAKIA (Slovak Republic)", "SLOVENIA", "SOLOMON ISLANDS", "SOMALIA", "SOUTH AFRICA", "SPAIN",
        "SRI LANKA", "ST. HELENA", "ST. PIERRE AND MIQUELON", "SUDAN", "SURINAME", "SWAZILAND", "SWEDEN",
        "SWITZERLAND", "SYRIAN ARAB REPUBLIC", "TAIWAN PROVINCE OF CHINA", "TAJIKISTAN", "THAILAND",
        "TOKELAU", "TONGA", "TRINIDAD AND TOBAGO", "TUNISIA", "TURKEY", "TURKMENISTAN", "UGANDA",
        "UKRAINE", "UNITED ARAB EMIRATES", "UNITED KINGDOM", "UNITED STATES", "URUGUAY", "UZBEKISTAN",
        "VENEZUELA", "VIET NAM", "VIRGIN ISLANDS (BRITISH)", "VIRGIN ISLANDS (U.S.)",
        "WALLIS AND FUTUNA ISLANDS", "YUGOSLAVIA", "ZAMBIA", "ZIMBABWE", "KOSOVO", "SERBIA",
        "FRENCH POLYNESIA", "FRENCH S. TERRITORIES", "TANZANIA", "TOGO",
        "UNITED STATES MINOR OUTLYING ISLANDS"
    );
    const CIVILITIES = array("Mr", "Mrs");
    const LANGUAGES = array("fr", "de", "en");
    const URL_DEFAULT = "/ikevents/#/cart";
    const URL_OK = "/ikevents/#/checkout/success";
    const URL_ERROR = "/ikevents/#/checkout/error";
    const LOCALE = "fr-CH";

    /**
     * Send a request to the API and return the decoded response.
     *
     * @param string $api_endpoint
     * @param string $api_lang
     * @param string $api_currency
     * @param array $api_headers
     * @param array $api_data
     * @param string $method
     * @return WP_Error|array|null The decoded response, or a WP_Error on failure.
     */
    public function apiRequest(string $api_endpoint, string $api_lang, string $api_currency, array $api_headers = array(), array $api_data = array(), string $method = 'POST'): WP_Error | WP_REST_Response | array | int | string | null
    {
        // Define the full API URL
        $api_url = self::API_URL . $api_endpoint;

        $headers = array(
            'Accept-Language' => $api_lang, // or 'fr_FR' or 'de_DE'
            'Authorization'   => 'Bearer ' . get_option('iktickets_api_token'),
            'key'             => get_option('iktickets_api_key'),
            'Accept'          => 'application/json',
            'Content-Type'    => 'application/json',
            'currency'        => $api_currency,    // '1' for CHF, '2' for EUR
        );

        // Add the API headers
        $headers = array_merge($headers, $api_headers);

        // Define the API request arguments
        if ($method == 'GET') {
            $args = array(
                'headers' => $headers,
                'timeout' => 60 * 3, // seconds
            );
        } else {
            $args = array(
                'headers' => $headers,
                'body'    => json_encode($api_data), // Encode the data as JSON string for POST requests
            );

            // Remove $args['body'] if empty
            if (empty($args['body'])) {
                unset($args['body']);
            }
        }

        // print request
        //echo '<pre>';
        //print_r($args);
        //echo '</pre>';

        // Make the API request
        try {
            $response = match ($method) {
                'GET' => wp_remote_get($api_url, $args),
                'HEAD' => wp_remote_head($api_url, $args),
                'PUT' => wp_remote_request($api_url, array_merge($args, ['method' => 'PUT'])),
                'DELETE' => wp_remote_request($api_url, array_merge($args, ['method' => 'DELETE'])),
                default => wp_remote_post($api_url, $args),
            };
        } catch (Exception $e) {
            // Log the error
            error_log($e->getMessage());

            // Return null
            return null;
        }

        // Check for errors
        if (is_wp_error($response)) {
            error_log($response->get_error_message()); // Log the error message
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Could not communicate with the Infomaniak API.'), 500);
        }

        // Extract the response body
        $response_body = wp_remote_retrieve_body($response);

        if ($response_body == 'Not Authorized') {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Not Authorized'), 401);
        }

        // Decode the JSON response
        return json_decode($response_body, true);
    }

    /**
     * Validate the parameters.
     * @param $params
     * @param array $enum
     * @return bool|WP_Error
     */
    protected function checkParams($params, array $enum = array()): bool|WP_REST_Response
    {
        foreach ($params as $key => $value) {
            if (empty($value)) {
                if (!in_array($key, $enum)) {
                    return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid parameter: ' . $key), 400);
                }
            }
        }
        return true;
    }

    /**
     * Check sort enum
     * @param $sort
     * @param array $enum
     * @return bool|WP_Error
     */
    protected function checkSort($sort, array $enum = array()): bool|WP_REST_Response
    {
        if (!in_array($sort, $enum)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid sort: ' . $sort), 400);
        }
        return true;
    }

    /**
     * Check civility enum
     * @param $civility
     * @param array $enum
     * @return bool|WP_Error
     */
    protected function checkCivility($civility, array $enum = self::CIVILITIES): bool|WP_REST_Response
    {
        if (!in_array($civility, $enum)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid civility: ' . $civility), 400);
        }
        return true;
    }

    /**
     * Check languages enum
     * @param $language
     * @param array $enum
     * @return bool|WP_Error
     */
    protected function checkLanguage($language, array $enum = self::LANGUAGES): bool|WP_REST_Response
    {
        if (!in_array($language, $enum)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid language: ' . $language), 400);
        }
        return true;
    }

    /**
     * Check country enum
     * @param $country
     * @param array $enum
     * @return bool|WP_Error
     */
    protected function checkCountry($country, array $enum = self::COUNTRIES): bool|WP_REST_Response
    {
        if (!in_array($country, $enum)) {
            return new WP_REST_Response(array('status' => 'error', 'message' => 'Invalid country: ' . $country), 400);
        }
        return true;
    }

    /**
     * Remove empty parameters
     * @param $params
     * @return array
     */
    protected function removeEmptyParams($params): array
    {
        foreach ($params as $key => $value) {
            if (empty($value)) {
                unset($params[$key]);
            }
        }
        return $params;
    }

    /**
     * Add attachment image to post
     * @param $post_id
     * @param $image_url
     */
    public static function attachment_exists($file_hash)
    {
        $query_args = array(
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'fields'      => 'ids',
            'meta_query'  => array(
                array(
                    'key'   => 'file_hash',
                    'value' => $file_hash,
                )
            )
        );

        $query = new WP_Query($query_args);

        if (!empty($query->posts)) {
            return $query->posts[0];  // Return the first matching attachment ID
        }

        return false;
    }


    public static function set_featured_image_from_url($post_id, $image_url, $filename)
    {
        // Remove all after ? in image url
        $file_content = @file_get_contents($image_url);
        $file_hash = md5($file_content);
        $existing_attachment_id = self::attachment_exists($file_hash);


        if ($existing_attachment_id) {
            // If the image already exists, set it as the featured image for the post
            set_post_thumbnail($post_id, $existing_attachment_id);
        } else {
            $upload_file = wp_upload_bits($filename, null, @file_get_contents($image_url));
            if (!$upload_file['error']) {
                // if succesfull insert the new file into the media library (create a new attachment post type).
                $wp_filetype = wp_check_filetype($filename, null);

                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_parent'    => $post_id,
                    'post_title'     => preg_replace('/\.[^.]+$/', '', $filename), // eg: image.jpg => image
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $post_id);

                if (!is_wp_error($attachment_id)) {
                    // if attachment post was successfully created, insert it as a thumbnail to the post $post_id.
                    require_once(ABSPATH . "wp-admin" . '/includes/image.php');

                    $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);

                    wp_update_attachment_metadata($attachment_id,  $attachment_data);
                    set_post_thumbnail($post_id, $attachment_id);
                }
            }
        }
    }

    public static function getBaseUrl() 
    {
        // output: /myproject/index.php
        $currentPath = $_SERVER['PHP_SELF']; 
    
        // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
        $pathInfo = pathinfo($currentPath); 
    
        // output: localhost
        $hostName = $_SERVER['HTTP_HOST']; 
    
        // output: http://
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
    
        // return: http://localhost/myproject/
        return $protocol.'://'.$hostName;
    }
}
