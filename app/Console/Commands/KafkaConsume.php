<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Jobcloud\Kafka\Exception\KafkaConsumerConsumeException;
use Jobcloud\Kafka\Exception\KafkaConsumerEndOfPartitionException;
use Jobcloud\Kafka\Exception\KafkaConsumerTimeoutException;
use Jobcloud\Kafka\Message\KafkaConsumerMessageInterface;
use Jobcloud\Kafka\Consumer\KafkaConsumerBuilder;

class KafkaConsume extends Command
{
    protected $signature = 'kafka:consume {--reset-offset : Reset offset to earliest}';
    protected $description = 'Consume Kafka messages';

    public function handle()
    {
        $resetOffset = $this->option('reset-offset');
        
        $additionalConfig = [
            'enable.auto.commit' => 'false', // Manual commit
            'session.timeout.ms' => 30000,
            'max.poll.interval.ms' => 300000,
            'fetch.wait.max.ms' => 1000,
            'auto.offset.reset' => $resetOffset ? 'earliest' : 'latest',
            'enable.partition.eof' => 'false',
        ];

        $this->info('🚀 Starting Kafka consumer with config:');
        $this->info('- Broker: localhost:9092');
        $this->info('- Group: laravel-group');
        $this->info('- Topic: chat-messages');
        $this->info('- Auto offset reset: ' . $additionalConfig['auto.offset.reset']);

        // Không sử dụng callback trong builder, thay vào đó xử lý manual
        $consumer = KafkaConsumerBuilder::create()
            ->withAdditionalConfig($additionalConfig)
            ->withAdditionalBroker('localhost:9092')
            ->withConsumerGroup('laravel-group')
            ->withAdditionalSubscription('chat-messages')
            ->build();

        logger()->info('Consumer started, subscribing to topic: chat-messages');
        $this->info('🔄 Consumer started, waiting for messages...');
        $consumer->subscribe();

        $messageCount = 0;
        while (true) {
            try {
                $this->line('👀 Polling for messages...');
                $message = $consumer->consume();
                
                if ($message !== null) {
                    $messageCount++;
                    $this->info("📬 Message #{$messageCount} received!");
                    
                    // DEBUG: Check message properties first
                    $this->info("🔍 Raw message inspection:");
                    $this->line("   Class: " . get_class($message));
                    $this->line("   Topic: " . ($message->getTopicName() ?? 'NULL'));
                    $this->line("   Partition: " . ($message->getPartition() ?? 'NULL'));
                    $this->line("   Offset: " . ($message->getOffset() ?? 'NULL'));
                    
                    $body = $message->getBody();
                    $this->line("   Body length: " . (is_string($body) ? strlen($body) : 'NOT STRING'));
                    $this->line("   Body type: " . gettype($body));
                    $this->line("   Body content: " . (is_string($body) ? $body : json_encode($body)));
                    
                    // Xử lý message manually thay vì dựa vào callback
                    $this->processMessage($message);
                    
                    // Commit message
                    $this->info("🔄 Committing message...");
                    $consumer->commit($message);
                    $this->info("✅ Message committed successfully");
                } else {
                    $this->line('⏳ No message received in this poll (message is null)');
                }
                
            } catch (KafkaConsumerTimeoutException $e) {
                $this->line('⏰ Kafka consumer timed out, no messages received.');
                logger()->info('Kafka consumer timed out, no messages received.');
                usleep(100000); // Sleep for 100ms
            } catch (KafkaConsumerEndOfPartitionException $e) {
                $this->line('🏁 Reached end of partition: ' . $e->getMessage());
                logger()->warning('Reached end of partition: ' . $e->getMessage());
                usleep(500000); // Sleep for 500ms
            } catch (KafkaConsumerConsumeException $e) {
                $this->error('❌ Failed to consume message: ' . $e->getMessage());
                logger()->error('Failed to consume message: ' . $e->getMessage());
                usleep(1000000); // Sleep for 1s before retry
            } catch (\Exception $e) {
                $this->error('💥 Unexpected error: ' . $e->getMessage());
                logger()->error('Unexpected error in consumer', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                usleep(5000000); // Sleep for 5s before retry
            }
        }
    }

    /**
     * Process received message
     */
    private function processMessage(KafkaConsumerMessageInterface $message): void
    {
        try {
            $this->info("🔥 PROCESSING MESSAGE - Callback triggered!");
            
            $body = $message->getBody();
            $this->info("📨 Raw message body: " . $body);
            
            // Log message details
            $this->line("   Topic: " . $message->getTopicName());
            $this->line("   Partition: " . $message->getPartition());
            $this->line("   Offset: " . $message->getOffset());
            $this->line("   Key: " . ($message->getKey() ?? 'null'));
            $this->line("   Timestamp: " . ($message->getTimestamp() ?? 'null'));
            
            $data = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                logger()->info('🎯 Received Kafka message', [
                    'topic' => $message->getTopicName(),
                    'partition' => $message->getPartition(),
                    'offset' => $message->getOffset(),
                    'timestamp' => $message->getTimestamp(),
                    'key' => $message->getKey(),
                    'data' => $data
                ]);
                
                $this->info("✅ Message processed successfully");
                $this->line("   Parsed Data: " . json_encode($data, JSON_PRETTY_PRINT));
                
                // Thêm logic xử lý business ở đây
                $this->handleBusinessLogic($data);
                
            } else {
                $errorMsg = 'Failed to decode JSON message: ' . json_last_error_msg();
                logger()->error($errorMsg, ['body' => $body]);
                $this->error("❌ " . $errorMsg);
            }
        } catch (\Exception $e) {
            $this->error("💥 Error in processMessage: " . $e->getMessage());
            logger()->error('Error in processMessage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle business logic for the message
     */
    private function handleBusinessLogic(array $data): void
    {
        try {
            $this->info("🎯 Handling business logic...");
            
            // Thêm logic xử lý business ở đây
            // Ví dụ: lưu vào database, gửi notification, etc.
            
            if (isset($data['type'])) {
                $this->line("   Message Type: " . $data['type']);
            }
            
            if (isset($data['user_id'])) {
                $this->line("   User ID: " . $data['user_id']);
            }
            
            if (isset($data['message'])) {
                $this->line("   Message Content: " . $data['message']);
            }
            
            // Simulate processing time
            usleep(100000); // 100ms
            
            $this->info("✅ Business logic completed");
            
        } catch (\Exception $e) {
            $this->error("💥 Error in business logic: " . $e->getMessage());
            throw $e; // Re-throw để không commit message nếu có lỗi
        }
    }
}