<?php

namespace App\Http\Controllers;

use App\Actions\Settings\SetDefaultSettingsForUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(string $provider)
    {
        $this->authorizeOauthRequest($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $this->authorizeOauthRequest($provider);

        $authUser = Socialite::driver($provider)->user();

        // If a user with the provided email address already exists, register the oauth login
        if ($user = User::where('email', $authUser->getEmail())->first()) {
            if ($user->sso_provider !== null && $user->sso_provider !== $provider) {
                abort(403, trans('auth.sso_wrong_provider', [
                    'currentProvider' => trans('auth.sso_provider.' . $provider),
                    'userProvider' => trans('auth.sso_provider.' . $user->sso_provider),
                ]));
            }

            $user->update([
                'name' => $authUser->getNickname() ?: Str::studly($authUser->getName()),
                'sso_id' => $authUser->id,
                'sso_provider' => $provider,
                'sso_token' => $authUser->token ?? null,
                'sso_token_secret' => $authUser->tokenSecret ?? null,
                'sso_refresh_token' => $authUser->refreshToken ?? null,
            ]);
        } elseif (config('auth.sso.registration_enabled') === false) {
            // Users should not be able to register new accounts on their own
            $user = User::where([
                'email' => $authUser->getEmail(),
                'sso_id' => $authUser->getId(),
                'sso_provider' => $provider,
            ])->first();

            if ($user === null) {
                abort(403, trans('auth.sso_registration_disabled'));
            }
        } else {
            // otherwise, either update an existing oauth user or register a new user
            $user = User::updateOrCreate([
                'email' => $authUser->getEmail(),
                'sso_id' => $authUser->getId(),
                'sso_provider' => $provider,
            ], [
                'name' => $authUser->getNickname() ?: Str::studly($authUser->getName()),
                'email' => $authUser->getEmail(),
                'sso_id' => $authUser->getId(),
                'sso_provider' => $provider,
                'sso_token' => $authUser->token ?? null,
                'sso_token_secret' => $authUser->tokenSecret ?? null,
                'sso_refresh_token' => $authUser->refreshToken ?? null,
            ]);

            if ($user->wasRecentlyCreated) {
                (new SetDefaultSettingsForUser($user))->up();
            }
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    protected function authorizeOauthRequest(string $provider): void
    {
        if (config('auth.sso.enabled') !== true || !in_array($provider, config('auth.sso.providers'))) {
            abort(403, trans('auth.unauthorized'));
        }

        if (config('services.' . $provider . '.enabled') !== true) {
            abort(403, trans('auth.sso_provider_disabled'));
        }
    }
}
