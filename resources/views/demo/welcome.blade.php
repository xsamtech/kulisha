@extends('layouts.app')

@section('app-content')

                <!-- Sidenav START -->
                <div class="col-lg-3 mt-0">
                    <!-- Advanced filter responsive toggler START -->
                    <div class="d-flex align-items-center d-lg-none">
                        <button class="border-0 bg-transparent" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSideNavbar" aria-controls="offcanvasSideNavbar">
                            <span class="btn btn-primary"><i class="fa-solid fa-sliders-h"></i></span>
                            <span class="h6 mb-0 fw-bold d-lg-none ms-2">@lang('miscellaneous.menu.public.profile.title')</span>
                        </button>
                    </div>

                    <!-- Advanced filter responsive toggler END -->
                    <!-- Navbar START-->
                    <nav class="navbar navbar-expand-lg mx-0">
                        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSideNavbar">
                            <!-- Offcanvas header -->
                            <div class="offcanvas-header">
                                <button type="button" class="btn-close text-reset ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>

                            <!-- Offcanvas body -->
                            <div class="offcanvas-body d-block px-2 px-lg-0">
                                <!-- Card START -->
                                <div class="card overflow-hidden">
                                    <!-- Cover image -->
                                    <div class="h-50px" style="background-image:url({{ asset('assets/img/template/bg/01.jpg') }}); background-position: center; background-size: cover; background-repeat: no-repeat;"></div>
                                    <!-- Card body START -->
                                    <div class="card-body pt-0">
                                        <div class="text-center">
                                            <!-- Avatar -->
                                            <div class="avatar avatar-lg mt-n5 mb-3">
                                                <a href="#!">
                                                    <img class="avatar-img rounded border border-white border-3" src="{{ asset('assets/img/template/avatar/07.jpg') }}" alt></a>
												</div>

                                                <!-- Info -->
												<h5 class="mb-0"> <a href="#!">Robert Downey Jr.</a></h5>
												<small>Entrepreneur</small>
												<p class="mt-3">
                                                    I'd love to change the world, but they won’t give me the source code.
                                                </p>

												<!-- User stat START -->
												<div class="hstack gap-2 gap-xl-3 justify-content-center">
													<!-- User stat item -->
													<div>
														<h6 class="mb-0">256</h6>
														<small>Post</small>
													</div>
													<!-- Divider -->
													<div class="vr"></div>
													<!-- User stat item -->
													<div>
														<h6 class="mb-0">2.5K</h6>
														<small>Followers</small>
													</div>
													<!-- Divider -->
													<div class="vr"></div>
													<!-- User stat item -->
													<div>
														<h6 class="mb-0">365</h6>
														<small>Following</small>
													</div>
												</div>
												<!-- User stat END -->
											</div>

											<!-- Divider -->
											<hr>

											<!-- Side Nav START -->
											<ul class="nav nav-link-secondary flex-column fw-bold gap-2">
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('profile.entity', ['username' => 'tonystark', 'entity' => 'connections']) }}">
                                                        <i class="fa-solid fa-users me-2 fs-5 align-middle text-danger"></i>
                                                        <span>@lang('miscellaneous.menu.public.profile.connections')</span>
                                                    </a>
												</li>

                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('profile.entity', ['username' => 'tonystark', 'entity' => 'products']) }}">
                                                        <i class="fa-solid fa-basket-shopping me-2 fs-5 align-middle text-primary"></i>
                                                        <span>@lang('miscellaneous.menu.public.profile.products')</span>
                                                    </a>
												</li>

                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('profile.entity', ['username' => 'tonystark', 'entity' => 'services']) }}">
                                                        <i class="fa-solid fa-people-carry-box me-2 fs-5 align-middle text-warning-emphasis"></i>
                                                        <span>@lang('miscellaneous.menu.public.profile.services')</span>
                                                    </a>
												</li>

                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{ route('profile.entity', ['username' => 'tonystark', 'entity' => 'my_activities']) }}">
                                                        <i class="fa-regular fa-clock-four me-2 fs-5 align-middle text-success-emphasis"></i>
                                                        <span>@lang('miscellaneous.menu.public.profile.my_activities')</span>
                                                    </a>
												</li>
											</ul>
											<!-- Side Nav END -->
										</div>
										<!-- Card body END -->
										<!-- Card footer -->
										<div class="card-footer text-center py-2">
											<a class="btn btn-link btn-sm" href="my-profile.html">View Profile</a>
										</div>
									</div>
									<!-- Card END -->

									<!-- Helper link START -->
									<ul class="nav small mt-4 justify-content-center lh-1">
										<li class="nav-item">
											<a class="nav-link" target="_blank" href="https://xsamtech.com/products/kulisha">@lang('miscellaneous.menu.about')</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="{{ route('settings.home') }}">@lang('miscellaneous.menu.public.settings.title')</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" target="_blank" href="https://xsamtech.com/messenger">@lang('miscellaneous.public.help.title')</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="https://xsamtech.com/products/kulisha/privacy_policy">@lang('miscellaneous.menu.privacy_policy')</a>
										</li>
									</ul>
									<!-- Helper link END -->
									<!-- Copyright -->
									<p class="small text-center mt-1">&copy; {{ date('Y') }} <a class="text-reset" target="_blank" href="https://xsamtech.com/">Xsam Technologies</a></p>
								</div>
							</div>
						</div>
					</nav>
					<!-- Navbar END-->
				</div>
				<!-- Sidenav END -->

@endsection