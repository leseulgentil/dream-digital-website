<!-- BEGIN: Theme CSS-->
@vite(['resources/assets/vendor/fonts/iconify/iconify.css'])

<!-- Vendor Styles -->
@yield('vendor-style')

<!-- Core CSS -->
@vite(['resources/assets/vendor/scss/front-core.scss', 'resources/assets/css/demo.css', 'resources/assets/vendor/scss/pages/front-page.scss'])

<!-- Page Styles -->
@yield('page-style')
