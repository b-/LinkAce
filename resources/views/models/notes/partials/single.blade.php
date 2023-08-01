<div class="card card-body p-3 mb-3">

    <div class="note-content small">
        {!! $note->formatted_note !!}
    </div>
    <div class="note-meta small d-flex align-items-center">
        <div class="ms-auto me-2 text-pale text-xs">
            @if($note->is_private)
                <span>
                    <x-icon.lock class="me-1" title="@lang('note.private')"/>
                    <span class="visually-hidden">@lang('note.private')</span>
                </span>
            @endif
            {!! $note->addedAt() !!}
        </div>
        <div class="text-end">

            <div class="btn-group">
                <a href="{{ route('notes.edit', [$note->id]) }}" class="btn btn-xs btn-outline-secondary"
                    aria-label="@lang('note.edit')">
                    <x-icon.edit class="me-2"/>
                    @lang('linkace.edit')
                </a>
                <a onclick="event.preventDefault();document.getElementById('note-delete-{{ $note->id }}').submit();"
                    class="btn btn-xs btn-outline-danger cursor-pointer" aria-label="@lang('note.delete')">
                    <x-icon.trash class="me-2"/>
                    @lang('linkace.delete')
                </a>
            </div>

            <form id="note-delete-{{ $note->id }}" method="POST" style="display: none;"
                action="{{ route('notes.destroy', [$note->id]) }}">
                @method('DELETE')
                @csrf
                <input type="hidden" name="note_id" value="{{ $note->id }}">
            </form>

        </div>
    </div>

</div>
