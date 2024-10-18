@if (!empty($breadcrumbs))
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item @if (isset($breadcrumb['active'])) active @endif"
                    @if (isset($breadcrumb['active'])) aria-current="page" @endif>
                    @if (isset($breadcrumb['link']))
                        <a href="{{ $breadcrumb['link'] }}">
                            <i class="bi {{ $breadcrumb['icon'] }}"></i> {{ $breadcrumb['title'] }}
                        </a>
                    @else
                        <i class="bi {{ $breadcrumb['icon'] }}"></i> {{ $breadcrumb['title'] }}
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif

@push('css')
    <style>
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 0.875rem;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #007bff;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }
    </style>
@endpush
