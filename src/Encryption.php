<?php
namespace Industrious\WpHelpers;

class Encryption
{
    const CIPHER = 'AES-128-CBC';
    const OPENSSL_OPTIONS = 0;

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
        return base64_encode(
            openssl_encrypt(
                $string,
                self::CIPHER,
                $this->getKey(),
                self::OPENSSL_OPTIONS,
                $this->getInitializationVector()
            )
        );
    }

    /**
     * @param $data
     * @return string|null
     */
    public function decrypt($data): ?string
    {
        return openssl_decrypt(
            base64_decode($data),
            self::CIPHER,
            $this->getKey(),
            self::OPENSSL_OPTIONS,
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
