<?php
namespace Industrious\WpHelpers\Fetchers\Other;

use Industrious\WpHelpers\Fetchers\TransientFetcher;

/**
 * Class CustomFieldFetcher
 */
class CustomFieldFetcher extends TransientFetcher
{
    /**
     * Cache/Transient Expiry Length
     * @var
     */
    protected $transient_exipiry = 900; // 15 Minutes

    /**
     * Cache/Transient Key
     * @var
     */
    protected $transient_key = 'custom_field_fetcher';

    /**
     * @param string $field
     * @param int    $post_id
     *
     * @return bool|mixed|null|void
     * @throws \Exception
     */
    public function fetchOne($field = '', $post_id = 0)
    {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $args = [
            'field'   => $field,
            'post_id' => $post_id
        ];

        $this->transient_key = $this->getTransientKey() . '_' . md5(json_encode($args));

        return $this->execute($field, $post_id);
    }

    /**
     * @param      $field
     * @param      $page
     * @param      $format_value
     *
     * @return bool|mixed|null|void
     * @throws \Exception
     */
    public function execute($field, $page = null, $format_value = true)
    {
        $query = $this->getTransient($this->getTransientKey());

        if ($query === false) {
            $query = get_field($field, $page, $format_value);

            $this->setTransient($query);
        }

        return $query;
    }

    /**
     * Fetch all from either a stored transient, or database query.
     *
     * @param array $args
     *
     * @return WP_Query|mixed
     */
    public function fetchAll($args)
    {
        // TODO: Implement fetchAll() method.
    }

    /**
     * @param $post_id
     *
     * @return array|bool|mixed|null
     */
    public function fetchAllForId($post_id)
    {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $this->transient_key = $this->getTransientKey() . '_' . __METHOD__ . '_' . md5(json_encode($post_id));

        $query = $this->getTransient($this->getTransientKey());

        if ($query === false) {
            $query = get_fields($post_id);

            $this->setTransient($query);
        }

        return $query;
    }

    /**
     * @return string
     */
    protected function getTransientHookName()
    {
        // TODO: Implement getTransientHookName() method.
    }
}
