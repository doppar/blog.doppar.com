#extends('layouts.app')
#section('title')
    Create Administrator
#endsection
#section('body_class')
    auth-body
#endsection
#section('content')
    <main class="auth-shell">
        #include('partials.auth-side')
        <section class="auth-card">
            <div class="auth-card__brand">
                <span class="auth-card__mark"><img src="/logo-icon.png" alt=""></span>
                <div>
                    <p class="auth-card__eyebrow">First-time setup</p>
                    <h1>Create the first admin</h1>
                    <p>This account becomes the initial administrator for the CMS.</p>
                </div>
            </div>

            <form class="auth-form" action="[[ route('register') ]]" method="POST">
                #csrf

                <div class="auth-field">
                    <label for="name">Full name</label>
                    <input id="name" name="name" type="text" value="[[ old('name') ]]" placeholder="Your name" autocomplete="name">
                </div>

                <div class="auth-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="[[ old('email') ]]" placeholder="admin@example.com" autocomplete="email">
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Create a secure password" autocomplete="new-password">
                </div>

                <div class="auth-field">
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Repeat your password" autocomplete="new-password">
                </div>

                <button class="auth-submit" type="submit">Create Administrator</button>
            </form>
        </section>
    </main>
#endsection
