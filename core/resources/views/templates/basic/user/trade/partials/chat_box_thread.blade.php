<div>
    @foreach ($trade->chats as $chat)
        @php
            if ($chat->user_id == $trade->buyer_id) {
                $senderName = null;
                $senderImage = getImage(getFilePath('userProfile') . '/' . @$trade->buyer->image, $profileImage->size);
            } elseif ($chat->user_id == $trade->seller_id) {
                $senderName = null;
                $senderImage = getImage(getFilePath('userProfile') . '/' . @$trade->seller->image, $profileImage->size);
            } else {
                $senderName = 'System';
                $senderImage = getImage(getFilePath('logoIcon') . '/favicon.png');
            }
        @endphp
        <div class="single-message @if ($chat->user_id == auth()->id()) message--right @else message--left @endif  @if ($senderName == 'System') admin-message @endif">
            <div class="message-content-outer">
                <div class="message-content">
                    <h6 class="name">{{ $senderName }}</h6>
                    <p class="message-text">{!! __($chat->message) !!}</p>

                    @if ($chat->file)
                        <div class="messgae-attachment">
                            <b class="text-sm d-block"> @lang('Attachment') </b>
                            <a href="{{ route('user.chat.download', [$trade->id, $chat->id]) }}" class="file-demo-btn">
                                {{ __($chat->file) }}
                            </a>
                        </div>
                    @endif
                </div>
                <span class="message-time d-block text-end mt-2">{{ showDateTime($chat->created_at) }}</span>
            </div>
            <div class="message-author">
                <img src="{{ $senderImage }}" alt="image" class="thumb">
            </div>

        </div><!-- single-message end -->
    @endforeach
</div>

