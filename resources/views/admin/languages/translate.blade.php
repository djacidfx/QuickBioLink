@extends('admin.layouts.main')
@section('title', admin_lang('Translate').' ' . $language->name)

@section('header_buttons')
    <form class="d-inline" action="{{ route('admin.languages.translates.export', $language->code) }}"
          method="POST">
        @csrf
        <button class="btn btn-primary me-2"><i class="fas fa-download me-2"></i>{{ admin_lang('Export') }}
        </button>
    </form>
    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importModal"><i
            class="fas fa-upload me-2"></i>{{ admin_lang('Import') }}</button>
@endsection

@section('content')

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between position-sticky top-0">
            <h5>{{ str_replace('-', ' ', $active) }}</h5>
            <div>
                <div class="dropdown d-inline-block">
                    <button class="btn btn-outline-dark dropdown-toggle text-capitalize" type="button"
                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="far fa-list me-2"></i>{{ str_replace('-', ' ', $active) }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                        @foreach ($groups as $group)
                            <li><a class="dropdown-item  {{ $active == $group ? 'active' : '' }} text-capitalize"
                                   href="{{ route('admin.languages.translates.group', [$language->code, $group]) }}">{{ str_replace('-', ' ', $group) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <button form="quick-submitted-form"
                        class="btn btn-primary ms-2">{{ admin_lang('Save Changes') }}</button>
            </div>
        </div>
        <div class="card-body my-1">
            <form id="quick-submitted-form"
                  action="{{ route('admin.languages.translates.update', $language->id) }}" method="POST">
                @csrf
                <input type="hidden" name="group" value="{{ $active }}">
                <table class="table">
                    <tr>
                        <th>{{ admin_lang('Key') }}</th>
                        <th>{{ admin_lang('Value') }}</th>
                    </tr>
                    @if (is_array($translates) && count($translates) > 0)
                        @foreach ($translates as $key1 => $value1)
                            @if (is_array($value1))
                                @foreach ($value1 as $key2 => $value2)
                                    @if (is_array($value2)) @continue @endif
                                    <tr>
                                        <td><textarea class="form-control bg-label-secondary"
                                                      readonly>{{ $defaultLanguage[$key1][$key2] }}</textarea></td>
                                        <td><textarea name="translates[{{ $key1 }}][{{ $key2 }}]"
                                                      class="form-control"
                                                      placeholder="{{ $value2 }}">{{ $value2 }}</textarea></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td><textarea class="form-control bg-label-secondary"
                                                  readonly>{{ $defaultLanguage[$key1] }}</textarea></td>
                                    <td><textarea name="translates[{{ $key1 }}]" class="form-control"
                                                  placeholder="{{ $value1 }}">{{ $value1 }}</textarea></td>
                                </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="2">{{ admin_lang('No translations found') }}</td>
                        </tr>
                    @endif
                </table>
            </form>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">{{ admin_lang('Import Translations') }} ({{ $language->name }})
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.languages.translates.import', $language->code) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="alert alert-warning d-flex" role="alert">
                            <span class="badge badge-center rounded-pill bg-warning border-label-warning p-3 me-2"><i class="fas fa-info fs-6"></i></span>
                            <div class="d-flex flex-column ps-1">
                                <h5 class="alert-heading d-flex align-items-center mt-2">{{ admin_lang('Important!') }}</h5>
                                <p class="mb-2">
                                    {{ admin_lang('Create a backup of the existing translations before importing the new translations. Existing translations will be permanently deleted.') }}
                                </p>
                                <p class="mb-0">
                                    {{ admin_lang('Do not upload any file other than the exported translation files, make sure you are importing the correct files.') }}
                                </p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ admin_lang('Language File (ZIP)') }} *</label>
                            <input type="file" name="language_file" class="form-control">
                        </div>
                        <button class="btn btn-primary"><i class="fas fa-upload me-2"></i>{{ admin_lang('Import') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts_at_top')
        <script>
            "use strict";
            var QuickMenu = {"page": "languages"};
        </script>
    @endpush
@endsection
