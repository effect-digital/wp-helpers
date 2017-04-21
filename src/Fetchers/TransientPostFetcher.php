<?php
namespace Industrious\WpHelpers\Fetchers;

/**
 * Class TransientPostFetcher
 */
abstract class TransientPostFetcher extends TransientFetcher
{
    /**
     * Custom Post Type to use as a hook on save/update
     * @var
     */
    protected $custom_post_type;

    /**
     * Number of posts to retrieve on fetchAll
     * @var int
     */
    protected $posts_per_page = 99;

    /**
     * @return string
     */
    protected function getTransientHookName()
    {
        return 'save_post_' . $this->custom_post_type;
    }

    /**
     * @param array $args
     *
     * @return mixed|\WP_Query
     */
    public function fetchOne($args = [])
    {
        if(is_int($args))
        {
            $args = [
                'p' => $args
            ];
        }

        $args = array_merge([
            'post_type'      => $this->custom_post_type,
            'posts_per_page' => 1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC'
        ], $args);

        $this->transient_key = $this->getTransientKey() . '_' . md5(json_encode($args));

        return $this->execute($args);
    }

    /**
     * @param array $args
     *
     * @return mixed|WP_Query
     */
    public function fetchAll($args = [])
    {
        $args = array_merge([
            'post_type'      => $this->custom_post_type,
            'posts_per_page' => $this->posts_per_page,
            'orderby'        => 'menu_order',
            'order'          => 'ASC'
        ], $args);

        $this->transient_key = $this->getTransientKey() . '_' . md5(json_encode($args));

        return $this->execute($args);
    }

    /**
     * @param $args
     *
     * @return mixed|\WP_Query
     */
    public function execute($args)
    {
        $query = $this->getTransient($this->getTransientKey());

        if ($query === false) {
            $query = new \WP_Query($args);

            $this->setTransient($query);
        }

        return $query;
    }
}
