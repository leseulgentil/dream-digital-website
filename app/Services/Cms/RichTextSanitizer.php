<?php

namespace App\Services\Cms;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\File;

class RichTextSanitizer
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $cachePath = storage_path('framework/cache/htmlpurifier');
        File::ensureDirectoryExists($cachePath);

        $config->set('Cache.SerializerPath', $cachePath);
        $config->set('HTML.Allowed', implode(',', config('dream-digital.cms.rich_text.allowed_html', [])));
        $config->set('URI.AllowedSchemes', config('dream-digital.cms.rich_text.allowed_schemes', []));
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('HTML.TargetBlank', true);
        $config->set('HTML.Nofollow', true);
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);

        $this->purifier = new HTMLPurifier($config);
    }

    public function clean(?string $html): string
    {
        $clean = trim($this->purifier->purify((string) $html));

        return $this->removeDisallowedImages($clean);
    }

    private function removeDisallowedImages(string $html): string
    {
        if ($html === '' || ! str_contains($html, '<img')) {
            return $html;
        }

        $allowedPrefixes = config('dream-digital.cms.rich_text.allowed_image_prefixes', ['/img/cms/pages/']);

        return (string) preg_replace_callback('/<img\b[^>]*\bsrc=(["\']?)([^"\'>\s]+)\1[^>]*>/i', function (array $matches) use ($allowedPrefixes): string {
            $src = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            foreach ($allowedPrefixes as $prefix) {
                if (str_starts_with($src, $prefix)) {
                    return $matches[0];
                }
            }

            return '';
        }, $html);
    }
}
