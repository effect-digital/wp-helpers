<?php

namespace Industrious\WpHelpers;

use Exception;
use Industrious\WpHelpers\Fetchers\Other\SiteOptionFetcher;
use Industrious\WpHelpers\Fetchers\Other\CustomFieldFetcher;
use Industrious\WpHelpers\Fetchers\Other\CustomSubFieldFetcher;

use function get_post_thumbnail_id;
use function get_queried_object;
use function wp_get_attachment_image_src;
use function maybe_serialize;
use function maybe_unserialize;

/**
 * @param string $field
 * @param string $post_id
 * @param bool $format_value
 * @return mixed
 * @throws Exception
 */
function custom_field($field = '', $post_id = '', $format_value = true)
{
    return (new CustomFieldFetcher)->fetchOne($field, $post_id, $format_value);
}

/**
 * @param string $field
 * @param string $post_id
 * @param bool $format_value
 * @return mixed
 * @throws Exception
 */
function custom_sub_field($field = '', $post_id = '', $format_value = true)
{
    return (new CustomSubFieldFetcher)->fetchOne($field, $post_id, $format_value);
}

/**
 * @param string $field
 * @param string $page
 *
 * @return mixed
 * @throws Exception
 */
function site_option($field = '', $page = 'options')
{
    return (new SiteOptionFetcher)->fetchOne($field, $page);
}

/**
 * @param string $field
 * @param string $size
 * @param string $fetcher_type
 * @param bool $use_fallback
 * @param null $post_id
 *
 * @return string|null
 * @throws Exception
 */
function fetch_image($field = '', $size = '', $fetcher_type = '', $use_fallback = true, $post_id = null): ?string
{
    if (is_int($field))
    {
        $image_id = $field;
    }
    elseif ($field == 'featured_image')
    {
        $image_id = get_post_thumbnail_id($post_id);
    }
    else
    {
        $image_id = $fetcher_type == 'sub'
            ? custom_sub_field($field, $post_id)
            : custom_field($field, $post_id);

        if (is_array($image_id))
        {
            $image_id = $image_id['id'];
        }
    }

    $image = $image_id ? wp_get_attachment_image_src($image_id, $size) : null;

    //  Fallback Image
    if (!$image && $use_fallback)
    {
        $fallback = apply_filters('image-fallback', get_option('site_icon'));

        $image = wp_get_attachment_image_src($fallback, $size);
    }

    return $image
        ? $image[0]
        : null;
}

/**
 * @param $tax
 * @return string
 */
function get_acf_term_field($tax = null): string
{
    $tax = isset($tax)
        ? $tax
        : get_queried_object();

    return implode('_', [$tax->taxonomy, $tax->term_id]);
}

/**
 * @param $data
 * @return string|null
 * @throws Exception
 */
function wp_encrypt($data): ?string
{
    $encryptor = get_encryptor();

    return $encryptor->encrypt(
        maybe_serialize($data)
    );
}

/**
 * @param string $data
 * @return mixed
 * @throws Exception
 */
function wp_decrypt(string $data)
{
    $encryptor = get_encryptor();

    return maybe_unserialize(
        $encryptor->decrypt($data)
    );
}

/**
 * @return Encryption
 * @throws Exception
 */
function get_encryptor(): Encryption
{
    $key = getenv('WP_ENCRYPTION_KEY');
    $initializationVector = getenv('WP_ENCRYPTION_IV');

    if (! $key) {
        throw new Exception(
            sprintf('No key [WP_ENCRYPTION_KEY] found.')
        );
    }

    if (! $initializationVector) {
        throw new Exception(
            sprintf('No key [WP_ENCRYPTION_IV] found.')
        );
    }

    return new Encryption($key, $initializationVector);
}
