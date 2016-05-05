<?php

namespace mprofi;

/**
 * Description here...
 *
 * @author Michał Motyczko <michal@motyczko.pl>
 */
class Message implements \JsonSerializable
{
    /**
     * @var string recipient phone number
     */
    private $recipient;

    /**
     * @var string sms content message
     */
    private $content;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $apiKey;

    public function __construct($recipient, $content, $reference = null)
    {
        $this->recipient = trim($recipient);
        $this->content   = trim($content);
        $this->reference = trim($reference);

        if ($this->recipient === '') {
            throw new \InvalidArgumentException('Recipient cannot be empty');
        }

        if ($this->content === '') {
            throw new \InvalidArgumentException('Content cannot be empty');
        }
    }

    /**
     * @param $apiKey
     *
     * @internal
     */
    public function setApiKey($apiKey)
    {

    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->getPayload();
    }

    /**
     * Returns attributes array.
     *
     * @return array
     */
    public function getPayload()
    {
        $result = [
            'recipient' => $this->recipient,
            'message'   => $this->content,
            'reference' => $this->reference,
        ];

        if (trim($this->apiKey) !== '') {
            $result['apikey'] = $this->apiKey;
        }

        return $result;
    }
}