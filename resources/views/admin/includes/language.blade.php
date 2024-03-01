<div class="dropdown d-inline-block">
    <button class="btn btn-secondary dropdown-toggle" type="button"
            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-globe me-2"></i>{{ $active }}
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        @foreach ($adminLanguages as $adminLanguage)
            <li><a class="dropdown-item @if ($adminLanguage->name == $active) active @endif"
                   href="?lang={{ $adminLanguage->code }}">{{ $adminLanguage->name }}</a>
            </li>
        @endforeach
    </ul>
</div>
