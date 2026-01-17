<?php

namespace App\Services;

use App\Models\MailTemplate;

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

        return $this->renderModel($template, $data);
    }

    /**
     * Render a mail template by its ID.
     */
    public function renderById(string $id, array $data = []): array
    {
        $template = MailTemplate::find($id);

        return $this->renderModel($template, $data);
    }

    /**
     * Shared render logic for model
     */
    protected function renderModel(?MailTemplate $template, array $data = []): array
    {
        if (! $template) {
            return [
                'subject' => '',
                'content' => '',
            ];
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
