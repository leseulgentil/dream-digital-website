<?php

namespace Tests\Feature;

use Tests\TestCase;

class BuildConfigurationTest extends TestCase
{
    public function test_vite_has_lean_production_and_full_template_build_modes(): void
    {
        $package = json_decode(file_get_contents(base_path('package.json')), true);
        $viteConfig = file_get_contents(base_path('vite.config.js'));

        $this->assertSame('vite build', $package['scripts']['build']);
        $this->assertSame('cross-env DD_BUILD_FULL=true vite build', $package['scripts']['build:full']);
        $this->assertStringContainsString('const productionInputs', $viteConfig);
        $this->assertStringContainsString('const fullTemplateBuild', $viteConfig);
        $this->assertStringContainsString('resources/assets/js/dd-admin-pages.js', $viteConfig);
    }
}
