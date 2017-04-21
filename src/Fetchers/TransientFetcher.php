<?php
namespace Industrious\WpHelpers\Fetchers;

/**
 * Class TransientFetcher
 */
abstract class TransientFetcher
{
    /**
     * Cache/Transient Expiry Length
     * @var
     */
    protected $transient_exipiry = 43200; // 12 Hours

    /**
     * Cache/Transient Key
     * @var
     */
    protected $transient_key;

    /**
     * Toggle to Enable/Disable Cache
     * @var
     */
    protected $cache_enabled = false;

    /**
     * TransientFetcher constructor.
     *
     * Sets up the action hook for clearing cache
     */
    public function __construct()
    {
        $this->clearTransientHook();
    }

    /**
     * Hooks to remove cached items on save
     */
    public function clearTransientHook()
    {
        $action = $this->getTransientHookName();

        if (!$action) {
            return;
        }

        add_action($action, function ($id, $post, $is_update) {
            $this->clearTransient();
        }, 10, 3);
    }

    /**
     * Function to clear the transient/cache
     */
    public function clearTransient()
    {
        delete_transient($this->getTransientKey());
    }

    /**
     * Fetch the Transient if Caching is enabled
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getTransient($key = '')
    {
        return $this->cache_enabled
            ? get_transient($key)
            : false;
    }

    /**
     * Set the Transient if Caching is enabled
     *
     * @param $data
     *
     * @return $this
     */
    public function setTransient($data)
    {
        if ($this->cache_enabled) {
            set_transient($this->getTransientKey(), $data, $this->transient_exipiry);
        }

        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getTransientKey()
    {
        if ($this->transient_key) {
            return $this->transient_key;
        }

        throw new \Exception('No Transient Key set for ' . get_called_class());
    }

    /**
     * @return string
     */
    abstract protected function getTransientHookName();

    /**
     * Fetch all from either a stored transient, or database query.
     *
     * @param array $args
     *
     * @return WP_Query|mixed
     */
    abstract public function fetchAll($args);
}
