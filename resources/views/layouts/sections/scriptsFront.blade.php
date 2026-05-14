<!-- BEGIN: Vendor JS-->
@vite(['resources/assets/vendor/libs/popper/popper.js', 'resources/assets/vendor/js/bootstrap.js'])

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/front-main.js'])
<!-- END: Theme JS-->
<!-- BEGIN: Theme Switcher (Q13) -->
@vite(['resources/assets/js/dd-theme-switcher.js'])
<!-- END: Theme Switcher -->
<!-- BEGIN: Cookie Consent -->
@vite(['resources/assets/js/dd-cookie-consent.js'])
<!-- END: Cookie Consent -->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->
