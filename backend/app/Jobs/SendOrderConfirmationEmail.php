<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\EmailLog;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Send Order Confirmation Email Job
 *
 * Handles sending order confirmation emails with retry logic
 * Retry schedule: 0min (immediate), 3min, 7min (exponential backoff)
 */
class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum number of retry attempts
     */
    public $tries = 3;

    /**
     * Calculate the number of seconds to wait before retrying the job
     * Attempt 1: 0 seconds (immediate)
     * Attempt 2: 180 seconds (3 minutes)
     * Attempt 3: 420 seconds (7 minutes)
     */
    public function backoff(): array
    {
        return [0, 180, 420];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Order $order,
        public EmailLog $emailLog
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(EmailService $emailService): void
    {
        try {
            // Send email using Laravel Mail
            Mail::send(
                'emails.order-confirmation',
                ['order' => $this->order->load('items.product')],
                function ($message) {
                    $message->to($this->order->customer_email, $this->order->customer_name)
                        ->subject('Order Confirmation - ' . $this->order->order_number);
                }
            );

            // Mark email as sent
            $emailService->markAsSent($this->emailLog->id);

            Log::info('Order confirmation email sent successfully', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'email_log_id' => $this->emailLog->id,
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to send order confirmation email', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'email_log_id' => $this->emailLog->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
            ]);

            // Mark as failed in EmailService
            $emailService->markAsFailed($this->emailLog->id, $e->getMessage());

            // Re-throw exception to trigger Laravel's retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // This method is called when the job has failed after all retry attempts
        Log::error('Order confirmation email permanently failed', [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'email_log_id' => $this->emailLog->id,
            'error' => $exception->getMessage(),
        ]);

        // Email log is already marked as failed by the EmailService
        // No need to update again
    }
}
