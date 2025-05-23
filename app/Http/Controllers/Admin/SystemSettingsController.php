<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ActivityLog;
use App\Helper\UpdateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemSettingsUpdateRequest;
use App\Settings\GuestSettings;
use App\Settings\SystemSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class SystemSettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.system-settings.index', [
            'linkaceVersion' => UpdateHelper::currentVersion(),
        ]);
    }

    public function update(SystemSettingsUpdateRequest $request): RedirectResponse
    {
        $sysSettings = app(SystemSettings::class);

        $settings = $request->except(['_token', 'guest_share']);

        foreach ($settings as $key => $value) {
            $sysSettings->$key = $value;
        }

        $sysSettings->save();

        flash(trans('settings.settings_saved'));
        return redirect()->route('get-systemsettings');
    }

    public function updateGuest(SystemSettingsUpdateRequest $request): RedirectResponse
    {
        $guestSettings = app(GuestSettings::class);
        $systemSettings = app(SystemSettings::class);

        $systemSettings->guest_access_enabled = $request->input('guest_access_enabled');

        $settings = $request->except(['_token', 'guest_share', 'guest_access_enabled']);

        foreach ($settings as $key => $value) {
            $guestSettings->$key = $value;
        }

        // Enable / disable sharing services for guests
        $guestSharingSettings = $request->input('guest_share');
        if ($guestSharingSettings) {
            foreach (config('sharing.services') as $service => $details) {
                $guestSettings->{'share_' . $service} = array_key_exists($service, $guestSharingSettings);
            }
        }

        $systemSettings->save();
        $guestSettings->save();

        flash(trans('settings.settings_saved'));
        return redirect()->route('get-systemsettings');
    }

    public function generateCronToken(SystemSettings $settings): JsonResponse
    {
        $newToken = Str::random(32);

        $settings->cron_token = $newToken;
        $settings->save();

        activity()->by(auth()->user())->log(ActivityLog::SYSTEM_CRON_TOKEN_REGENERATED);

        return response()->json([
            'new_token' => $newToken,
        ]);
    }
}
