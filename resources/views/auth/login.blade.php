@extends('layouts.guest')

@section('guest-content')

                <div class="row justify-content-center pb-2">
                    <!-- Main content START -->
                    <div class="col-sm-10 col-md-8 col-lg-7 col-xl-6 col-xxl-5">
                        <!-- Sign in START -->
                        <div class="card card-body text-center p-4 p-sm-5">
                            <!-- Title -->
                            <h1 class="mb-4 text-success">Identifiez-vous</h1>

                            <!-- Form START -->
                            <form>
                                <!-- Email, Phone or Username -->
                                <div class="mt-4 mb-3 input-group-lg">
                                    <input type="text" name="login_username" class="form-control" placeholder="E-mail, n° de téléphone ou pseudo" autofocus>
                                </div>

                                <!-- Password -->
                                <div class="mb-3 position-relative">
                                    <!-- Password -->
                                    <div class="input-group input-group-lg">
                                        <input type="password" name="login_password" id="psw-input" class="form-control fakepassword" placeholder="Mot de passe">
                                        <span class="input-group-text p-0">
                                            <i class="fakepasswordicon fa-solid fa-eye-slash cursor-pointer p-2 w-40px"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Remember me -->
                                <div class="mb-4 d-sm-flex justify-content-between">
                                    <div>
                                        <input type="checkbox" class="form-check-input" id="rememberCheck">
                                        <label class="form-check-label" for="rememberCheck">Rester connecté</label>
                                    </div>
                                    <a href="" role="button">Mot de passe oublié ?</a>
                                </div>

                                <!-- Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-lg btn-primary">Connexion</button>
                                </div>
                                <!-- Register -->
                                <p class="mt-4 mb-0" style="line-height: 20px;">Vous n'avez pas de compte ?<a href="" role="button"> <br class="d-sm-none d-block">Cliquez ici pour vous inscrire</a></p>
                            </form>
                            <!-- Form END -->
                        </div>
                        <!-- Sign in END -->
                    </div>
                </div> <!-- Row END -->

@endsection