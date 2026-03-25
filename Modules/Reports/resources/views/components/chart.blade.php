@props(['chartId', 'config'])

@php
    $configJson = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
@endphp

<canvas id="{{ $chartId }}" data-chart-config='@json($config)'></canvas>

@once
    @push('scripts')
        <script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        (() => {
            const chartId = @js($chartId);
            const rawConfig = {!! $configJson ?: '{}' !!};

            const drawChart = () => {
                if (typeof window.Chart === 'undefined') {
                    return;
                }

                const canvas = document.getElementById(chartId);

                if (!canvas) {
                    return;
                }

                window.__crmCharts = window.__crmCharts || {};

                if (window.__crmCharts[chartId]) {
                    window.__crmCharts[chartId].destroy();
                }

                window.__crmCharts[chartId] = new window.Chart(canvas, rawConfig);
            };

            document.addEventListener('DOMContentLoaded', drawChart);
            document.addEventListener('livewire:navigated', drawChart);
            drawChart();
        })();
    </script>
@endpush
