<div class="container py-4">
    <h2 class="mb-4 text-center">Pilih Template Toko</h2>

    <form id="templateForm" method="POST" action="{{ route('tenant.store') }}">
        @csrf
        <input type="hidden" name="template_id" id="selectedTemplateId">

        <div class="row mb-4">
            <div class="col-md-6 offset-md-3">
                <label for="store_name">Nama Toko</label>
                <input type="text" name="store_name" class="form-control" required placeholder="Masukkan nama toko Anda">
            </div>
        </div>

        <div class="row">
            @foreach ($templates as $template)
                <div class="col-md-4 mb-4">
                    <div class="card template-card h-100" data-template-id="{{ $template->id }}">
                        <img src="{{ asset('images/templates/' . $template->slug . '.png') }}" class="card-img-top" alt="{{ $template->name }}">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $template->name }}</h5>
                            <button type="button"
                                class="btn btn-sm btn-outline-secondary mt-2 preview-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#previewModal"
                                data-template-url="{{ url('/preview/' . $template->slug) }}">
                                Lihat Preview
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Buat Toko</button>
        </div>
    </form>

    {{-- Modal untuk Preview --}}
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="templatePreviewIframe" src="" width="100%" height="600px" frameborder="0" class="w-100"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>


@push('styles')
    <style>
        .template-card {
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .template-card:hover {
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .template-card.selected {
            border: 2px solid #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('.template-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.template-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('selectedTemplateId').value = this.dataset.templateId;
            });
        });

        document.querySelectorAll('.preview-btn').forEach(button => {
            button.addEventListener('click', function() {
                const previewImage = this.getAttribute('data-template-preview');
                document.getElementById('templatePreviewImage').setAttribute('src', previewImage);
            });
        });
    </script>
@endpush
