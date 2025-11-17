<?php

class XMLAmpersandEscaper
{
    private const UNESCAPED_AMPERSAND_PATTERN = '/&(?!(?:amp|lt|gt|quot|apos|#\d+|#x[0-9a-fA-F]+);)/';

    public static function escapeAmpersands(string $content): string
    {
        if (empty($content)) {
            return $content;
        }

        $result = preg_replace_callback(
            self::UNESCAPED_AMPERSAND_PATTERN,
            function ($matches) {
                return '&amp;';
            },
            $content
        );

        return $result;
    }

    public static function validateXmlAmpersands(string $content): bool
    {
        if (empty($content)) {
            return true;
        }

        $previousError = libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $success = $dom->loadXML($content);

        libxml_use_internal_errors($previousError);

        return $success;
    }

    public static function fixAndValidate(string $content): array
    {
        $originalValid = self::validateXmlAmpersands($content);

        if ($originalValid) {
            return [
                'content' => $content,
                'valid' => true,
                'original_valid' => true,
                'fixed' => false
            ];
        }

        $fixedContent = self::escapeAmpersands($content);
        $fixedValid = self::validateXmlAmpersands($fixedContent);

        return [
            'content' => $fixedContent,
            'valid' => $fixedValid,
            'original_valid' => false,
            'fixed' => true
        ];
    }
}
