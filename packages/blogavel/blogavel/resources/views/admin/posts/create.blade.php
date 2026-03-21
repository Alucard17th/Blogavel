@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Create Post')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
    <div class="header">
        <div>
            <h1>Create post</h1>
            <p class="hint">Draft, schedule, or publish. You can always edit later.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.posts.index') }}">
                <button type="button">Back</button>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.posts.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row cols-2">
            <div>
                <label>Title</label>
                <input name="title" value="{{ old('title') }}" />
                @error('title')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Slug (optional)</label>
                <input name="slug" value="{{ old('slug') }}" />
                @error('slug')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row cols-2" style="margin-top:12px">
            <div>
                <label>Category</label>
                <select name="category_id">
                    <option value="">(none)</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Status</label>
                <select name="status">
                    @foreach (['draft','scheduled','published'] as $status)
                        <option value="{{ $status }}" @selected(old('status','draft') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row cols-2" style="margin-top:12px">
            <div>
                <label>Published at</label>
                @php($publishedAtValue = (string) old('published_at'))
                @php($publishedAtValue = $publishedAtValue !== '' ? str_replace(' ', 'T', substr($publishedAtValue, 0, 16)) : '')
                <input type="datetime-local" name="published_at" value="{{ $publishedAtValue }}" />
                @error('published_at')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Featured image</label>
                <input type="file" name="featured_image" />
                @error('featured_image')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row" style="margin-top:12px">
            <div>
                <label>Tags</label>
                @php($selectedTags = (array) old('tags', []))
                <div class="actions">
                    @foreach ($tags as $tag)
                        <label style="margin:0; display:flex; align-items:center; gap:8px; color:var(--text)">
                            <input style="width:auto" type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array((string) $tag->id, array_map('strval', $selectedTags), true)) />
                            <span>{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('tags')<div class="error">{{ $message }}</div>@enderror
                @error('tags.*')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row" style="margin-top:12px">
            <div>
                <label>Content</label>
                <textarea id="content" name="content" style="display:none">{{ old('content') }}</textarea>
                <div id="content-editor" style="min-height:260px">{!! old('content') !!}</div>
                @error('content')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="actions" style="margin-top:14px">
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        (function () {
            var editorEl = document.getElementById('content-editor');
            var inputEl = document.getElementById('content');
            if (!editorEl || !inputEl) return;

            function getCsrfToken() {
                var meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : '';
            }

            function uploadImage(file) {
                var formData = new FormData();
                formData.append('file', file);

                return fetch("{{ route('blogavel.admin.media.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                }).then(function (res) {
                    return res.json().then(function (json) {
                        if (!res.ok) {
                            throw json;
                        }
                        return json;
                    });
                });
            }

            function imageHandler() {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = function () {
                    var file = input.files ? input.files[0] : null;
                    if (!file) return;

                    uploadImage(file)
                        .then(function (json) {
                            var range = quill.getSelection(true);
                            var index = range ? range.index : quill.getLength();
                            quill.insertEmbed(index, 'image', json.url);
                            quill.setSelection(index + 1);
                        })
                        .catch(function () {
                            alert('Image upload failed.');
                        });
                };
            }

            var quill = new Quill(editorEl, {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: [
                            [{ font: [] }, { size: [] }],
                            [{ header: [1, 2, 3, 4, 5, 6, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ color: [] }, { background: [] }],
                            [{ script: 'sub' }, { script: 'super' }],
                            [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
                            [{ direction: 'rtl' }, { align: [] }],
                            ['blockquote', 'code-block'],
                            ['link', 'image', 'video'],
                            ['clean']
                        ],
                        handlers: {
                            image: imageHandler
                        }
                    }
                }
            });

            var form = editorEl.closest('form');
            if (!form) return;

            form.addEventListener('submit', function () {
                inputEl.value = quill.root.innerHTML;
            });
        })();
    </script>
@endpush
