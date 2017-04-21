<?php
namespace Industrious\WpHelpers\Fetchers\Other;

/**
 * Class SiteOptionFetcher
 * @package Fetchers\Other
 */
class SiteOptionFetcher extends CustomFieldFetcher
{
    /**
     * Cache/Transient Key
     * @var
     */
    protected $transient_key = 'site_option_fetcher';

    /**
     * @return string
     */
    protected function getTransientHookName()
    {
        return 'acf/save_post';
    }

    /**
     * Hooks to remove cached items on save
     */
    public function clearTransientHook()
    {
        $action = $this->getTransientHookName();

        add_action($action, function ($id) {
            if ($id !== 'options') {
                return;
            }

            $this->clearTransient();
        }, 10, 3);
    }

    /**
     * @param string $field
     * @param string $page
     *
     * @return bool|mixed|null|void
     * @throws \Exception
     */
    public function fetchOne($field = '', $page = 'option')
    {
        return parent::fetchOne($field, $page);
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
}