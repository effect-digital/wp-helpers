<?php
namespace Industrious\WpHelpers\Fetchers\Other;

/**
 * Class CustomFieldFetcher
 * @package EIS\Other
 */
class CustomSubFieldFetcher extends CustomFieldFetcher
{
    /**
     * Cache/Transient Key
     * @var
     */
    protected $transient_key = 'custom_sub_field_fetcher';

    /**
     * @param      $field
     * @param null $page
     *
     * @return bool|mixed|null|void
     * @throws \Exception
     */
    public function execute($field, $page = null)
    {
        $query = $this->getTransient($this->getTransientKey());

        if ($query === false) {
            $query = get_sub_field($field, $page);

            $this->setTransient($query);
        }

        return $query;
    }
}
