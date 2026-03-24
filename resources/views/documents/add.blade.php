@extends('layouts.app')

@section('title', 'Add Document')

@section('content')
<div class="add-document-container">

    <h1 class="page-title">Add Document</h1>

    <div class="document-card">
        <h2>Document Details</h2>

        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Document Number (Preview Only) -->
            <label for="document_number">Document Number</label>
            <input
                type="text"
                id="document_number"
                readonly
            >

            <!-- Document Type -->
            <label for="type_id">Document Type</label>
            <select name="type_id" id="type_id" required>
                @foreach($types as $index => $type)
                    <option
                        value="{{ $type->type_id }}"
                        data-type-code="{{ strtoupper($type->type_code) }}"
                        {{ $index === 0 ? 'selected' : '' }}
                    >
                        {{ $type->type_name }}
                    </option>
                @endforeach
            </select>

            <!-- Document Name -->
            <label for="document_name">Document Name</label>
            <input
                type="text"
                name="document_name"
                id="document_name"
                required
                placeholder="Enter document name"
            >

            <!-- Notes -->
            <label for="notes">
                Additional Notes <span class="optional">(Optional)</span>
            </label>
            <textarea
                name="notes"
                id="notes"
                rows="4"
                placeholder="Add notes..."
            ></textarea>

            <label>Upload Picture/s</label>

            <div id="uploadContainer" class="upload-grid">
                <!-- Upload cards will be injected here -->
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    Create Document
                </button>
                <a href="{{ route('documents.index') }}" class="btn-cancel">
                    Cancel
                </a>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.getElementById('type_id');
    const docNumberInput = document.getElementById('document_number');

    // Injected from controller
    const departmentCode = "{{ $department_code }}";
    const sectionCode = "{{ $section_code }}";
    const year = "{{ $year }}";
    const nextDocId = "{{ $nextDocIdPadded }}";

    function updateDocumentNumber() {
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        const typeCode = selectedOption.dataset.typeCode || 'TYPE';

        docNumberInput.value =
            `${departmentCode}-${sectionCode}-${typeCode}-${year}-${nextDocId}`;
    }

    // Init
    updateDocumentNumber();

    // Update on type change
    typeSelect.addEventListener('change', updateDocumentNumber);

    const container = document.getElementById('uploadContainer');

    function createUploadCard() {
        const card = document.createElement('label');
        card.classList.add('upload-card');

        const input = document.createElement('input');
        input.type = 'file';
        input.name = 'attachments[]';
        input.accept = 'image/*';

        const plus = document.createElement('span');
        plus.textContent = '+';

        card.appendChild(input);
        card.appendChild(plus);

        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (e) {
                card.innerHTML = `<img src="${e.target.result}" />`;
                card.appendChild(input);
            };

            reader.readAsDataURL(file);

            // Add new empty card if this is the last one
            if (container.lastElementChild === card) {
                container.appendChild(createUploadCard());
            }
        });

        return card;
    }

    // Init first card
    container.appendChild(createUploadCard());
    
});
</script>
@endpush
