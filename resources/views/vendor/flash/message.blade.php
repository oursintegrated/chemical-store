@foreach (session('flash_notification', collect())->toArray() as $message)
    @if ($message['overlay'])
        @include('flash::modal', [
            'modalClass' => 'flash-modal',
            'title'      => $message['title'],
            'body'       => $message['message']
        ])
    @else
        <div class="alert alert-{{ $message['level'] }} alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                <span class="pficon pficon-close"></span>
            </button>
            @if($message['level'] == 'danger')
                <span class="pficon pficon-error-circle-o"></span>
            @elseif($message['level'] == 'warning')
                <span class="pficon pficon-warning-triangle-o"></span>
            @elseif($message['level'] == 'success')
                <span class="pficon pficon-ok"></span>
            @elseif($message['level'] == 'info')
                <span class="pficon pficon-info"></span>
            @endif
            <strong>{!! $message['message'] !!}</strong>
        </div>
    @endif
@endforeach

{{ session()->forget('flash_notification') }}
