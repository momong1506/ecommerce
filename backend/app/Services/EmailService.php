<?php

namespace App\Services;

use App\Jobs\SendOrderConfirmationEmail;
use App\Models\Order;
use App\Models\EmailLog;
use Exception;

/**
 * Email Service
 *
 * Handles email notification management including sending
 * order confirmations and tracking email delivery status
 * with retry logic.
 */
class EmailService
{
    /**
     * Maximum number of retry attempts for failed emails
     */
    const MAX_RETRIES = 3;

    /**
     * Send order confirmation email
     *
     * Creates an email log entry and queues the email job
     * for asynchronous processing.
     *
     * @param Order $order
     * @return EmailLog
     */
    public function sendOrderConfirmation(Order $order): EmailLog
    {
        // Create email log entry
        $emailLog = EmailLog::create([
            'order_id' => $order->id,
            'recipient_email' => $order->customer_email,
            'status' => 'pending',
            'retry_count' => 0,
        ]);

        // Queue email job for asynchronous processing with retry logic
        dispatch(new SendOrderConfirmationEmail($order, $emailLog));

        return $emailLog;
    }

    /**
     * Mark email as sent successfully
     *
     * @param int $emailLogId
     * @return void
     */
    public function markAsSent(int $emailLogId): void
    {
        $emailLog = EmailLog::find($emailLogId);

        if ($emailLog) {
            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }
    }

    /**
     * Mark email as failed
     *
     * @param int $emailLogId
     * @param string $errorMessage
     * @return void
     */
    public function markAsFailed(int $emailLogId, string $errorMessage): void
    {
        $emailLog = EmailLog::find($emailLogId);

        if ($emailLog) {
            $emailLog->increment('retry_count');
            $emailLog->update([
                'error_message' => $errorMessage,
            ]);

            // Mark as permanently failed if max retries exceeded
            if ($emailLog->retry_count >= self::MAX_RETRIES) {
                $emailLog->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                ]);
            }
        }
    }

    /**
     * Retry sending a failed email
     *
     * @param int $emailLogId
     * @return bool
     * @throws Exception
     */
    public function retryEmail(int $emailLogId): bool
    {
        $emailLog = EmailLog::with('order')->find($emailLogId);

        if (!$emailLog) {
            throw new Exception("Email log {$emailLogId} not found");
        }

        if ($emailLog->retry_count >= self::MAX_RETRIES) {
            throw new Exception("Maximum retry attempts exceeded");
        }

        if ($emailLog->status === 'sent') {
            throw new Exception("Email already sent successfully");
        }

        // Reset status to pending for retry
        $emailLog->update([
            'status' => 'pending',
        ]);

        // Queue retry job
        // dispatch(new SendOrderConfirmationEmail($emailLog->order, $emailLog));

        return true;
    }

    /**
     * Get email logs for an order
     *
     * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrderEmailLogs(int $orderId)
    {
        return EmailLog::where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get failed email logs that can be retried
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFailedEmails()
    {
        return EmailLog::where('status', 'pending')
            ->where('retry_count', '<', self::MAX_RETRIES)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
