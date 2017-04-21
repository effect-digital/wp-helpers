<?php

namespace Industrious\WpHelpers;

use Industrious\WpHelpers\Fetchers\Other\SiteOptionFetcher;
use Industrious\WpHelpers\Fetchers\Other\CustomFieldFetcher;
use Industrious\WpHelpers\Fetchers\Other\CustomSubFieldFetcher;

/**
 * @param string $field
 * @param string $post_id
 *
 * @return mixed
 */
if (!function_exists('custom_field'))
{
    function custom_field($field = '', $post_id = '')
    {
        return (new CustomFieldFetcher)->fetchOne($field, $post_id);
    }
}

/**
 * @param string $field
 * @param string $post_id
 *
 * @return mixed
 */
if (!function_exists('custom_sub_field'))
{
    function custom_sub_field($field = '', $post_id = '')
    {
        return (new CustomSubFieldFetcher)->fetchOne($field, $post_id);
    }
}

/**
 * @param string $field
 * @param string $page
 *
 * @return mixed
 */
if (!function_exists('site_option'))
{
    function site_option($field = '', $page = 'options')
    {
        return (new SiteOptionFetcher)->fetchOne($field, $page);
    }
}

/**
 * @param string $field
 * @param string $size
 * @param string $fetcher_type
 * @param bool   $use_fallback
 * @param null   $post_id
 *
 * @return bool
 */
if (!function_exists('fetch_image'))
{
    function fetch_image($field = '', $size = '', $fetcher_type = '', $use_fallback = true, $post_id = null)
    {
        if(is_int($field))
        {
            $image_id = $field;
        }
        elseif($field == 'featured_image')
        {
            $image_id = \get_post_thumbnail_id($post_id);
        }
        else
        {
            $image_id = $fetcher_type == 'sub'
                ? custom_sub_field($field, $post_id)
                : custom_field($field, $post_id);

            if(is_array($image_id))
            {
                $image_id = $image_id['id'];
            }
        }

        //  Fallback Image
        if (!$image_id && $use_fallback)
        {
          return \get_site_icon_url();
        }

        $image = \wp_get_attachment_image_src($image_id, $size);

        return $image
            ? $image[0]
            : false;
    }
}

/**
 * @param $tax
 *
 * @return string
 */
if (!function_exists('get_acf_term_field'))
{
  function get_acf_term_field($tax = null)
  {
      $tax = isset($tax)
          ? $tax
          : \get_queried_object();

      return implode('_', [$tax->taxonomy, $tax->term_id]);
  }
}
