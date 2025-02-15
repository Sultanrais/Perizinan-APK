<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function system()
    {
        $settings = [
            'app_name' => config('app.name'),
            'notification_enabled' => true,
            'auto_delete_notifications' => 30, // hari
            'max_upload_size' => 10, // MB
            'allowed_file_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
        ];

        return view('settings.system', compact('settings'));
    }

    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'notification_enabled' => 'boolean',
            'auto_delete_notifications' => 'required|integer|min:1|max:365',
            'max_upload_size' => 'required|integer|min:1|max:100',
            'allowed_file_types' => 'required|array|min:1',
            'allowed_file_types.*' => 'string|max:10',
        ]);

        // Update .env file
        $this->updateEnvFile('APP_NAME', $validated['app_name']);

        // Update settings in database/cache
        $settings = [
            'notification_enabled' => $validated['notification_enabled'],
            'auto_delete_notifications' => $validated['auto_delete_notifications'],
            'max_upload_size' => $validated['max_upload_size'],
            'allowed_file_types' => $validated['allowed_file_types'],
        ];

        foreach ($settings as $key => $value) {
            $this->updateSetting($key, $value);
        }

        return redirect()
            ->back()
            ->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    public function account()
    {
        $settings = [
            'notifications' => [
                'email_enabled' => true,
                'browser_enabled' => true,
            ],
            'display' => [
                'items_per_page' => 10,
                'theme' => 'light',
            ],
        ];

        return view('settings.account', compact('settings'));
    }

    public function updateAccount(Request $request)
    {
        $validated = $request->validate([
            'notifications.email_enabled' => 'boolean',
            'notifications.browser_enabled' => 'boolean',
            'display.items_per_page' => 'required|integer|min:5|max:100',
            'display.theme' => 'required|in:light,dark',
        ]);

        // Update user settings
        $settings = [
            'notifications' => [
                'email_enabled' => $validated['notifications']['email_enabled'],
                'browser_enabled' => $validated['notifications']['browser_enabled'],
            ],
            'display' => [
                'items_per_page' => $validated['display']['items_per_page'],
                'theme' => $validated['display']['theme'],
            ],
        ];

        // Save to user preferences (assuming we'll implement user authentication later)
        foreach ($settings as $group => $values) {
            foreach ($values as $key => $value) {
                $this->updateUserSetting($group, $key, $value);
            }
        }

        return redirect()
            ->back()
            ->with('success', 'Pengaturan akun berhasil diperbarui.');
    }

    private function updateEnvFile($key, $value)
    {
        $path = base_path('.env');
        $content = file_get_contents($path);

        // Escape special characters in the value
        $value = str_replace('"', '\"', $value);
        
        // Update existing value
        if (preg_match("/^{$key}=.*/m", $content)) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
        }
        // Add new value
        else {
            $content .= "\n{$key}=\"{$value}\"";
        }

        file_put_contents($path, $content);
    }

    private function updateSetting($key, $value)
    {
        // Store in database
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value]
        );

        // Update cache
        Cache::forever("settings.{$key}", $value);
    }

    private function updateUserSetting($group, $key, $value)
    {
        // This is a placeholder. In a real application, you would:
        // 1. Get the authenticated user
        // 2. Update their settings in the database
        // 3. Update the session/cache if needed
        
        // For now, we'll just store it in the session
        session(["user.settings.{$group}.{$key}" => $value]);
    }
}
