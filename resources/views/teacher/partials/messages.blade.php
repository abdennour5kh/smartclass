<style>
    .list-group-item:hover {
        background-color: #f8f9fa !important;
    }
</style>

@if($conversations->count())
    <div class="list-group rounded border shadow-sm">
        @foreach($conversations as $conversation)
            @php
                $isUnread = $conversation->pivot->last_read_at === null ||
                            $conversation->updated_at->gt($conversation->pivot->last_read_at);

                $lastMessage = $conversation->messages->last();
                $files = $lastMessage->studentFiles->merge($lastMessage->teacherFiles);
                $hasAttachments = $files->isNotEmpty();
                $preview = Str::limit(strip_tags($lastMessage->body), 100);
            @endphp

            <a href="{{ route('teacher_show_conversation', $conversation->id) }}"
               class="list-group-item list-group-item-action py-3 {{ $isUnread ? 'bg-white fw-bold' : 'bg-white' }}"
               style="transition: background-color 0.2s ease;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="mb-0 text-dark">
                        {{ $conversation->subject }}
                        @if($hasAttachments)
                            <span class="ms-1 text-muted">📎</span>
                        @endif
                    </h6>
                    <small class="text-muted">
                        {{ $conversation->updated_at->diffForHumans() }}
                    </small>
                </div>

                <div class="d-flex justify-content-between">
                    <p class="mb-0 text-muted">
                        👤 From: <strong>{{ $conversation->messages->first()->sender->full_name }}</strong>
                    </p>
                    @if($isUnread)
                        <span class="badge bg-warning align-self-center">New</span>
                    @endif
                </div>

                <p class="mb-0 text-muted mt-1" style="font-size: 0.93rem;">
                    💬 {{ $preview ?: 'No message content.' }}
                </p>
            </a>
        @endforeach
    </div>
@else
    <div class="text-center text-muted py-5">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        <p class="mb-0">No messages yet. Your inbox is empty.</p>
    </div>
@endif
