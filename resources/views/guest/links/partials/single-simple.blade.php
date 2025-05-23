@php
    $shareLinks = getShareLinks($link);
@endphp
<li class="single-link link-simple list-group-item">
    <div class="d-sm-flex align-items-center">
        <div class="me-4 one-line-sm">
            <a href="{{ $link->url }}" title="{{ $link->url }}" {!! linkTarget() !!} class="link-url">
                {{ $link->title }}
            </a>
        </div>
        <div class="mt-2 mt-sm-0 ms-auto flex-shrink-0">
            <small class="text-pale me-2 text-condensed">{!! $link->domainOfURL() !!}</small>
            <button type="button" class="btn btn-xs btn-outline-secondary" title="@lang('sharing.share_link')"
                data-bs-toggle="collapse" data-bs-target="#sharing-{{ $link->id }}"
                aria-expanded="false" aria-controls="sharing-{{ $link->id }}">
                <x-icon.share class="fw"/>
                <span class="visually-hidden">@lang('sharing.share_link')</span>
            </button>
        </div>
    </div>
    @if($shareLinks !== '')
        <div class="collapse" id="sharing-{{ $link->id }}">
            <div class="share-links justify-content-end mt-1">
                {!! $shareLinks !!}
            </div>
        </div>
    @endif
</li>
