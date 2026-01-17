<?php

namespace App\Services;

use App\Models\MailTemplate;
use Illuminate\Support\Facades\Log;

class MailTemplateService
{
    /**
     * Render a mail template by its system key.
     *
     * @param  string  $key  System key of the template
     * @param  array  $data  Data to replace variables with
     * @return array [subject, content]
     */
    public function render(string $key, array $data = []): array
    {
        $template = MailTemplate::where('system_key', $key)->first();

        if (! $template) {
            Log::warning("Mail template not found for key: {$key}");

            return ['', ''];
        }

        $subject = $this->parseTemplate($template->subject, $data);
        $content = $this->parseTemplate($template->content, $data);

        return [
            'subject' => $subject,
            'content' => $content,
        ];
    }

    /**
     * Parse variables in a string.
     */
    private function parseTemplate(string $text, array $data): string
    {
        // Simple string replacement for now.
        // We can make it more advanced (dot notation, etc.) if needed.
        return strtr($text, $data);
    }
}
