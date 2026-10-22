<?php

namespace App\Services\Notifications;

use App\Models\PushSubscription;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushTransportDispatcher
{
    protected ?WebPush $webPush = null;

    public function __construct()
    {
        $this->ensureOpenSslConfig();

        $vapidConfig = config('webpush.vapid', []);
        $subject = $vapidConfig['subject'] ?? 'mailto:admin@bbpom.go.id';
        $publicKey = $vapidConfig['public_key'] ?? '';
        $privateKey = $vapidConfig['private_key'] ?? '';

        if (!empty($publicKey) && !empty($privateKey)) {
            try {
                $this->webPush = new WebPush(
                    ['VAPID' => ['subject' => $subject, 'publicKey' => $publicKey, 'privateKey' => $privateKey]],
                    ['urgency' => 'high', 'TTL' => 86400],
                    $this->createHttpClient()
                );
                $this->webPush->setReuseVAPIDHeaders(true);
            } catch (\Throwable $e) {
                Log::warning('WebPush initialization warning: ' . $e->getMessage());
                $this->webPush = null;
            }
        }
    }

    public function dispatch($subscriptions, string $title, string $body, ?string $url = null, array $extra = []): int
    {
        if (!$this->webPush) {
            return 0;
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => asset('images/logo-bpom.png'),
            'badge' => asset('images/logo-bpom.png'),
            'tag' => $extra['tag'] ?? 'bbpom-helpdesk-alert',
            'data' => array_merge(['url' => $url ?: url('/admin')], $extra),
        ]);

        $sentCount = 0;
        $failedSubIds = [];

        foreach ($subscriptions as $sub) {
            try {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?: 'aes128gcm',
                ]);
                $this->webPush->queueNotification($subscription, $payload, ['urgency' => 'high', 'TTL' => 86400]);
            } catch (\Throwable $e) {
                Log::warning("Failed to queue push notification for sub #{$sub->id}: " . $e->getMessage());
            }
        }

        try {
            foreach ($this->webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    $sentCount++;
                } else {
                    Log::info("Push notification failed for {$endpoint}: " . $report->getReason());
                    if ($report->isSubscriptionExpired()) {
                        $expiredSub = $subscriptions->firstWhere('endpoint', $endpoint);
                        if ($expiredSub) {
                            $failedSubIds[] = $expiredSub->id;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('WebPush flush error: ' . $e->getMessage());
        }

        if (!empty($failedSubIds)) {
            PushSubscription::whereIn('id', $failedSubIds)->delete();
        }

        return $sentCount;
    }

    protected function createHttpClient(): Client
    {
        $options = ['timeout' => 15, 'connect_timeout' => 10];
        $caBundle = config('webpush.ca_bundle');
        if ($caBundle && file_exists($caBundle)) {
            $options['verify'] = $caBundle;
        } elseif (file_exists(storage_path('cacert.pem'))) {
            $options['verify'] = storage_path('cacert.pem');
        }
        return new Client($options);
    }

    protected function ensureOpenSslConfig(): void
    {
        if (PHP_OS_FAMILY === 'Windows' && !getenv('OPENSSL_CONF')) {
            $candidate = dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'openssl.cnf';
            if (file_exists($candidate)) {
                putenv("OPENSSL_CONF={$candidate}");
            }
        }
    }
}
