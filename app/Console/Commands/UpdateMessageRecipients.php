<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\Message;
use Illuminate\Console\Command;

class UpdateMessageRecipients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:update-recipients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing messages with recipient information from customer contacts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating message recipients...');

        $messages = Message::whereNull('recipient_name')
            ->whereNull('recipient_email')
            ->with('customer')
            ->get();

        $updated = 0;

        foreach ($messages as $message) {
            if (! $message->customer) {
                continue;
            }

            // Get the first contact for this customer
            $contact = Contact::where('customer_id', $message->customer_id)
                ->whereNotNull('email')
                ->first();

            if ($contact) {
                $message->update([
                    'recipient_name' => $contact->name,
                    'recipient_email' => $contact->email,
                    'contact_id' => $contact->id,
                ]);
                $updated++;
            }
        }

        $this->info("Updated {$updated} messages with recipient information.");

        return 0;
    }
}
