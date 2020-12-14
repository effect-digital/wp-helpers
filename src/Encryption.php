<?php
namespace Industrious\WpHelpers;

class Encryption
{
    const CIPHER = 'AES-128-CBC';

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $initializationVector;

    /**
     * @param string $key
     * @param string $initializationVector
     */
    public function __construct(string $key, string $initializationVector)
    {
        $this->key = $key;
        $this->initializationVector = $initializationVector;
    }

    /**
     * @param string $string
     * @return string|null
     */
    public function encrypt(string $string): ?string
    {
        return openssl_encrypt(
            $string,
            self::CIPHER,
            $this->getKey(),
            null,
            $this->getInitializationVector()
        );
    }

    /**
     * @param $data
     * @return string|null
     */
    public function decrypt($data): ?string
    {
        return openssl_decrypt(
            $data,
            self::CIPHER,
            $this->getKey(),
            null,
            $this->getInitializationVector()
        );
    }

    /**
     * @return string
     */
    private function getKey(): string
    {
        return substr($this->key, 0, 16);
    }

    /**
     * @return string
     */
    private function getInitializationVector(): string
    {
        return substr($this->initializationVector, 0, 16);
    }
}
