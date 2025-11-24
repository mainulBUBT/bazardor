<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            @php
                $appName = \App\Models\Setting::where('key_name', 'company_name')->first();
                $appNameValue = $appName && isset($appName->value) ? $appName->value : 'BazarDor';
            @endphp
            <span>Copyright &copy; {{ $appNameValue }} {{ date('Y') }}</span>
        </div>
    </div>
</footer>
<!-- End of Footer -->