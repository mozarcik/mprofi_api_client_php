<?php

namespace mprofi;

/**
 * Description here...
 *
 * @author Michał Motyczko <michal@motyczko.pl>
 */
class Status
{
    const PROCESSING = 'processing';
    const SENT = 'sent';
    const DELIVERED = 'delivered';
    const NOT_DELIVERED = 'not delivered';
    const ERROR = 'error';
    const UNKNOWN = 'unknown';
    
    /**
     * @var string message status. One of {@link PROCESSING}, {@link SENT}, {@link DELIVERED}, {@link NOT_DELIVERED}, {@link ERROR} or {@link UNKNOWN}
     */
    private $status;
    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
    private $reference;
    /**
     * @var
     */
    private $ts;

    public function __construct($status, $id, $reference, $ts)
    {
        $availableStatuses = [self::PROCESSING, self::SENT, self::DELIVERED, self::NOT_DELIVERED, self::ERROR, self::UNKNOWN];
        if (!in_array($status, $availableStatuses)) {
            throw new \InvalidArgumentException('status can be one of ' . implode(', ', $availableStatuses));
        }
        $this->status = $status;
        $this->id = $id;
        $this->reference = $reference;
        $this->ts = $ts;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return mixed
     */
    public function getTs()
    {
        return $this->ts;
    }
}