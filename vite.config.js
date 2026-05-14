import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import html from '@rollup/plugin-html';
import { glob } from 'glob';
import path from 'path';
import iconsPlugin from './vite.icons.plugin.js';

/**
 * Get Files from a directory
 * @param {string} query
 * @returns array
 */
function GetFilesArray(query) {
  return glob.sync(query);
}

// Page JS Files
const pageJsFiles = GetFilesArray('resources/assets/js/*.js');

// Processing Vendor JS Files
const vendorJsFiles = GetFilesArray('resources/assets/vendor/js/*.js');

// Processing Libs JS Files
const LibsJsFiles = GetFilesArray('resources/assets/vendor/libs/**/*.js');

// Processing Libs Scss & Css Files
const LibsScssFiles = GetFilesArray('resources/assets/vendor/libs/**/!(_)*.scss');
const LibsCssFiles = GetFilesArray('resources/assets/vendor/libs/**/*.css');

// Processing Core, Themes & Pages Scss Files
const CoreScssFiles = GetFilesArray('resources/assets/vendor/scss/**/!(_)*.scss');

// Processing Fonts Scss & JS Files
const FontsScssFiles = GetFilesArray('resources/assets/vendor/fonts/!(_)*.scss');
const FontsJsFiles = GetFilesArray('resources/assets/vendor/fonts/**/!(_)*.js');
const FontsCssFiles = GetFilesArray('resources/assets/vendor/fonts/**/!(_)*.css');
const publicOnlyBuild = process.env.DD_BUILD_PUBLIC_ONLY === 'true';
const fullTemplateBuild = process.env.DD_BUILD_FULL === 'true';

const publicFrontInputs = [
  'resources/assets/vendor/fonts/iconify/iconify.css',
  'resources/assets/vendor/scss/front-core.scss',
  'resources/assets/css/demo.css',
  'resources/assets/vendor/scss/pages/front-page.scss',
  'resources/assets/vendor/scss/pages/front-page-landing.scss',
  'resources/assets/vendor/libs/swiper/swiper.scss',
  'resources/assets/vendor/js/helpers.js',
  'resources/assets/js/front-config.js',
  'resources/assets/vendor/libs/popper/popper.js',
  'resources/assets/vendor/js/bootstrap.js',
  'resources/assets/vendor/libs/swiper/swiper.js',
  'resources/assets/js/front-main.js',
  'resources/assets/js/dd-theme-switcher.js',
  'resources/assets/js/dd-cookie-consent.js',
  'resources/assets/js/front-page-landing.js'
];

const productionInputs = [
  ...publicFrontInputs,
  'resources/css/app.css',
  'resources/js/app.js',
  'resources/assets/vendor/scss/core.scss',
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
  'resources/assets/vendor/scss/pages/page-auth.scss',
  'resources/assets/vendor/libs/jquery/jquery.js',
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
  'resources/assets/vendor/libs/hammer/hammer.js',
  'resources/assets/vendor/js/menu.js',
  'resources/assets/js/config.js',
  'resources/assets/js/main.js',
  'resources/assets/vendor/libs/quill/typography.scss',
  'resources/assets/vendor/libs/quill/editor.scss',
  'resources/assets/js/dd-admin-pages.js'
];

const fullTemplateInputs = [
  'resources/css/app.css',
  'resources/assets/css/demo.css',
  'resources/js/app.js',
  ...pageJsFiles,
  ...vendorJsFiles,
  ...LibsJsFiles,
  'resources/js/laravel-user-management.js',
  ...CoreScssFiles,
  ...LibsScssFiles,
  ...LibsCssFiles,
  ...FontsScssFiles,
  ...FontsJsFiles,
  ...FontsCssFiles
];

const uniqueInputs = inputs => [...new Set(inputs)];

// Processing Window Assignment for Libs like jKanban, pdfMake
function libsWindowAssignment() {
  return {
    name: 'libsWindowAssignment',

    transform(src, id) {
      if (id.includes('jkanban.js')) {
        return src.replace('this.jKanban', 'window.jKanban');
      } else if (id.includes('vfs_fonts')) {
        return src.replaceAll('this.pdfMake', 'window.pdfMake');
      }
    }
  };
}

export default defineConfig({
  plugins: [
    laravel({
      input: publicOnlyBuild
        ? publicFrontInputs
        : uniqueInputs(fullTemplateBuild ? fullTemplateInputs : productionInputs),
      refresh: true
    }),
    html(),
    libsWindowAssignment(),
    iconsPlugin()
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources')
    }
  },
  json: {
    stringify: true // Helps with JSON import compatibility
  },
  build: {
    target: 'es2020',
    minify: 'esbuild',
    sourcemap: false,
    chunkSizeWarningLimit: 1500,
    commonjsOptions: {
      include: [/node_modules/] // Helps with importing CommonJS modules
    },
    rollupOptions: {
      output: {
        entryFileNames: 'assets/[name]-[hash].js',
        chunkFileNames: 'assets/[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash][extname]'
      }
    }
  }
});
