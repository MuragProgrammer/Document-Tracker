@forelse($documents as $doc)
<tr>
    <td>{{ $doc->document_number }}</td>
    <td>{{ $doc->document_name }}</td>
    <td>{{ $doc->type->type_name ?? '-' }}</td>
    <td>{{ $doc->currentSection->section_name ?? '-' }}</td>
    <td>{{ $doc->currentHolder->full_name ?? '-' }}</td>
    <td>{{ $doc->created_at ?? '-' }}</td>

    <td class="status-cell">
        <span class="status {{ strtolower($doc->status) }}">
            {{ ucfirst(strtolower($doc->status)) }}
        </span>
    </td>

    <td>
        <a href="{{ route('documents.show', $doc) }}" class="btn btn-view">
            View
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted">
        No documents found.
    </td>
</tr>
@endforelse
