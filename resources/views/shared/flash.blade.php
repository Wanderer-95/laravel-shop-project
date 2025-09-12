@if($message = flash()->get())
    <div class="{{ $message->getClass() }}">
        {{ $message->getMessage() }}
    </div>
@endif
