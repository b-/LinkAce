@extends('layouts.app')

@section('content')

    <div class="user-profile">
        <div class="card">
            <div class="card-header">
                @lang('user.user')
            </div>
            <div class="card-body">
                <h2 class="title mb-0">{{ $user->name }}</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-7">

                <div class="card mt-4">
                    <div class="card-header">
                        @lang('link.recent_links')
                    </div>

                    <ul class="user-links list-group list-group-flush">
                        @forelse($links as $link)
                            <a href="{{ $link->url }}" class="list-group-item list-group-item-action one-line">
                                {!! $link->getIcon('me-1') !!}
                                {{ $link->title }}
                            </a>
                        @empty
                            <li class="list-group-item text-muted">
                                @lang('linkace.no_results_found', ['model' => trans('link.links')])
                            </li>
                        @endforelse
                    </ul>
                </div>

            </div>
            <div class="meta col-12 col-md-5">

                <div class="card mt-4">
                    <div class="card-header">
                        @lang('stats.stats')
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="me-1">@lang('stats.total_links')</span>
                            <span class="badge bg-secondary">{{ $stats['total_links'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="me-1">@lang('stats.total_lists')</span>
                            <span class="badge bg-secondary">{{ $stats['total_lists'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="me-1">@lang('stats.total_tags')</span>
                            <span class="badge bg-secondary">{{ $stats['total_tags'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="me-1">@lang('stats.total_notes')</span>
                            <span class="badge bg-secondary">{{ $stats['total_notes'] }}</span>
                        </li>
                    </ul>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        @lang('list.recent_lists')
                    </div>

                    <div class="card-body">
                        @forelse($lists as $list)
                            <a href="{{ route('guest.lists.show', ['list' => $list]) }}"
                                class="btn btn-light btn-sm m-1">
                                {{ $list->name }}
                            </a>
                        @empty
                            <div class="text-muted">
                                @lang('linkace.no_results_found', ['model' => trans('list.lists')])
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        @lang('tag.recent_tags')
                    </div>

                    <div class="card-body">
                        @forelse($tags as $tag)
                            <a href="{{ route('guest.tags.show', ['tag' => $tag]) }}" class="btn btn-light btn-sm m-1">
                                {{ $tag->name }}
                            </a>
                        @empty
                            <div class="text-muted">
                                @lang('linkace.no_results_found', ['model' => trans('tag.tags')])
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
