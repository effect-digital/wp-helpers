<?php
namespace Industrious\WpHelpers\Fetchers\Other;

use Industrious\WpHelpers\Fetchers\TransientFetcher;

/**
 * Class InstagramFetcher
 */
class InstagramFetcher extends TransientFetcher
{
    /**
     * @var TwitterAPIExchange
     */
    private $client;

    /**
     * Cache/Transient Expiry Length
     * @var
     */
    protected $transient_exipiry = 900; // 15 Minutes

    /**
     * Toggle to Enable/Disable Cache
     * @var
     */
    protected $cache_enabled = TRUE;

    /**
     * Cache/Transient Key
     * @var
     */
    protected $transient_key = 'instagram_fetcher';

    /**
     * Access Token
     * @var
     */
    private $access_token = null;

    /**
     * InstagramFetcher constructor.
     * Sets up the Twitter Client
     */
    public function __construct()
    {
        if ($access_token = getenv('INSTAGRAM_ACCESS_TOKEN')) {
            $this->setAccessToken($access_token);
        }

        parent::__construct();
    }

    /**
     * @param array $args
     * @return array
     */
    public function fetchOne($args = [])
    {
        $args = array_merge([
            'count'             => 1,
        ], $args);

        $query = $this->fetchAll($args);

        return [
            'error'             => isset($query['error']) ? $query['error'] : null,
            'user'              => isset($query['user']) ? $query['user'] : null,
            'post'             => isset($query['posts']) && $query['posts']
                ? reset($query['posts'])
                : null
        ];
    }

    /**
     * Fetch Posts from Instagram
     *
     * @param array $args
     * @return array|string
     */
    public function fetchAll($args = [])
    {
        $url = 'https://api.instagram.com/v1/users/self/media/recent/';

        $args = array_merge([
            'access_token' => $this->access_token,
            'count' => 2
        ], $args);

        $this->transient_key = 'instagram_fetcher_' . sha1($this->access_token) . '_' . http_build_query($args);
        $response = $this->getTransient($this->transient_key) ?: $this->execute($url, $args);

        $data = json_decode($response);

        if (! $data || ! isset($data->data) || ! $data->data) {
            return [
                'error'    => $response
            ];
        }

        return [
            'user' => [
                'name' => $data->data[0]->user->full_name,
                'handle' => $data->data[0]->user->username,
                'icon' => $data->data[0]->user->profile_picture
            ],
            'posts'        => $this->presentPosts($data->data)
        ];
    }

    /**
     * @param string $access_token
     * @return InstagramFetcher
     */
    public function setAccessToken(string $access_token): InstagramFetcher
    {
        $this->access_token = $access_token;

        return $this;
    }

    /**
     * Execute a call to the Twitter API
     *
     * @param string $method
     * @param string $url
     * @param array  $args
     *
     * @return string
     * @throws \Exception
     */
    private function execute($url = '', $args = [])
    {
        try
        {
            $request = curl_init();

            curl_setopt($request, CURLOPT_URL, $url . '?' . http_build_query($args));
            curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($request);
            curl_close($request);

            if (!$response)
            {
                throw new \Exception('An error occurred in ' . __METHOD__);
            }

            $this->setTransient($response);

            return $response;
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }
    }

    /**
     * @param array $posts
     *
     * @return array
     */
    private function presentPosts($posts = [])
    {
        return array_map(function($post)
        {
            return (object) [
                'created_at'    => $post->created_time,
                'relative_time' => $this->relative_time($post->created_time),
                'link'          => $post->link,
                'image'         => $post->images->standard_resolution->url,
                'content'       => $post->caption->text
            ];
        }, $posts);
    }

    private function relative_time($time = '')
    {
        $time = time() - $time;

        $time = ($time < 1) ? 1 : $time;

        // $tokens = [
        //     31536000 => 'y',
        //     2592000 => 'm',
        //     604800 => 'w',
        //     86400 => 'd',
        //     3600 => 'h',
        //     60 => 'm',
        //     1 => 's'
        // ];

        $tokens = [
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        ];

        foreach ($tokens as $unit => $text)
        {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            // return $numberOfUnits.$text;
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
    }

    /**
     * @return string
     */
    protected function getTransientHookName()
    {
        // TODO: Implement getTransientHookName() method.
    }
}
