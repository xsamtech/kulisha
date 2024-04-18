@extends('layouts.guest')

@section('guest-content')

                                <div class="card border rounded-0 text-center shadow-0">
                                    <div class="card-body py-5">
                                        <h1 class="display-1 fw-bold dktv-text-pink">405</h1>
                                        <h2 class="mb-4 dktv-text-blue">{{ __('notifications.405_title') }}</h2>
                                        <p class="lead mb-4">{{ __('notifications.405_description') }}</p>
                                        <a href="{{ route('home') }}" class="btn dktv-btn-yellow rounded-pill py-3 px-5">{{ __('miscellaneous.back_home') }}</a>
                                    </div>
                                </div>

@endsection
