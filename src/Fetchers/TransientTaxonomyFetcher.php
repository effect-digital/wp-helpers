<?php
namespace Industrious\WpHelpers\Fetchers;

/**
 * Class TransientTaxonomyFetcher
 */
abstract class TransientTaxonomyFetcher extends TransientFetcher
{
    /**
     * @var
     */
    protected $taxonomy;

    /**
     * @return string
     */
    protected function getTransientHookName()
    {
        return 'edit_' . $this->taxonomy;
    }

    /**
     * @param $term_id
     *
     * @return array|null|void|\WP_Error|\WP_Term
     */
    public function fetchOne($term_id)
    {
        $term = get_term($term_id, $this->taxonomy);

        if (!$term->count) {
            return;
        }

        $term->posts = [];

        if ($this->fetch_posts) {
            $term->posts = get_posts([
                'post_type'      => 'any',
                'order'          => 'asc',
                'orderby'        => 'menu_order',
                'posts_per_page' => -1,
                $this->taxonomy  => $term->slug
            ]);
        }

        return $term;
    }

    /**
     * @param array $args
     *
     * @return mixed|WP_Query
     */
    public function fetchAll($args = [])
    {
        $transient_key = $this->getTransientKey();

        $query = $this->getTransient($transient_key);

        if ($query === false) {
            $query = get_terms($this->taxonomy, $args);

            if ($query instanceof \WP_Error) {
                return $query;
            }

            if ($this->fetch_posts) {
                foreach ($query as $term) {
                    $term->posts = [];

                    if (!$term->count) {
                        continue;
                    }

                    $term->posts = get_posts([
                        'post_type'      => $this->fetch_posts_post_type,
                        'order'          => 'asc',
                        'orderby'        => 'menu_order',
                        'posts_per_page' => -1,
                        'tax_query'      => [
                            [
                                'taxonomy' => $this->taxonomy,
                                'field'    => 'term_id',
                                'terms'    => $term->term_id,
                            ]
                        ]
                    ]);
                }
            }

            $this->setTransient($query);
        }

        return $query;
    }

    /**
     *
     */
    public function clearTransientHook()
    {
        $action = $this->getTransientHookName();

        add_action($action, function ($term_id, $term_taxonomy_id) {
            $this->clearTransient();
        }, 10, 3);
    }
}
