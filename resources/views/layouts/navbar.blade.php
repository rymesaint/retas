<div class="menu">
    <ul class="list">
        <li class="header">MAIN NAVIGATION</li>
        @forelse(getMenu(Auth::user()->role) as $menu)
            @if($menu->hasChild == true)
                <li>
                    <a href="#" class="menu-toggle">
                        <i class="material-icons">{{ $menu->icon }}</i>
                        <span>{{ __($menu->titleName) }}</span>
                    </a>
                    <ul class="ml-menu">
                        @foreach($menu->childs as $child)
                            <li class="{{ $child->isActiveRoute }}">
                                <a href="{{ $child->route }}">
                                    <span>{{ __($child->titleName) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @else
                <li class="{{ $menu->isActiveRoute }}">
                    <a href="{{ $menu->route }}">
                    <i class="material-icons">{{ $menu->icon }}</i>
                        <span>{{ __($menu->titleName) }}</span>
                    </a>
                </li>
            @endif
        @empty
            <li>
                <a href="javascript:void(0);">
                    <i class="material-icons">warning</i>
                    <span>{{ __('You have no menu access here.') }}</span>
                </a>
            </li>
        @endforelse
    </ul>
</div>
