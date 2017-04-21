<?php
namespace Industrious\WpHelpers\Fetchers\PostType;

/**
 * Class PostFetcher
 * @package Fetchers\PostType
 */
class PostFetcher extends \Fetchers\TransientPostFetcher
{
    /**
     * @var string
     */
    protected $custom_post_type = 'post';

    /**
     * @var string
     */
    protected $transient_key = 'post_fetcher';

    /**
     * @param string $post_type
     *
     * @return $this
     */
    public function setPostType($post_type = '')
    {
        $this->custom_post_type = $post_type;

        $this->transient_key = str_replace('post_', $post_type . '_', $this->transient_key);

        return $this;
    }
}