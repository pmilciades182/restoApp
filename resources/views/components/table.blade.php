<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                {{ $head }}
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {{ $body }}
        </tbody>
        @if(isset($foot))
        <tfoot>
            {{ $foot }}
        </tfoot>
        @endif
    </table>
</div>
