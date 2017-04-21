<?php
namespace Industrious\WpHelpers\Fetchers\Taxonomy;

use Industrious\WpHelpers\Fetchers\TransientTaxonomyFetcher;


/**
 * Class TaxFetcher
 */
class TaxFetcher extends TransientTaxonomyFetcher
{
    /**
     * @var string
     */
    protected $taxonomy = 'tax';

    /**
     * @var string
     */
    protected $transient_key = 'taxonomy_tax';

    /**
     * @var bool
     */
    protected $fetch_posts = false;

    /**
     * @var bool
     */
    protected $fetch_posts_post_type;

    /**
     * @var bool
     */
    protected $sort_hierarchically = false;

    /**
     * @param string $taxonomy
     *
     * @return $this
     */
    public function setTaxonomy($taxonomy = '')
    {
        $this->taxonomy = $taxonomy;

        $this->transient_key = str_replace('_tax', '_' . $taxonomy, $this->transient_key);

        return $this;
    }

    /**
     * @param string $post_type
     *
     * @return $this
     */
    public function withPosts($post_type = 'any')
    {
        $this->fetch_posts = true;
        $this->fetch_posts_post_type = $post_type;

        return $this;
    }

    /**
     * @return $this
     */
    public function setHierarchicalSorting()
    {
        $this->sort_hierarchically = true;

        return $this;
    }

    /**
     * @param array $args
     *
     * @return array|\Fetchers\WP_Query|mixed
     */
    public function fetchAll($args = [])
    {
        $query = parent::fetchAll($args);

        if ($this->sort_hierarchically) {
            $sorted = [];
            $this->sort_terms_hierarchically($query, $sorted);

            $query = $sorted;
        }

        return $query;
    }

    /**
     * Recursively sort an array of taxonomy terms hierarchically. Child categories will be
     * placed under a 'children' member of their parent term.
     *
     * @param array   $cats      taxonomy term objects to sort
     * @param array   $into      result array to put them in
     * @param integer $parent_id the current parent ID to put them in
     */
    protected function sort_terms_hierarchically(Array &$cats, Array &$into, $parent_id = 0)
    {
        foreach ($cats as $i => $cat) {
            if ($cat->parent == $parent_id) {
                $into[$cat->term_id] = $cat;
                unset($cats[$i]);
            }
        }

        foreach ($into as $topCat) {
            $topCat->children = array();
            $this->sort_terms_hierarchically($cats, $topCat->children, $topCat->term_id);
        }
    }
}
