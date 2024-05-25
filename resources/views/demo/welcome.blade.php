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
                                                <a href="{{ route('profile.home', ['username' => 'tonystark']) }}">
                                                    <img class="avatar-img rounded border border-white border-3" src="{{ asset('assets/img/template/avatar/07.jpg') }}" alt>
												</a>
											</div>

                                            <!-- Info -->
											<h5 class="mb-0"> <a href="{{ route('profile.home', ['username' => 'tonystark']) }}">Robert Downey Jr.</a></h5>
											<small>@tonystark</small>
											<p class="mt-3">
                                                I'd love to change the world, but they won’t give me the source code.
                                            </p>

											<!-- User stat START -->
											<div class="hstack gap-2 gap-xl-3 justify-content-center">
												<!-- User stat item -->
												<div>
													<h6 class="mb-0 small">{{ thousandsCurrencyFormat(256) }}</h6>
													<small class="kls-fs-7">{{ Str::limit(__('miscellaneous.public.profile.statistics.posts'), (str_starts_with(app()->getLocale(), 'fr') ? 7 : 8), '...') }}</small>
												</div>
												<!-- Divider -->
												<div class="vr" style="z-index: 9999;"></div>
												<!-- User stat item -->
												<div>
													<h6 class="mb-0 small">{{ thousandsCurrencyFormat(25829384) }}</h6>
													<small class="kls-fs-7">{{ Str::limit(__('miscellaneous.public.profile.statistics.followers'), (str_starts_with(app()->getLocale(), 'fr') ? 7 : 8), '...') }}</small>
												</div>
												<!-- Divider -->
												<div class="vr" style="z-index: 9999;"></div>
												<!-- User stat item -->
												<div>
													<h6 class="mb-0 small">{{ thousandsCurrencyFormat(36500) }}</h6>
													<small class="kls-fs-7">{{ Str::limit(__('miscellaneous.public.profile.statistics.following'), (str_starts_with(app()->getLocale(), 'fr') ? 7 : 8), '...') }}</small>
												</div>
											</div>

											<!-- Divider -->
											<hr>
										</div>

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
													<i class="fa-solid fa-seedling me-3 fs-5 align-middle text-success-emphasis"></i>
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
													<i class="fa-regular fa-clock-four me-3 fs-5 align-middle text-primary"></i>
													<span>@lang('miscellaneous.menu.public.profile.my_activities')</span>
												</a>
											</li>
										</ul>
										<!-- Side Nav END -->
									</div>
									<!-- Card body END -->

									<!-- Card footer -->
									<div class="card-footer text-center py-2">
										<a class="btn btn-link btn-sm" href="{{ route('profile.home', ['username' => 'tonystark']) }}">@lang('miscellaneous.public.home.view_profile')</a>
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
										<a class="nav-link" target="_blank" href="https://xsamtech.com/messenger">@lang('miscellaneous.public.home.help')</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="https://xsamtech.com/products/kulisha/privacy_policy">@lang('miscellaneous.menu.privacy_policy')</a>
									</li>
									<li class="nav-item dropdown">
										<a role="button" id="dropdownLanguage" class="nav-link" data-bs-toggle="dropdown" aria-expanded="false">
											@lang('miscellaneous.your_language') <i class="fa-solid fa-angle-down"></i>
										</a>

										<ul class="dropdown-menu mt-1 p-0" aria-labelledby="dropdownLanguage">
    @foreach ($available_locales as $locale_name => $available_locale)
                                            <li class="w-100">
		@if ($available_locale != $current_locale)
												<a class="dropdown-item px-3 py-2" href="{{ route('change_language', ['locale' => $available_locale]) }}">
													{{ $locale_name }}
												</a>
		@else
												<span class="dropdown-item px-3 py-2 kls-lime-green-text">
													{{ $locale_name }}
												</span>
		@endif
                                            </li>
    @endforeach
                                        </ul>
                                    </li>
								</ul>
								<!-- Helper link END -->
								<!-- Copyright -->
								<p class="small text-center mt-1">&copy; {{ date('Y') }} <a class="text-reset" target="_blank" href="https://xsamtech.com/">Xsam Technologies</a></p>
							</div>
						</div>
					</nav>
					<!-- Navbar END-->
				</div>
				<!-- Sidenav END -->

				<!-- Main content START -->
				<div class="col-lg-6 col-md-8 vstack gap-4 mt-0">
					<!-- Story START -->
					<div class="d-flex gap-3 mb-n3">
						<div class="position-relative text-center">
							<!-- Card START -->
							<div class="mb-1">
								<a class="stretched-link btn btn-dark rounded-circle icon-xxl rounded-circle" href="#!"><i class="fa-solid fa-plus fs-6"></i></a>
							</div>

							<a href="#!" class="small fw-normal text-secondary">@lang('miscellaneous.public.home.stories.new')</a>
							<!-- Card END -->
						</div>
						<!-- Stories -->
						<div id="stories" class="storiesWrapper stories user-icon carousel scroll-enable"></div>
					</div>
					<!-- Story END -->

					<!-- Share feed START -->
					<div class="card card-body">
						<div class="d-flex mb-3">
							<!-- Avatar -->
							<div class="avatar avatar-xs me-2">
								<a href="#">
									<img class="avatar-img rounded-circle" src="{{ asset('assets/img/template/avatar/07.jpg') }}" alt>
								</a>
							</div>

							<!-- Post input -->
							<form class="w-100">
								<input class="form-control pe-4 border-0" placeholder="@lang('miscellaneous.public.home.posts.new')" data-bs-toggle="modal" data-bs-target="#modalCreateFeed">
							</form>
						</div>

						<!-- Share feed toolbar START -->
						<ul class="nav nav-pills nav-stack small fw-normal">
							<li class="nav-item">
								<a class="nav-link bg-light py-1 px-2 mb-0" href="#!" data-bs-toggle="modal" data-bs-target="#feedActionPhoto">
									<i class="bi bi-camera pe-2 fs-6 text-success"></i><span class="kls-text-secondary">@lang('miscellaneous.public.home.posts.type.image')</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link bg-light py-1 px-2 mb-0" href="#!" data-bs-toggle="modal" data-bs-target="#modalCreateEvents">
									<i class="bi bi-calendar2-event-fill pe-2 fs-6 text-danger"></i><span class="kls-text-secondary">@lang('miscellaneous.public.home.posts.type.event')</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link bg-light py-1 px-2 mb-0" href="#!" data-bs-toggle="modal" data-bs-target="#modalCreatePoll">
									<i class="bi bi-list-check pe-2 fs-6 text-warning"></i><span class="kls-text-secondary">@lang('miscellaneous.public.home.posts.type.poll')</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link bg-light py-1 px-2 mb-0" href="#!" data-bs-toggle="modal" data-bs-target="#feedActionVideo">
									<i class="bi bi-question-circle pe-2 fs-6 text-info"></i><span class="kls-text-secondary">@lang('miscellaneous.public.home.posts.type.anonymous_question')</span>
								</a>
							</li>
						</ul>
						<!-- Share feed toolbar END -->
					</div>
					<!-- Share feed END -->

					<!-- Card feed item START -->
					<div class="card">
						<!-- Card header START -->
						<div class="card-header border-0 pb-0">
							<div class="d-flex align-items-center justify-content-between">
								<div class="d-flex align-items-center">
									<!-- Avatar -->
									<div class="avatar avatar-story me-2">
										<a href="#!">
											<img class="avatar-img rounded-circle" src="{{ asset('assets/img/template/avatar/04.jpg') }}" alt>
										</a>
									</div>

									<!-- Info -->
									<div>
										<div class="nav nav-divider">
											<h6 class="nav-item card-title mb-0">
												<a href="#!">Lori Ferguson</a>
											</h6>
											<span class="nav-item small">2hr</span>
										</div>

										<p class="mb-0 small">@loriferguson</p>
									</div>
								</div>

								<!-- Card feed action dropdown START -->
								<div class="dropdown">
									<a href="#" class="text-secondary btn btn-secondary-soft-hover py-1 px-2" id="cardFeedAction" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="bi bi-chevron-down"></i>
									</a>

									<!-- Card feed action dropdown menu -->
									<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="cardFeedAction">
										<li>
											<a class="dropdown-item" href="#"><i class="fa-regular fa-bookmark fa-fw me-2"></i>@lang('miscellaneous.public.home.posts.actions.save')</a>
										</li>
										<li>
											<a class="dropdown-item" href="#"><i class="fa-solid fa-user-large-slash fa-fw me-2"></i>@lang('miscellaneous.public.home.posts.actions.unfollow_owner', ['owner' => 'loriferguson'])</a>
										</li>
										<li>
											<a class="dropdown-item" href="#"><i class="fa-regular fa-eye-slash fa-fw me-2"></i>@lang('miscellaneous.public.home.posts.actions.hide')</a>
										</li>
										<li><hr class="dropdown-divider"></li>
										<li>
											<a class="dropdown-item" href="#"><i class="fa-regular fa-flag fa-fw me-2"></i>@lang('miscellaneous.public.home.posts.actions.report')</a>
										</li>
									</ul>
								</div>
								<!-- Card feed action dropdown END -->
							</div>
						</div>
						<!-- Card header END -->

						<!-- Card body START -->
						<div class="card-body">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

							<!-- Card img -->
							<img class="card-img" src="{{ asset('assets/img/template/post/3by2/01.jpg') }}" alt="Post">

							<!-- Feed react START -->
							<ul class="nav nav-pills nav-pills-light nav-fill nav-stack small border-top border-bottom py-1 my-3">
								<li class="nav-item">
									<a class="nav-link mb-0 active" href="#!" data-bs-container="body" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" data-bs-custom-class="tooltip-text-start" data-bs-title="Frances Guerrero<br> Lori Stevens<br> Billy Vasquez<br> Judy Nguyen<br> Larry Lawson<br> Amanda Reed<br> Louis Crawford">
										<i class="bi bi-heart pe-1"></i>Liked (56)
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link mb-0" href="#!">
										<i class="bi bi-chat-fill pe-1"></i>Comments (12)
									</a>
								</li>
								<!-- Card share action menu START -->
								<li class="nav-item dropdown">
									<a href="#" class="nav-link mb-0" id="cardShareAction" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="bi bi-reply-fill flip-horizontal ps-1"></i>Share (3)
									</a>
									<!-- Card share action dropdown menu -->
									<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="cardShareAction">
										<li>
											<a class="dropdown-item" href="#"><i class="bi bi-envelope fa-fw pe-2"></i>Send via Direct Message</a>
										</li>
										<li>
											<a class="dropdown-item" href="#">
												<i class="bi bi-bookmark-check fa-fw pe-2"></i>Bookmark
											</a>
										</li>
										<li>
											<a class="dropdown-item" href="#">
												<i class="bi bi-link fa-fw pe-2"></i>Copy link to post
											</a>
										</li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-share fa-fw pe-2"></i>Share post via
									…</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-pencil-square fa-fw pe-2"></i>Share to
									News Feed</a></li>
							  </ul>
							</li>
							<!-- Card share action menu END -->
							<li class="nav-item">
							  <a class="nav-link mb-0" href="#!"> <i
								  class="bi bi-send-fill pe-1"></i>Send</a>
							</li>
						  </ul>
						  <!-- Feed react START -->
		  
						  <!-- Add comment -->
						  <div class="d-flex mb-3">
							<!-- Avatar -->
							<div class="avatar avatar-xs me-2">
							  <a href="#!"> <img class="avatar-img rounded-circle"
								  src="assets/img/template/avatar/12.jpg" alt> </a>
							</div>
							<!-- Comment box  -->
							<form class="position-relative w-100">
							  <textarea class="form-control pe-4 bg-light" data-autoresize
								rows="1" placeholder="Add a comment..."></textarea>
							  <!-- Emoji button -->
							  <div class="position-absolute top-0 end-0">
								<button class="btn" type="button">🙂</button>
							  </div>
		  
							  <button class="btn btn-sm btn-primary mb-0 rounded mt-2"
								type="submit">Post</button>
							</form>
						  </div>
						  <!-- Comment wrap START -->
						  <ul class="comment-wrap list-unstyled">
							<!-- Comment item START -->
							<li class="comment-item">
							  <div class="d-flex position-relative">
								<div class="comment-line-inner"></div>
								<!-- Avatar -->
								<div class="avatar avatar-xs">
								  <a href="#!"><img class="avatar-img rounded-circle"
									  src="assets/img/template/avatar/05.jpg" alt></a>
								</div>
								<div class="ms-2">
								  <!-- Comment by -->
								  <div class="bg-light rounded-start-top-0 p-3 rounded">
									<div class="d-flex justify-content-between">
									  <h6 class="mb-1"> <a href="#!"> Frances Guerrero
										</a></h6>
									  <small class="ms-2">5hr</small>
									</div>
									<p class="small mb-0">Removed demands expense account
									  in outward tedious do. Particular way thoroughly
									  unaffected projection.</p>
								  </div>
								  <!-- Comment react -->
								  <ul class="nav nav-divider py-2 small">
									<li class="nav-item">
									  <a class="nav-link" href="#!"> Like (3)</a>
									</li>
									<li class="nav-item">
									  <a class="nav-link" href="#!"> Reply</a>
									</li>
									<li class="nav-item">
									  <a class="nav-link" href="#!"> View 5 replies</a>
									</li>
								  </ul>
								</div>
							  </div>
							  <!-- Comment item nested START -->
							  <ul class="comment-item-nested list-unstyled">
								<!-- Comment item START -->
								<li class="comment-item">
								  <div class="comment-line-inner"></div>
								  <div class="d-flex">
									<!-- Avatar -->
									<div class="avatar avatar-xs">
									  <a href="#!"><img class="avatar-img rounded-circle"
										  src="assets/img/template/avatar/06.jpg" alt></a>
									</div>
									<!-- Comment by -->
									<div class="ms-2">
									  <div class="bg-light p-3 rounded">
										<div class="d-flex justify-content-between">
										  <h6 class="mb-1"> <a href="#!"> Lori Stevens
											</a> </h6>
										  <small>2hr</small>
										</div>
										<p class="small mb-0">See resolved goodness
										  felicity shy civility domestic had but Drawings
										  offended yet answered Jennings perceive.</p>
									  </div>
									  <!-- Comment react -->
									  <ul class="nav nav-divider py-2 small">
										<li class="nav-item">
										  <a class="nav-link" href="#!"> Like (5)</a>
										</li>
										<li class="nav-item">
										  <a class="nav-link" href="#!"> Reply</a>
										</li>
									  </ul>
									</div>
								  </div>
								</li>
								<!-- Comment item END -->
								<!-- Comment item START -->
								<li class="comment-item">
								  <div class="comment-line-inner"></div>
								  <div class="d-flex">
									<!-- Avatar -->
									<div class="avatar avatar-story avatar-xs">
									  <a href="#!"><img class="avatar-img rounded-circle"
										  src="assets/img/template/avatar/07.jpg" alt></a>
									</div>
									<!-- Comment by -->
									<div class="ms-2">
									  <div class="bg-light p-3 rounded">
										<div class="d-flex justify-content-between">
										  <h6 class="mb-1"> <a href="#!"> Billy Vasquez
											</a> </h6>
										  <small>15min</small>
										</div>
										<p class="small mb-0">Wishing calling is warrant
										  settled was lucky.</p>
									  </div>
									  <!-- Comment react -->
									  <ul class="nav nav-divider py-2 small">
										<li class="nav-item">
										  <a class="nav-link" href="#!"> Like</a>
										</li>
										<li class="nav-item">
										  <a class="nav-link" href="#!"> Reply</a>
										</li>
									  </ul>
									</div>
								  </div>
								</li>
								<!-- Comment item END -->
							  </ul>
							  <!-- Load more replies -->
							  <a href="#!" role="button"
								class="btn btn-link btn-link-loader btn-sm text-secondary d-flex align-items-center mb-3 ms-5"
								data-bs-toggle="button" aria-pressed="true">
								<div class="spinner-dots me-2">
								  <span class="spinner-dot"></span>
								  <span class="spinner-dot"></span>
								  <span class="spinner-dot"></span>
								</div>
								Load more replies
							  </a>
							  <!-- Comment item nested END -->
							</li>
							<!-- Comment item END -->
							<!-- Comment item START -->
							<li class="comment-item">
							  <div class="d-flex">
								<!-- Avatar -->
								<div class="avatar avatar-xs">
								  <a href="#!"><img class="avatar-img rounded-circle"
									  src="assets/img/template/avatar/05.jpg" alt></a>
								</div>
								<!-- Comment by -->
								<div class="ms-2">
								  <div class="bg-light p-3 rounded">
									<div class="d-flex justify-content-between">
									  <h6 class="mb-1"> <a href="#!"> Frances Guerrero
										</a> </h6>
									  <small class="ms-2">4min</small>
									</div>
									<p class="small mb-0">Removed demands expense account
									  in outward tedious do. Particular way thoroughly
									  unaffected projection.</p>
								  </div>
								  <!-- Comment react -->
								  <ul class="nav nav-divider pt-2 small">
									<li class="nav-item">
									  <a class="nav-link" href="#!"> Like (1)</a>
									</li>
									<li class="nav-item">
									  <a class="nav-link" href="#!"> Reply</a>
									</li>
									<li class="nav-item">
									  <a class="nav-link" href="#!"> View 6 replies</a>
									</li>
								  </ul>
								</div>
							  </div>
							</li>
							<!-- Comment item END -->
						  </ul>
						  <!-- Comment wrap END -->
						</div>
						<!-- Card body END -->
						<!-- Card footer START -->
						<div class="card-footer border-0 pt-0">
						  <!-- Load more comments -->
						  <a href="#!" role="button"
							class="btn btn-link btn-link-loader btn-sm text-secondary d-flex align-items-center"
							data-bs-toggle="button" aria-pressed="true">
							<div class="spinner-dots me-2">
							  <span class="spinner-dot"></span>
							  <span class="spinner-dot"></span>
							  <span class="spinner-dot"></span>
							</div>
							Load more comments
						  </a>
						</div>
						<!-- Card footer END -->
					  </div>
					  <!-- Card feed item END -->
		  
					  <!-- Card feed item START -->
					  <div class="card">
						<!-- Card header START -->
						<div class="card-header">
						  <div class="d-flex align-items-center justify-content-between">
							<div class="d-flex align-items-center">
							  <!-- Avatar -->
							  <div class="avatar me-2">
								<a href="#!"> <img class="avatar-img rounded-circle"
									src="assets/img/template/logo/12.svg" alt> </a>
							  </div>
							  <!-- Info -->
							  <div>
								<h6 class="card-title mb-0"><a href="#!"> Bootstrap:
									Front-end framework </a></h6>
								<a href="#!" class="mb-0 text-body">Sponsored <i
									class="bi bi-info-circle ps-1"
									data-bs-container="body" data-bs-toggle="popover"
									data-bs-placement="top"
									data-bs-content="You're seeing this ad because your activity meets the intended audience of our site."></i>
								</a>
							  </div>
							</div>
							<!-- Card share action START -->
							<div class="dropdown">
							  <a href="#"
								class="text-secondary btn btn-secondary-soft-hover py-1 px-2"
								id="cardShareAction2" data-bs-toggle="dropdown"
								aria-expanded="false">
								<i class="bi bi-three-dots"></i>
							  </a>
							  <!-- Card share action dropdown menu -->
							  <ul class="dropdown-menu dropdown-menu-end"
								aria-labelledby="cardShareAction2">
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-bookmark fa-fw pe-2"></i>Save
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-person-x fa-fw pe-2"></i>Unfollow lori
									ferguson </a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-x-circle fa-fw pe-2"></i>Hide
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-slash-circle fa-fw pe-2"></i>Block</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-flag fa-fw pe-2"></i>Report
									post</a></li>
							  </ul>
							</div>
							<!-- Card share action START -->
						  </div>
						</div>
						<!-- Card header START -->
		  
						<!-- Card body START -->
						<div class="card-body">
						  <p class="mb-0">Quickly design and customize responsive
							mobile-first sites with Bootstrap.</p>
						</div>
						<img src="assets/img/template/post/3by2/02.jpg" alt>
						<!-- Card body END -->
						<!-- Card footer START -->
						<div
						  class="card-footer border-0 d-flex justify-content-between align-items-center">
						  <p class="mb-0">Currently v5.1.3 </p>
						  <a class="btn btn-primary-soft btn-sm" href="#"> Download now
						  </a>
						</div>
						<!-- Card footer END -->
		  
					  </div>
					  <!-- Card feed item END -->
		  
					  <!-- Card feed item START -->
					  <div class="card">
						<!-- Card header START -->
						<div class="card-header border-0 pb-0">
						  <div class="d-flex align-items-center justify-content-between">
							<div class="d-flex align-items-center">
							  <!-- Avatar -->
							  <div class="avatar me-2">
								<a href="#"> <img class="avatar-img rounded-circle"
									src="assets/img/template/avatar/04.jpg" alt> </a>
							  </div>
							  <!-- Info -->
							  <div>
								<h6 class="card-title mb-0"> <a href="#"> Judy Nguyen
								  </a></h6>
								<div class="nav nav-divider">
								  <p class="nav-item mb-0 small">Web Developer at
									Webestica</p>
								  <span class="nav-item small" data-bs-toggle="tooltip"
									data-bs-placement="top" title="Public"> <i
									  class="bi bi-globe"></i> </span>
								</div>
							  </div>
							</div>
							<!-- Card share action START -->
							<div class="dropdown">
							  <a href="#"
								class="text-secondary btn btn-secondary-soft-hover py-1 px-2"
								id="cardShareAction3" data-bs-toggle="dropdown"
								aria-expanded="false">
								<i class="bi bi-three-dots"></i>
							  </a>
							  <!-- Card share action dropdown menu -->
							  <ul class="dropdown-menu dropdown-menu-end"
								aria-labelledby="cardShareAction3">
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-bookmark fa-fw pe-2"></i>Save
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-person-x fa-fw pe-2"></i>Unfollow lori
									ferguson </a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-x-circle fa-fw pe-2"></i>Hide
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-slash-circle fa-fw pe-2"></i>Block</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-flag fa-fw pe-2"></i>Report
									post</a></li>
							  </ul>
							</div>
							<!-- Card share action END -->
						  </div>
						</div>
						<!-- Card header START -->
						<!-- Card body START -->
						<div class="card-body">
						  <p>I'm so privileged to be involved in the <a
							  href="#!">@bootstrap </a>hiring process! Interviewing with
							their team was fun and I hope this can be a valuable resource
							for folks! <a href="#!"> #inclusivebusiness</a> <a href="#!">
							  #internship</a> <a href="#!"> #hiring</a> <a href="#">
							  #apply </a></p>
						  <!-- Card feed grid START -->
						  <div class="d-flex justify-content-between">
							<div class="row g-3">
							  <div class="col-6">
								<!-- Grid img -->
								<a href="assets/img/template/post/1by1/03.jpg" data-glightbox
								  data-gallery="image-popup">
								  <img class="rounded img-fluid"
									src="assets/img/template/post/1by1/03.jpg" alt="Image">
								</a>
							  </div>
							  <div class="col-6">
								<!-- Grid img -->
								<a href="assets/img/template/post/3by2/01.jpg" data-glightbox
								  data-gallery="image-popup">
								  <img class="rounded img-fluid"
									src="assets/img/template/post/3by2/01.jpg" alt="Image">
								</a>
								<!-- Grid img -->
								<div class="position-relative bg-dark mt-3 rounded">
								  <div
									class="hover-actions-item position-absolute top-50 start-50 translate-middle z-index-9">
									<a class="btn btn-link text-white" href="#"> View all
									</a>
								  </div>
								  <a href="assets/img/template/post/3by2/02.jpg" data-glightbox
									data-gallery="image-popup">
									<img class="img-fluid opacity-50 rounded"
									  src="assets/img/template/post/3by2/02.jpg" alt>
								  </a>
								</div>
							  </div>
							</div>
						  </div>
						  <!-- Card feed grid END -->
		  
						  <!-- Feed react START -->
						  <ul class="nav nav-stack py-3 small">
							<li class="nav-item">
							  <a class="nav-link active text-secondary" href="#!"> <i
								  class="bi bi-heart-fill me-1 icon-xs bg-danger text-white rounded-circle"></i>
								Louis, Billy and 126 others </a>
							</li>
							<li class="nav-item ms-sm-auto">
							  <a class="nav-link" href="#!"> <i
								  class="bi bi-chat-fill pe-1"></i>Comments (12)</a>
							</li>
						  </ul>
						  <!-- Feed react END -->
		  
						  <!-- Feed react START -->
						  <ul
							class="nav nav-pills nav-pills-light nav-fill nav-stack small border-top border-bottom py-1 mb-3">
							<li class="nav-item">
							  <a class="nav-link mb-0 active" href="#!"> <i
								  class="bi bi-heart pe-1"></i>Liked (56)</a>
							</li>
							<li class="nav-item">
							  <a class="nav-link mb-0" href="#!"> <i
								  class="bi bi-chat-fill pe-1"></i>Comments (12)</a>
							</li>
							<!-- Card share action menu START -->
							<li class="nav-item dropdown">
							  <a href="#" class="nav-link mb-0" id="cardShareAction4"
								data-bs-toggle="dropdown" aria-expanded="false">
								<i class="bi bi-reply-fill flip-horizontal ps-1"></i>Share
								(3)
							  </a>
							  <!-- Card share action dropdown menu -->
							  <ul class="dropdown-menu dropdown-menu-end"
								aria-labelledby="cardShareAction4">
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-envelope fa-fw pe-2"></i>Send via
									Direct Message</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-bookmark-check fa-fw pe-2"></i>Bookmark
								  </a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-link fa-fw pe-2"></i>Copy link to
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-share fa-fw pe-2"></i>Share post via
									…</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-pencil-square fa-fw pe-2"></i>Share to
									News Feed</a></li>
							  </ul>
							</li>
							<!-- Card share action menu END -->
							<li class="nav-item">
							  <a class="nav-link mb-0" href="#!"> <i
								  class="bi bi-send-fill pe-1"></i>Send</a>
							</li>
						  </ul>
						  <!-- Feed react START -->
		  
						  <!-- Comment wrap START -->
						  <ul class="comment-wrap list-unstyled">
							<!-- Comment item START -->
							<li class="comment-item">
							  <div class="d-flex">
								<!-- Avatar -->
								<div class="avatar avatar-xs">
								  <a href="#"> <img class="avatar-img rounded-circle"
									  src="assets/img/template/avatar/05.jpg" alt> </a>
								</div>
								<div class="ms-2">
								  <!-- Comment by -->
								  <div class="bg-light rounded-start-top-0 p-3 rounded">
									<div class="d-flex justify-content-between">
									  <h6 class="mb-1"> <a href="#!"> Frances Guerrero
										</a></h6>
									  <small class="ms-2">5hr</small>
									</div>
									<p class="small mb-0">Removed demands expense account
									  in outward tedious do. Particular way thoroughly
									  unaffected projection.</p>
								  </div>
								  <!-- Comment rect -->
								  <ul class="nav nav-divider py-2 small">
									<li class="nav-item">
									  <a class="nav-link" href="#!"> Like (3)</a>
									</li>
									<li class="nav-item">
									  <a class="nav-link" href="#!"> Reply</a>
									</li>
									<li class="nav-item">
									  <a class="nav-link" href="#!"> View 5 replies</a>
									</li>
								  </ul>
								</div>
							  </div>
							  <!-- Comment item nested START -->
							  <ul class="comment-item-nested list-unstyled">
								<!-- Comment item START -->
								<li class="comment-item">
								  <div class="d-flex">
									<!-- Avatar -->
									<div class="avatar avatar-xs">
									  <a href="#!"><img class="avatar-img rounded-circle"
										  src="assets/img/template/avatar/06.jpg" alt></a>
									</div>
									<!-- Comment by -->
									<div class="ms-2">
									  <div class="bg-light p-3 rounded">
										<div class="d-flex justify-content-between">
										  <h6 class="mb-1"> <a href="#!"> Lori Stevens
											</a> </h6>
										  <small>2hr</small>
										</div>
										<p class="small mb-0">See resolved goodness
										  felicity shy civility domestic had but Drawings
										  offended yet answered Jennings perceive.</p>
									  </div>
									  <!-- Comment rect -->
									  <ul class="nav nav-divider py-2 small">
										<li class="nav-item">
										  <a class="nav-link" href="#!"> Like (5)</a>
										</li>
										<li class="nav-item">
										  <a class="nav-link" href="#!"> Reply</a>
										</li>
									  </ul>
									</div>
								  </div>
								</li>
								<!-- Comment item END -->
								<!-- Comment item START -->
								<li class="comment-item">
								  <div class="d-flex">
									<!-- Avatar -->
									<div class="avatar avatar-xs">
									  <a href="#!"><img class="avatar-img rounded-circle"
										  src="assets/img/template/avatar/07.jpg" alt></a>
									</div>
									<!-- Comment by -->
									<div class="ms-2">
									  <div class="bg-light p-3 rounded">
										<div class="d-flex justify-content-between">
										  <h6 class="mb-1"> <a href="#!"> Billy Vasquez
											</a> </h6>
										  <small class="ms-2">15min</small>
										</div>
										<p class="small mb-0">Wishing calling is warrant
										  settled was lucky.</p>
									  </div>
									  <!-- Comment rect -->
									  <ul class="nav nav-divider py-2 small">
										<li class="nav-item">
										  <a class="nav-link" href="#!"> Like</a>
										</li>
										<li class="nav-item">
										  <a class="nav-link" href="#!"> Reply</a>
										</li>
									  </ul>
									</div>
								  </div>
								</li>
							  </ul>
							  <!-- Load more replies -->
							  <a href="#!" role="button"
								class="btn btn-link btn-link-loader btn-sm text-secondary d-flex align-items-center mb-3 ms-5"
								data-bs-toggle="button" aria-pressed="true">
								<div class="spinner-dots me-2">
								  <span class="spinner-dot"></span>
								  <span class="spinner-dot"></span>
								  <span class="spinner-dot"></span>
								</div>
								Load more replies
							  </a>
							</li>
							<!-- Comment item END -->
							<!-- Comment item START -->
							<li class="comment-item">
							  <div class="d-flex">
								<!-- Avatar -->
								<div class="avatar avatar-xs">
								  <a href="#!"><img class="avatar-img rounded-circle"
									  src="assets/img/template/avatar/05.jpg" alt></a>
								</div>
								<!-- Comment by -->
								<div class="ms-2">
								  <div class="bg-light p-3 rounded">
									<div class="d-flex justify-content-between">
									  <h6 class="mb-1"> <a href="#!"> Frances Guerrero
										</a> </h6>
									  <small class="ms-2">4min</small>
									</div>
									<p class="small mb-0">Congratulations:)</p>
									<div
									  class="card shadow-none p-2 border border-2 rounded mt-2">
									  <img src="assets/img/template/elements/11.svg" alt>
									</div>
								  </div>
								  <!-- Comment rect -->
								  <ul class="nav nav-divider pt-2 small">
									<li class="nav-item">
									  <a class="nav-link" href="#!"> Like (1)</a>
									</li>
									<li class="nav-item">
									  <a class="nav-link" href="#!"> Reply</a>
									</li>
									<li class="nav-item">
									  <a class="nav-link" href="#!"> View 6 replies</a>
									</li>
								  </ul>
								</div>
							  </div>
							</li>
							<!-- Comment item END -->
						  </ul>
						  <!-- Comment wrap END -->
						</div>
						<!-- Card body END -->
						<!-- Card footer START -->
						<div class="card-footer border-0 pt-0">
						  <!-- Load more comments -->
						  <a href="#!" role="button"
							class="btn btn-link btn-link-loader btn-sm text-secondary d-flex align-items-center"
							data-bs-toggle="button" aria-pressed="true">
							<div class="spinner-dots me-2">
							  <span class="spinner-dot"></span>
							  <span class="spinner-dot"></span>
							  <span class="spinner-dot"></span>
							</div>
							Load more comments
						  </a>
						</div>
						<!-- Card footer END -->
					  </div>
					  <!-- Card feed item END -->
		  
					  <!-- Card feed item START -->
					  <div class="card">
						<!-- Card header START -->
						<div class="card-header border-0 pb-0">
						  <div class="d-flex align-items-center justify-content-between">
							<div class="d-flex align-items-center">
							  <!-- Avatar -->
							  <div class="avatar me-2">
								<a href="#"> <img class="avatar-img rounded-circle"
									src="assets/img/template/logo/13.svg" alt> </a>
							  </div>
							  <!-- Title -->
							  <div>
								<h6 class="card-title mb-0"> <a href="#!"> Apple Education
								  </a></h6>
								<p class="mb-0 small">9 November at 23:29</p>
							  </div>
							</div>
							<!-- Card share action menu -->
							<a href="#"
							  class="text-secondary btn btn-secondary-soft-hover py-1 px-2"
							  id="cardShareAction5" data-bs-toggle="dropdown"
							  aria-expanded="false">
							  <i class="bi bi-three-dots"></i>
							</a>
							<!-- Card share action dropdown menu -->
							<ul class="dropdown-menu dropdown-menu-end"
							  aria-labelledby="cardShareAction5">
							  <li><a class="dropdown-item" href="#"> <i
									class="bi bi-bookmark fa-fw pe-2"></i>Save
								  post</a></li>
							  <li><a class="dropdown-item" href="#"> <i
									class="bi bi-person-x fa-fw pe-2"></i>Unfollow lori
								  ferguson </a></li>
							  <li><a class="dropdown-item" href="#"> <i
									class="bi bi-x-circle fa-fw pe-2"></i>Hide
								  post</a></li>
							  <li><a class="dropdown-item" href="#"> <i
									class="bi bi-slash-circle fa-fw pe-2"></i>Block</a></li>
							  <li><hr class="dropdown-divider"></li>
							  <li><a class="dropdown-item" href="#"> <i
									class="bi bi-flag fa-fw pe-2"></i>Report post</a></li>
							</ul>
						  </div>
						  <!-- Card share action END -->
						</div>
						<!-- Card header START -->
		  
						<!-- Card body START -->
						<div class="card-body pb-0">
						  <p>Find out how you can save time in the classroom this year.
							Earn recognition while you learn new skills on iPad and Mac.
							Start recognition your first Apple Teacher badge today!</p>
						  <!-- Feed react START -->
						  <ul class="nav nav-stack pb-2 small">
							<li class="nav-item">
							  <a class="nav-link active text-secondary" href="#!"> <i
								  class="bi bi-heart-fill me-1 icon-xs bg-danger text-white rounded-circle"></i>
								Louis, Billy and 126 others </a>
							</li>
							<li class="nav-item ms-sm-auto">
							  <a class="nav-link" href="#!"> <i
								  class="bi bi-chat-fill pe-1"></i>Comments (12)</a>
							</li>
						  </ul>
						  <!-- Feed react END -->
						</div>
						<!-- Card body END -->
						<!-- Card Footer START -->
						<div class="card-footer py-3">
						  <!-- Feed react START -->
						  <ul class="nav nav-fill nav-stack small">
							<li class="nav-item">
							  <a class="nav-link mb-0 active" href="#!"> <i
								  class="bi bi-heart pe-1"></i>Liked (56)</a>
							</li>
							<!-- Card share action dropdown START -->
							<li class="nav-item dropdown">
							  <a href="#" class="nav-link mb-0" id="cardShareAction6"
								data-bs-toggle="dropdown" aria-expanded="false">
								<i class="bi bi-reply-fill flip-horizontal ps-1"></i>Share
								(3)
							  </a>
							  <!-- Card share action dropdown menu -->
							  <ul class="dropdown-menu dropdown-menu-end"
								aria-labelledby="cardShareAction6">
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-envelope fa-fw pe-2"></i>Send via
									Direct Message</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-bookmark-check fa-fw pe-2"></i>Bookmark
								  </a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-link fa-fw pe-2"></i>Copy link to
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-share fa-fw pe-2"></i>Share post via
									…</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-pencil-square fa-fw pe-2"></i>Share to
									News Feed</a></li>
							  </ul>
							</li>
							<!-- Card share action dropdown END -->
							<li class="nav-item">
							  <a class="nav-link mb-0" href="#!"> <i
								  class="bi bi-send-fill pe-1"></i>Send</a>
							</li>
						  </ul>
						  <!-- Feed react END -->
						</div>
						<!-- Card Footer END -->
					  </div>
					  <!-- Card feed item END -->
		  
					  <!-- Card feed item START -->
					  <div class="card">
						<!-- Card header START -->
						<div
						  class="card-header d-flex justify-content-between align-items-center border-0 pb-0">
						  <h6 class="card-title mb-0">People you may know</h6>
						  <button class="btn btn-sm btn-primary-soft"> See all </button>
						</div>
						<!-- Card header START -->
		  
						<!-- Card body START -->
						<div class="card-body">
						  <div class="tiny-slider arrow-hover">
							<div class="tiny-slider-inner ms-n4" data-arrow="true"
							  data-dots="false" data-items-xl="3" data-items-lg="2"
							  data-items-md="2" data-items-sm="2" data-items-xs="1"
							  data-gutter="12" data-edge="30">
							  <!-- Slider items -->
							  <div>
								<!-- Card add friend item START -->
								<div class="card shadow-none text-center">
								  <!-- Card body -->
								  <div class="card-body p-2 pb-0">
									<div class="avatar avatar-xl">
									  <img class="avatar-img rounded-circle"
										src="assets/img/template/avatar/09.jpg" alt>
									</div>
									<h6 class="card-title mb-1 mt-3">Amanda Reed</h6>
									<p class="mb-0 small lh-sm">50 mutual connections</p>
								  </div>
								  <!-- Card footer -->
								  <div class="card-footer p-2 border-0">
									<button class="btn btn-sm btn-primary-soft w-100"> Add
									  friend </button>
								  </div>
								</div>
								<!-- Card add friend item END -->
							  </div>
							  <div>
								<!-- Card add friend item START -->
								<div class="card shadow-none text-center">
								  <!-- Card body -->
								  <div class="card-body p-2 pb-0">
									<div class="avatar avatar-story avatar-xl">
									  <img class="avatar-img rounded-circle"
										src="assets/img/template/avatar/10.jpg" alt>
									</div>
									<h6 class="card-title mb-1 mt-3">Larry Lawson</h6>
									<p class="mb-0 small lh-sm">33 mutual connections</p>
								  </div>
								  <!-- Card footer -->
								  <div class="card-footer p-2 border-0">
									<button class="btn btn-sm btn-primary-soft w-100"> Add
									  friend </button>
								  </div>
								</div>
								<!-- Card add friend item END -->
							  </div>
							  <div>
								<!-- Card add friend item START -->
								<div class="card shadow-none text-center">
								  <!-- Card body -->
								  <div class="card-body p-2 pb-0">
									<div class="avatar avatar-xl">
									  <img class="avatar-img rounded-circle"
										src="assets/img/template/avatar/11.jpg" alt>
									</div>
									<h6 class="card-title mb-1 mt-3">Louis Crawford</h6>
									<p class="mb-0 small lh-sm">45 mutual connections</p>
								  </div>
								  <!-- Card footer -->
								  <div class="card-footer p-2 border-0">
									<button class="btn btn-sm btn-primary-soft w-100"> Add
									  friend </button>
								  </div>
								</div>
								<!-- Card add friend item END -->
							  </div>
							  <div>
								<!-- Card add friend item START -->
								<div class="card shadow-none text-center">
								  <!-- Card body -->
								  <div class="card-body p-2 pb-0">
									<div class="avatar avatar-xl">
									  <img class="avatar-img rounded-circle"
										src="assets/img/template/avatar/12.jpg" alt>
									</div>
									<h6 class="card-title mb-1 mt-3">Dennis Barrett</h6>
									<p class="mb-0 small lh-sm">21 mutual connections</p>
								  </div>
								  <!-- Card footer -->
								  <div class="card-footer p-2 border-0">
									<button class="btn btn-sm btn-primary-soft w-100"> Add
									  friend </button>
								  </div>
								</div>
								<!-- Card add friend item END -->
							  </div>
							</div>
						  </div>
						</div>
						<!-- Card body END -->
					  </div>
					  <!-- Card feed item END -->
		  
					  <!-- Card feed item START -->
					  <div class="card">
						<!-- Card header START -->
						<div class="card-header border-0 pb-0">
						  <div class="d-flex align-items-center justify-content-between">
							<div class="d-flex align-items-center">
							  <!-- Avatar -->
							  <div class="avatar me-2">
								<a href="#"> <img class="avatar-img rounded-circle"
									src="assets/img/template/avatar/04.jpg" alt> </a>
							  </div>
							  <!-- Title -->
							  <div>
								<h6 class="card-title mb-0"> <a href="#!"> Apple Education
								  </a></h6>
								<p class="mb-0 small">9 November at 23:29</p>
							  </div>
							</div>
							<!-- Card share action menu -->
							<a href="#"
							  class="text-secondary btn btn-secondary-soft-hover py-1 px-2"
							  id="cardShareAction7" data-bs-toggle="dropdown"
							  aria-expanded="false">
							  <i class="bi bi-three-dots"></i>
							</a>
							<!-- Card share action dropdown menu -->
							<ul class="dropdown-menu dropdown-menu-end"
							  aria-labelledby="cardShareAction7">
							  <li><a class="dropdown-item" href="#"> <i
									class="bi bi-bookmark fa-fw pe-2"></i>Save
								  post</a></li>
							  <li><a class="dropdown-item" href="#"> <i
									class="bi bi-person-x fa-fw pe-2"></i>Unfollow lori
								  ferguson </a></li>
							  <li><a class="dropdown-item" href="#"> <i
									class="bi bi-x-circle fa-fw pe-2"></i>Hide
								  post</a></li>
							  <li><a class="dropdown-item" href="#"> <i
									class="bi bi-slash-circle fa-fw pe-2"></i>Block</a></li>
							  <li><hr class="dropdown-divider"></li>
							  <li><a class="dropdown-item" href="#"> <i
									class="bi bi-flag fa-fw pe-2"></i>Report post</a></li>
							</ul>
						  </div>
						  <!-- Card share action END -->
						</div>
						<!-- Card header START -->
		  
						<!-- Card body START -->
						<div class="card-body pb-0">
						  <p>How do you protect your business against cyber-crime?</p>
						  <div class="vstack gap-2">
							<!-- Feed poll item -->
							<div>
							  <input type="radio" class="btn-check" name="poll"
								id="option">
							  <label class="btn btn-outline-primary w-100" for="option">We
								have cybersecurity insurance coverage</label>
							</div>
							<!-- Feed poll item -->
							<div>
							  <input type="radio" class="btn-check" name="poll"
								id="option2">
							  <label class="btn btn-outline-primary w-100"
								for="option2">Our dedicated staff will protect us</label>
							</div>
							<!-- Feed poll item -->
							<div>
							  <input type="radio" class="btn-check" name="poll"
								id="option3">
							  <label class="btn btn-outline-primary w-100"
								for="option3">We give regular training for best
								practices</label>
							</div>
							<!-- Feed poll item -->
							<div>
							  <input type="radio" class="btn-check" name="poll"
								id="option4">
							  <label class="btn btn-outline-primary w-100"
								for="option4">Third-party vendor protection</label>
							</div>
						  </div>
		  
						  <!-- Feed poll votes START -->
						  <ul class="nav nav-divider mt-2 mb-0">
							<li class="nav-item">
							  <a class="nav-link" href="#">263 votes</a>
							</li>
							<li class="nav-item">
							  <a class="nav-link" href="#">2d left</a>
							</li>
						  </ul>
						  <!-- Feed poll votes ED -->
		  
						  <!-- Feed react START -->
						  <ul class="nav nav-stack pb-2 small mt-4">
							<li class="nav-item">
							  <a class="nav-link active text-secondary" href="#!"> <i
								  class="bi bi-heart-fill me-1 icon-xs bg-danger text-white rounded-circle"></i>
								Louis, Billy and 126 others </a>
							</li>
							<li class="nav-item ms-sm-auto">
							  <a class="nav-link" href="#!"> <i
								  class="bi bi-chat-fill pe-1"></i>Comments (12)</a>
							</li>
						  </ul>
						  <!-- Feed react END -->
						</div>
						<!-- Card body END -->
						<!-- Card Footer START -->
						<div class="card-footer py-3">
						  <!-- Feed react START -->
						  <ul class="nav nav-fill nav-stack small">
							<li class="nav-item">
							  <a class="nav-link mb-0 active" href="#!"> <i
								  class="bi bi-heart pe-1"></i>Liked (56)</a>
							</li>
							<!-- Card share action dropdown START -->
							<li class="nav-item dropdown">
							  <a href="#" class="nav-link mb-0" id="feedActionShare6"
								data-bs-toggle="dropdown" aria-expanded="false">
								<i class="bi bi-reply-fill flip-horizontal ps-1"></i>Share
								(3)
							  </a>
							  <!-- Card share action dropdown menu -->
							  <ul class="dropdown-menu dropdown-menu-end"
								aria-labelledby="feedActionShare6">
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-envelope fa-fw pe-2"></i>Send via
									Direct Message</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-bookmark-check fa-fw pe-2"></i>Bookmark
								  </a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-link fa-fw pe-2"></i>Copy link to
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-share fa-fw pe-2"></i>Share post via
									…</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-pencil-square fa-fw pe-2"></i>Share to
									News Feed</a></li>
							  </ul>
							</li>
							<!-- Card share action dropdown END -->
							<li class="nav-item">
							  <a class="nav-link mb-0" href="#!"> <i
								  class="bi bi-send-fill pe-1"></i>Send</a>
							</li>
						  </ul>
						  <!-- Feed react END -->
						</div>
						<!-- Card Footer END -->
					  </div>
					  <!-- Card feed item END -->
		  
					  <!-- Card feed item START -->
					  <div class="card">
						<!-- Card header START -->
						<div class="card-header">
						  <div class="d-flex align-items-center justify-content-between">
							<div class="d-flex align-items-center">
							  <!-- Avatar -->
							  <div class="avatar me-2">
								<a href="#!"> <img class="avatar-img rounded-circle"
									src="assets/img/template/logo/11.svg" alt> </a>
							  </div>
							  <!-- Info -->
							  <div>
								<h6 class="card-title mb-0"> <a href="#!"> Webestica
								  </a></h6>
								<p class="small mb-0">9 december at 10:00 </p>
							  </div>
							</div>
							<!-- Card share action START -->
							<div class="dropdown">
							  <a href="#"
								class="text-secondary btn btn-secondary-soft-hover py-1 px-2"
								id="cardShareAction8" data-bs-toggle="dropdown"
								aria-expanded="false">
								<i class="bi bi-three-dots"></i>
							  </a>
							  <!-- Card share action dropdown menu -->
							  <ul class="dropdown-menu dropdown-menu-end"
								aria-labelledby="cardShareAction8">
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-bookmark fa-fw pe-2"></i>Save
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-person-x fa-fw pe-2"></i>Unfollow lori
									ferguson </a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-x-circle fa-fw pe-2"></i>Hide
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-slash-circle fa-fw pe-2"></i>Block</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-flag fa-fw pe-2"></i>Report
									post</a></li>
							  </ul>
							</div>
							<!-- Card share action START -->
						  </div>
						</div>
						<!-- Card header START -->
		  
						<!-- Card body START -->
						<div class="card-body">
						  <p class="mb-0">The next-generation blog, news, and magazine
							theme for you to start sharing your content today with
							beautiful aesthetics! This minimal & clean Bootstrap 5 based
							theme is ideal for all types of sites that aim to provide
							users with content. <a href="#!"> #bootstrap</a> <a href="#!">
							  #webestica </a> <a href="#!"> #getbootstrap</a> <a href="#">
							  #bootstrap5 </a></p>
						</div>
						<!-- Card body END -->
						<a href="#!"> <img src="assets/img/template/post/3by2/03.jpg" alt> </a>
						<!-- Card body START -->
						<div class="card-body position-relative bg-light">
						  <a href="#!"
							class="small stretched-link">https://blogzine.webestica.com</a>
						  <h6 class="mb-0 mt-1">Blogzine - Blog and Magazine Bootstrap 5
							Theme</h6>
						  <p class="mb-0 small">Bootstrap based News, Magazine and Blog
							Theme</p>
						</div>
						<!-- Card body END -->
		  
						<!-- Card Footer START -->
						<div class="card-footer py-3">
						  <!-- Feed react START -->
						  <ul class="nav nav-fill nav-stack small">
							<li class="nav-item">
							  <a class="nav-link mb-0 active" href="#!"> <i
								  class="bi bi-heart pe-1"></i>Liked (56)</a>
							</li>
							<li class="nav-item">
							  <a class="nav-link mb-0" href="#!"> <i
								  class="bi bi-chat-fill pe-1"></i>Comments (12)</a>
							</li>
							<!-- Card share action dropdown START -->
							<li class="nav-item dropdown">
							  <a href="#" class="nav-link mb-0" id="feedActionShare7"
								data-bs-toggle="dropdown" aria-expanded="false">
								<i class="bi bi-reply-fill flip-horizontal ps-1"></i>Share
								(3)
							  </a>
							  <!-- Card share action dropdown menu -->
							  <ul class="dropdown-menu dropdown-menu-end"
								aria-labelledby="feedActionShare7">
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-envelope fa-fw pe-2"></i>Send via
									Direct Message</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-bookmark-check fa-fw pe-2"></i>Bookmark
								  </a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-link fa-fw pe-2"></i>Copy link to
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-share fa-fw pe-2"></i>Share post via
									…</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-pencil-square fa-fw pe-2"></i>Share to
									News Feed</a></li>
							  </ul>
							</li>
							<!-- Card share action dropdown END -->
							<li class="nav-item">
							  <a class="nav-link mb-0" href="#!"> <i
								  class="bi bi-send-fill pe-1"></i>Send</a>
							</li>
						  </ul>
						  <!-- Feed react END -->
						</div>
						<!-- Card Footer END -->
		  
					  </div>
					  <!-- Card feed item END -->
		  
					  <!-- Card feed item START -->
					  <div class="card">
						<!-- Card header START -->
						<div class="card-header border-0 pb-0">
						  <div class="d-flex align-items-center justify-content-between">
							<div class="d-flex align-items-center">
							  <!-- Avatar -->
							  <div class="avatar avatar-story me-2">
								<a href="#!"> <img class="avatar-img rounded-circle"
									src="assets/img/template/avatar/12.jpg" alt> </a>
							  </div>
							  <!-- Info -->
							  <div>
								<div class="nav nav-divider">
								  <h6 class="nav-item card-title mb-0"> <a href="#!"> Joan
									  Wallace </a></h6>
								  <span class="nav-item small"> 1day</span>
								</div>
								<p class="mb-0 small">12 January at 12:00</p>
							  </div>
							</div>
							<!-- Card feed action dropdown START -->
							<div class="dropdown">
							  <a href="#"
								class="text-secondary btn btn-secondary-soft-hover py-1 px-2"
								id="cardFeedAction2" data-bs-toggle="dropdown"
								aria-expanded="false">
								<i class="bi bi-three-dots"></i>
							  </a>
							  <!-- Card feed action dropdown menu -->
							  <ul class="dropdown-menu dropdown-menu-end"
								aria-labelledby="cardFeedAction2">
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-bookmark fa-fw pe-2"></i>Save
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-person-x fa-fw pe-2"></i>Unfollow lori
									ferguson </a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-x-circle fa-fw pe-2"></i>Hide
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-slash-circle fa-fw pe-2"></i>Block</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-flag fa-fw pe-2"></i>Report
									post</a></li>
							  </ul>
							</div>
							<!-- Card feed action dropdown END -->
						  </div>
						</div>
						<!-- Card header END -->
						<!-- Card body START -->
						<div class="card-body pb-0">
						  <p>Comfort reached gay perhaps chamber his <a
							  href="#!">#internship</a> <a href="#!">#hiring</a> <a
							  href="#!">#apply</a> </p>
						</div>
						<!-- Card img -->
						<div class="overflow-hidden fullscreen-video w-100">
		  
						  <!-- HTML video START -->
						  <div class="player-wrapper overflow-hidden">
							<video class="player-html" controls crossorigin="anonymous"
							  poster="assets/img/template/videos/poster.jpg">
							  <source src="assets/img/template/videos/video-feed.mp4"
								type="video/mp4">
							</video>
						  </div>
						  <!-- HTML video END -->
		  
						  <!-- Plyr resources and browser polyfills are specified in the pen settings -->
						</div>
						<!-- Feed react START -->
						<div class="card-body pt-0">
						  <!-- Feed react START -->
						  <ul
							class="nav nav-pills nav-pills-light nav-fill nav-stack small border-top border-bottom py-1 my-3">
							<li class="nav-item">
							  <a class="nav-link mb-0 active" href="#!"> <i
								  class="bi bi-heart pe-1"></i>Liked (56)</a>
							</li>
							<li class="nav-item">
							  <a class="nav-link mb-0" href="#!"> <i
								  class="bi bi-chat-fill pe-1"></i>Comments (12)</a>
							</li>
							<!-- Card share action menu START -->
							<li class="nav-item dropdown">
							  <a href="#" class="nav-link mb-0" id="cardShareAction9"
								data-bs-toggle="dropdown" aria-expanded="false">
								<i class="bi bi-reply-fill flip-horizontal ps-1"></i>Share
								(3)
							  </a>
							  <!-- Card share action dropdown menu -->
							  <ul class="dropdown-menu dropdown-menu-end"
								aria-labelledby="cardShareAction9">
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-envelope fa-fw pe-2"></i>Send via
									Direct Message</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-bookmark-check fa-fw pe-2"></i>Bookmark
								  </a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-link fa-fw pe-2"></i>Copy link to
									post</a></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-share fa-fw pe-2"></i>Share post via
									…</a></li>
								<li><hr class="dropdown-divider"></li>
								<li><a class="dropdown-item" href="#"> <i
									  class="bi bi-pencil-square fa-fw pe-2"></i>Share to
									News Feed</a></li>
							  </ul>
							</li>
							<!-- Card share action menu END -->
							<li class="nav-item">
							  <a class="nav-link mb-0" href="#!"> <i
								  class="bi bi-send-fill pe-1"></i>Send</a>
							</li>
						  </ul>
						  <!-- Feed react START -->
		  
						  <!-- Add comment -->
						  <div class="d-flex mb-3">
							<!-- Avatar -->
							<div class="avatar avatar-xs me-2">
							  <a href="#!"> <img class="avatar-img rounded-circle"
								  src="assets/img/template/avatar/12.jpg" alt> </a>
							</div>
							<!-- Comment box  -->
							<!-- Comment box  -->
							<form class="position-relative w-100">
							  <textarea class="form-control pe-4 bg-light" data-autoresize
								rows="1" placeholder="Add a comment..."></textarea>
							  <!-- Emoji button -->
							  <div class="position-absolute top-0 end-0">
								<button class="second-btn btn" type="button">🙂</button>
							  </div>
							</form>
						  </div>
						  <!-- Comment wrap START -->
						  <ul class="comment-wrap list-unstyled mb-0">
							<!-- Comment item START -->
							<li class="comment-item">
							  <div class="d-flex">
								<!-- Avatar -->
								<div class="avatar avatar-xs">
								  <a href="#!"><img class="avatar-img rounded-circle"
									  src="assets/img/template/avatar/05.jpg" alt></a>
								</div>
								<div class="ms-2">
								  <!-- Comment by -->
								  <div class="bg-light rounded-start-top-0 p-3 rounded">
									<div class="d-flex justify-content-between">
									  <h6 class="mb-1"> <a href="#!"> Frances Guerrero
										</a></h6>
									  <small class="ms-2">5hr</small>
									</div>
									<p class="small mb-0">Preference any astonished
									  unreserved Mrs.</p>
								  </div>
								  <!-- Comment react -->
								  <ul class="nav nav-divider py-2 small">
									<li class="nav-item">
									  <a class="nav-link" href="#!"> Like (3)</a>
									</li>
									<li class="nav-item">
									  <a class="nav-link" href="#!"> Reply</a>
									</li>
									<li class="nav-item">
									  <a class="nav-link" href="#!"> View 5 replies</a>
									</li>
								  </ul>
								</div>
							  </div>
							  <!-- Comment item nested START -->
							  <ul class="comment-item-nested list-unstyled">
								<!-- Comment item START -->
								<li class="comment-item">
								  <div class="d-flex">
									<!-- Avatar -->
									<div class="avatar avatar-xs">
									  <a href="#!"><img class="avatar-img rounded-circle"
										  src="assets/img/template/avatar/06.jpg" alt></a>
									</div>
									<!-- Comment by -->
									<div class="ms-2">
									  <div class="bg-light p-3 rounded">
										<div class="d-flex justify-content-between">
										  <h6 class="mb-1"> <a href="#!"> Lori Stevens
											</a> </h6>
										  <small class="ms-2">2hr</small>
										</div>
										<p class="small mb-0">Dependent on so extremely
										  delivered by. Yet no jokes worse her why.</p>
									  </div>
									  <!-- Comment react -->
									  <ul class="nav nav-divider py-2 small">
										<li class="nav-item">
										  <a class="nav-link" href="#!"> Like (5)</a>
										</li>
										<li class="nav-item">
										  <a class="nav-link" href="#!"> Reply</a>
										</li>
									  </ul>
									</div>
								  </div>
								</li>
								<!-- Comment item END -->
							  </ul>
							  <!-- Comment item nested END -->
							</li>
							<!-- Comment item END -->
						  </ul>
						  <!-- Comment wrap END -->
						</div>
						<!-- Card body END -->
						<!-- Card footer START -->
						<div class="card-footer border-0 pt-0">
						  <!-- Load more comments -->
						  <a href="#!" role="button"
							class="btn btn-link btn-link-loader btn-sm text-secondary d-flex align-items-center"
							data-bs-toggle="button" aria-pressed="true">
							<div class="spinner-dots me-2">
							  <span class="spinner-dot"></span>
							  <span class="spinner-dot"></span>
							  <span class="spinner-dot"></span>
							</div>
							Load more comments
						  </a>
						</div>
						<!-- Card footer END -->
					  </div>
					  <!-- Card feed item END -->
		  
					  <!-- Load more button START -->
					  <a href="#!" role="button" class="btn btn-loader btn-primary-soft"
						data-bs-toggle="button" aria-pressed="true">
						<span class="load-text"> Load more </span>
						<div class="load-icon">
						  <div class="spinner-grow spinner-grow-sm" role="status">
							<span class="visually-hidden">Loading...</span>
						  </div>
						</div>
					  </a>
					  <!-- Load more button END -->
		  
				</div>
				<!-- Main content END -->

					<!-- Right sidebar START -->
					<div class="col-lg-3 mt-0">
						<div class="row g-4">
							<!-- Card follow START -->
							<div class="col-sm-6 col-lg-12">
								<div class="card">
									<!-- Card header START -->
									<div class="card-header pb-0 border-0">
										<h5 class="card-title mb-0">Who to follow</h5>
									</div>
									<!-- Card header END -->
									<!-- Card body START -->
									<div class="card-body">
										<!-- Connection item START -->
										<div class="hstack gap-2 mb-3">
											<!-- Avatar -->
											<div class="avatar">
												<a href="#!"><img class="avatar-img rounded-circle"
														src="assets/img/template/avatar/04.jpg" alt></a>
											</div>
											<!-- Title -->
											<div class="overflow-hidden">
												<a class="h6 mb-0" href="#!">Judy Nguyen </a>
												<p class="mb-0 small text-truncate">News anchor</p>
											</div>
											<!-- Button -->
											<a class="btn btn-primary-soft rounded-circle icon-md ms-auto"
												href="#"><i class="fa-solid fa-plus"> </i></a>
										</div>
										<!-- Connection item END -->
										<!-- Connection item START -->
										<div class="hstack gap-2 mb-3">
											<!-- Avatar -->
											<div class="avatar avatar-story">
												<a href="#!"> <img class="avatar-img rounded-circle"
														src="assets/img/template/avatar/05.jpg" alt> </a>
											</div>
											<!-- Title -->
											<div class="overflow-hidden">
												<a class="h6 mb-0" href="#!">Amanda Reed </a>
												<p class="mb-0 small text-truncate">Web Developer</p>
											</div>
											<!-- Button -->
											<a class="btn btn-primary-soft rounded-circle icon-md ms-auto"
												href="#"><i class="fa-solid fa-plus"> </i></a>
										</div>
										<!-- Connection item END -->

										<!-- Connection item START -->
										<div class="hstack gap-2 mb-3">
											<!-- Avatar -->
											<div class="avatar">
												<a href="#"> <img class="avatar-img rounded-circle"
														src="assets/img/template/avatar/11.jpg" alt> </a>
											</div>
											<!-- Title -->
											<div class="overflow-hidden">
												<a class="h6 mb-0" href="#!">Billy Vasquez </a>
												<p class="mb-0 small text-truncate">News anchor</p>
											</div>
											<!-- Button -->
											<a class="btn btn-primary rounded-circle icon-md ms-auto" href="#"><i
													class="bi bi-person-check-fill"> </i></a>
										</div>
										<!-- Connection item END -->

										<!-- Connection item START -->
										<div class="hstack gap-2 mb-3">
											<!-- Avatar -->
											<div class="avatar">
												<a href="#"> <img class="avatar-img rounded-circle"
														src="assets/img/template/avatar/01.jpg" alt> </a>
											</div>
											<!-- Title -->
											<div class="overflow-hidden">
												<a class="h6 mb-0" href="#!">Lori Ferguson </a>
												<p class="mb-0 small text-truncate">Web Developer at Webestica</p>
											</div>
											<!-- Button -->
											<a class="btn btn-primary-soft rounded-circle icon-md ms-auto"
												href="#"><i class="fa-solid fa-plus"> </i></a>
										</div>
										<!-- Connection item END -->

										<!-- Connection item START -->
										<div class="hstack gap-2 mb-3">
											<!-- Avatar -->
											<div class="avatar">
												<a href="#"> <img class="avatar-img rounded-circle"
														src="assets/img/template/avatar/placeholder.jpg" alt> </a>
											</div>
											<!-- Title -->
											<div class="overflow-hidden">
												<a class="h6 mb-0" href="#!">Carolyn Ortiz </a>
												<p class="mb-0 small text-truncate">News anchor</p>
											</div>
											<!-- Button -->
											<a class="btn btn-primary-soft rounded-circle icon-md ms-auto"
												href="#"><i class="fa-solid fa-plus"> </i></a>
										</div>
										<!-- Connection item END -->

										<!-- View more button -->
										<div class="d-grid mt-3">
											<a class="btn btn-sm btn-primary-soft" href="#!">View more</a>
										</div>
									</div>
									<!-- Card body END -->
								</div>
							</div>
							<!-- Card follow START -->

							<!-- Card News START -->
							<div class="col-sm-6 col-lg-12">
								<div class="card">
									<!-- Card header START -->
									<div class="card-header pb-0 border-0">
										<h5 class="card-title mb-0">Today’s news</h5>
									</div>
									<!-- Card header END -->
									<!-- Card body START -->
									<div class="card-body">
										<!-- News item -->
										<div class="mb-3">
											<h6 class="mb-0"><a href="blog-details.html">Ten questions you should
													answer truthfully</a></h6>
											<small>2hr</small>
										</div>
										<!-- News item -->
										<div class="mb-3">
											<h6 class="mb-0"><a href="blog-details.html">Five unbelievable facts
													about money</a></h6>
											<small>3hr</small>
										</div>
										<!-- News item -->
										<div class="mb-3">
											<h6 class="mb-0"><a href="blog-details.html">Best Pinterest Boards
													for learning about business</a></h6>
											<small>4hr</small>
										</div>
										<!-- News item -->
										<div class="mb-3">
											<h6 class="mb-0"><a href="blog-details.html">Skills that you can
													learn from business</a></h6>
											<small>6hr</small>
										</div>
										<!-- Load more comments -->
										<a href="#!" role="button"
											class="btn btn-link btn-link-loader btn-sm text-secondary d-flex align-items-center"
											data-bs-toggle="button" aria-pressed="true">
											<div class="spinner-dots me-2">
												<span class="spinner-dot"></span>
												<span class="spinner-dot"></span>
												<span class="spinner-dot"></span>
											</div>
											View all latest news
										</a>
									</div>
									<!-- Card body END -->
								</div>
							</div>
							<!-- Card News END -->
						</div>
					</div>
					<!-- Right sidebar END -->

@endsection