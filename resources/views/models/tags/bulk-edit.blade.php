@extends('layouts.app')

@section('content')

    <form action="{{ route('bulk-edit.update-tags') }}" method="POST" class="card bulk-form tag-form">
        @csrf
        <input type="hidden" name="models" value="{{ old('models', implode(',', $models)) }}">
        <header class="card-header">@choice('tag.bulk_title', $modelCount, ['count' => $modelCount])</header>
        <div class="card-body">
            <div class="row">
                <x-forms.visibility-toggle class="col-6" :unchanged-option="true"/>
            </div>

            <div class="mt-3 d-sm-flex align-items-center justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <x-icon.save class="me-2"/> @lang('tag.update_tags')
                </button>
            </div>
        </div>
    </form>

    <form action="{{ route('bulk-edit.delete') }}" method="POST" class="card mt-4">
        @csrf
        <input type="hidden" name="type" value="tags">
        <input type="hidden" name="models" value="{{ implode(',', $models) }}">
        <header class="card-header">@choice('tag.delete', $modelCount)</header>
        <div class="card-body">
            <div class="text-end">
                <button type="submit" class="btn btn-danger">
                    <x-icon.save class="me-2"/> @choice('tag.delete', $modelCount)
                </button>
            </div>
        </div>
    </form>

@endsection
