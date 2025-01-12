<!-- resources/views/components/breadcrumbs.blade.php -->
@props(['breadcrumbs'])

<nav class="bg-white border-b border-gray-200 px-4 py-3">
    <div class="max-w-7xl mx-auto">
        <ol class="flex items-center space-x-2 text-sm">
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                </a>
            </li>
            @foreach($breadcrumbs as $breadcrumb)
                <li class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    @if(isset($breadcrumb['route']))
                        @php
                            $routeParams = [];
                            if (isset($breadcrumb['params'])) {
                                $routeParams = is_array($breadcrumb['params'])
                                    ? $breadcrumb['params']
                                    : ['id' => $breadcrumb['params']];
                            }
                        @endphp
                        <a href="{{ route($breadcrumb['route'], $routeParams) }}"
                           class="ml-2 text-gray-500 hover:text-gray-700 {{ isset($breadcrumb['class']) ? $breadcrumb['class'] : '' }}">
                            {{ $breadcrumb['name'] }}
                        </a>
                    @else
                        <span class="ml-2 text-gray-900">{{ $breadcrumb['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
</nav>
