<?php
namespace Industrious\WpHelpers\Fetchers\Other;

use TwitterAPIExchange;
use Industrious\WpHelpers\Fetchers\TransientFetcher;

/**
 * Class TweetFetcher
 */
class TweetFetcher extends TransientFetcher
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
    protected $transient_key = 'twitter_fetcher';

    /**
     * TweetFetcher constructor.
     * Sets up the Twitter Client
     */
    public function __construct()
    {
        $this->client = new TwitterAPIExchange([
            'oauth_access_token'        => getenv('TWITTER_OAUTH_ACCESS_TOKEN'),
            'oauth_access_token_secret' => getenv('TWITTER_OAUTH_ACCESS_TOKEN_SECRET'),
            'consumer_key'              => getenv('TWITTER_CONSUMER_KEY'),
            'consumer_secret'           => getenv('TWITTER_CONSUMER_SECRET')
        ]);

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
            'screen_name'       => isset($query['screen_name']) ? $query['screen_name'] : null,
            'tweet'             => isset($query['tweets']) && $query['tweets']
                ? reset($query['tweets'])
                : null
        ];
    }

    /**
     * Fetch Tweets from Twitter
     *
     * @param array $args
     * @return array|string
     */
    public function fetchAll($args = [])
    {
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

        $args = array_merge([
            'count'             => 2,
            'exclude_replies'   => TRUE,
            'include_rts'       => TRUE
        ], $args);

        $response = $this->execute('GET', $url, $args);

        $data = json_decode($response);

        if(!$data)
        {
            return [
                'error'    => $response
            ];
        }

        return [
            'user' => [
                'name' => $data[0]->user->name,
                'handle' => $data[0]->user->screen_name,
                'icon' => $data[0]->user->profile_image_url_https
            ],
            'tweets'        => $this->presentTweets($data)
        ];
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
    private function execute($method = 'GET', $url = '', $args = [])
    {
        $curl_options = [
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ];

        try
        {
            if ($method == 'POST') {
                return $this->client
                    ->buildOauth($url, 'POST')
                    ->setPostfields($args)
                    ->performRequest(true, $curl_options);
            }

            //  See if we've got a transient available
            if ($query = $this->getTransient($this->getTransientKey())) {
                return $query;
            }

            $request = $this->client
                ->setGetfield('?' . http_build_query($args))
                ->buildOauth($url, 'GET')
                ->performRequest(true, $curl_options);

            $response = json_decode($request);

            if(isset($response->errors))
            {
                throw new \Exception($response->errors[0]->message);
            }

            $this->setTransient($request);

            return $request;
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }
    }

    /**
     * @param array $tweets
     *
     * @return array
     */
    private function presentTweets($tweets = [])
    {
        return array_map(function($tweet)
        {
            return (object) [
                'id' => $tweet->id,
                'created_at' => $tweet->created_at,
                'relative_time' => $this->relative_time($tweet->created_at),
                'content' => $this->twitterify($tweet->text)
            ];
        }, $tweets);
    }

    /**
     * @param string $content
     *
     * @return mixed|string
     */
    private function twitterify($content = '')
    {
        $content = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $content);
        $content = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $content);
        $content = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $content);
        $content = preg_replace("/#(\w+)/", "<a href=\"http://twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $content);

        return $content;
    }

    private function relative_time($time = '')
    {
        $time = time() - strtotime($time);

        $time = ($time < 1) ? 1 : $time;

        $tokens = [
            31536000 => 'y',
            2592000 => 'm',
            604800 => 'w',
            86400 => 'd',
            3600 => 'h',
            60 => 'm',
            1 => 's'
        ];

        // $tokens = [
        //     31536000 => 'year',
        //     2592000 => 'month',
        //     604800 => 'week',
        //     86400 => 'day',
        //     3600 => 'hour',
        //     60 => 'minute',
        //     1 => 'second'
        // ];

        foreach ($tokens as $unit => $text)
        {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.$text;
            // return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
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
