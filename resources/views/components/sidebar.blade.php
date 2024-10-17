<div class="sidebar" id="sidebar">
    <p class="sidebar-title">Admin Page</p>
    <ul class="nav flex-column">
        @foreach ($sidebarItems as $item)
            <li class="sidebar-item">
                @if (isset($item['link']))
                    <a class="sidebar-item-link {{ $item['active'] ?? false ? 'active' : '' }}"
                        href="{{ $item['link'] }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        {{ $item['title'] }}
                    </a>
                @endif

                @if (isset($item['dropdown']))
                    <div class="dropdown">
                        <a class="sidebar-item-link sidebar-dropdown-toggle" href="#" id="settingsDropdown"
                            data-toggle="collapse" data-target="#settingsMenu{{ $loop->index }}" aria-expanded="false">
                            <i class="bi {{ $item['icon'] }}"></i>
                            {{ $item['title'] }}
                            <i class="bi bi-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="sidebar-collapse collapse" id="settingsMenu{{ $loop->index }}">
                            @foreach ($item['dropdown'] as $dropdownItem)
                                <li class="sidebar-item-dropdown">
                                    <a class="sidebar-item-link-dropdown" href="{{ $dropdownItem['link'] }}">
                                        <i class="bi {{ $dropdownItem['icon'] }}"></i>
                                        {{ $dropdownItem['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@push('script')
    <script>
        $(document).ready(function() {
            $('.sidebar-dropdown-toggle').on('click', function() {
                $(this).next('.sidebar-collapse').collapse('toggle');
                $(this).toggleClass('active');
            });
        });
    </script>
@endpush

@push('css')
    <style>
        .sidebar {
            min-width: 250px;
            background: #0080ff;
            height: 100vh;
            position: fixed;
            left: -250px;
            transition: left 0.3s ease;
            z-index: 1000;

            &.active {
                left: 0;
            }

            @media (min-width: 768px) {
                left: 0;
            }

            @media (max-width: 768px) {
                left: -250px;

                &.active {
                    left: 0;
                }
            }
        }

        .sidebar-title {
            color: white;
            padding: 1rem;
            margin: 0;
            text-align: left;
            font-weight: bold;
            font-size: 1.75rem;
        }

        .nav {
            padding: 0;
        }

        .sidebar-item {
            width: 100%;

        }

        .sidebar-item-link {
            width: 100%;
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background-color 0.2s;
            font-size: 0.9rem;
            position: relative;

            &:hover {
                background-color: rgba(255, 255, 255, 0.2);
                border-radius: 0.25rem;
                font-weight: bold;
                color: whitesmoke;
            }

            &.active {
                background-color: rgba(255, 255, 255, 0.4);
                font-weight: bold;
                color: whitesmoke;
            }

            i {
                margin-right: 10px;
                font-size: 1.2rem;
            }

            .dropdown-arrow {
                margin-left: auto;
                transition: transform 0.2s;

            }

            &.active .dropdown-arrow {
                transform: rotate(180deg);
            }
        }

        .sidebar-item-dropdown {
            list-style: none;
            padding: 0.25rem;
            margin: 0;
            width: 100%;

            &:hover {
                background-color: rgba(0, 128, 255, 0.6);
            }
        }

        .sidebar-item-link-dropdown {
            color: black;
            padding: 1rem;
            display: flex;
            align-items: center;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: background-color 0.2s;
            font-size: 0.8rem;

            &:hover {
                font-weight: bold;
                color: whitesmoke;
            }

            &.active {
                background-color: rgba(255, 255, 255, 0.4);
                font-weight: bold;
                color: whitesmoke;
            }

            i {
                margin-right: 10px;
                font-size: 1.2rem;
            }
        }

        .sidebar-collapse {
            display: none;
            background: white;
            padding-left: 0;

            &.show {
                display: block;
                transition: max-height 0.2s ease;
            }
        }
    </style>
@endpush
