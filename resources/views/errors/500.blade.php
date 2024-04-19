@extends('layouts.guest')

@section('guest-content')

                <div class="row g-lg-5 g-4 justify-content-center">
                    <div class="col-lg-6 col-md-8 col-sm-10 col-12 mx-auto">
                        <div class="card card-body text-center p-4 p-sm-5">
                            <h1 class="display-1 fw-bold dktv-text-pink">500</h1>
                            <h2 class="mb-4 dktv-text-blue">{{ __('notifications.500_title') }}</h2>
                            <p class="lead mb-4">{{ __('notifications.500_description') }}</p>
                            <a href="{{ route('home') }}" class="btn btn-success rounded-pill py-3 px-5">{{ __('miscellaneous.back_home') }}</a>
                        </div>
                    </div>
                </div>

@endsection
