<?php

namespace App\Services;

use Jobcloud\Kafka\Message\KafkaProducerMessage;
use Jobcloud\Kafka\Producer\KafkaProducer;
use Jobcloud\Kafka\Producer\KafkaProducerBuilder;

class KafkaProducerService
{
    protected KafkaProducer $producer;

    public function __construct()
    {
        $builder = KafkaProducerBuilder::create()
            ->withAdditionalBroker('localhost:9092');
        $this->producer = $builder->build();
    }

    public function sendMessage(string $topic, array $data): bool
    {
        try {
            $message = KafkaProducerMessage::create($topic, 0)
                ->withBody(json_encode($data))
                ->withKey($data['key'] ?? null);

            $this->producer->produce($message);
            $this->producer->flush(1); // <-- Đảm bảo Kafka gửi

            return true;
        } catch (\Throwable $e) {
            // Log hoặc throw lại nếu cần
            \Log::error('Kafka send error: ' . $e->getMessage());
            return false;
        }
    }
}
