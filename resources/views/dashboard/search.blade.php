@php
function highlight($text, $search) {
    if (!$search) return $text;

    $words = explode(' ', $search);

    foreach ($words as $word) {
        $word = preg_quote($word, '/');
        $text = preg_replace("/($word)/i", '<mark>$1</mark>', $text);
    }
    return $text;
}
@endphp

@forelse($documents as $doc)
<tr>
    <td>{!! highlight($doc->document_number, $search) !!}</td>
    <td>{!! highlight($doc->document_name, $search) !!}</td>
    <td>{{ $doc->type->type_name ?? '-' }}</td>
    <td>{{ $doc->currentSection->section_name ?? '-' }}</td>
    <td>{!! highlight($doc->currentHolder->full_name, $search) !!}</td>
    <td>{{ $doc->created_at ?? '-' }}</td>
    <td class="status-cell">
        <span class="status {{ strtolower($doc->status) }}">
            {{ ucfirst(strtolower($doc->status)) }}
        </span>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted">
        No documents found.
    </td>
</tr>
@endforelse
