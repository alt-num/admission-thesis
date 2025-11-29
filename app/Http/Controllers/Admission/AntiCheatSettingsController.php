<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\AntiCheatSetting;
use Illuminate\Http\Request;

class AntiCheatSettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->checkAdminAccess();
    }

    /**
     * Check if the current user is an admin.
     */
    private function checkAdminAccess()
    {
        $user = auth()->guard('admission')->user();
        if (!$user || $user->role !== 'Admin') {
            abort(403, 'Only administrators can access anti-cheat settings.');
        }
    }

    /**
     * Display the anti-cheat settings page.
     */
    public function index()
    {
        $this->checkAdminAccess();
        $settings = AntiCheatSetting::current();
        
        // The current() method migrates old defaults and accessors handle null/0 values
        // So the settings object will always have Balanced Mode defaults (5 and 10)
        
        return view('admission.settings.anticheat', compact('settings'));
    }

    /**
     * Update anti-cheat settings.
     */
    public function update(Request $request)
    {
        $this->checkAdminAccess();
        
        // Validate only non-boolean fields (boolean toggles are normalized manually)
        $validated = $request->validate([
            'idle_timeout_minutes' => 'required|integer|min:1|max:60',
            'ip_check_strictness' => 'required|in:log_only,warn,block',
        ]);

        $settings = AntiCheatSetting::current();
        
        // Normalize all boolean toggles manually (handles unchecked checkboxes)
        // When checkbox is unchecked, it's not in the request, so we set it to false
        $settings->enabled = $request->has('enabled');
        $settings->tab_switch_detection = $request->has('tab_switch_detection');
        $settings->focus_loss_violations = $request->has('focus_loss_violations');
        $settings->copy_paste_blocking = $request->has('copy_paste_blocking');
        $settings->right_click_blocking = $request->has('right_click_blocking');
        $settings->devtools_hotkey_blocking = $request->has('devtools_hotkey_blocking');
        $settings->ip_change_logging = $request->has('ip_change_logging');
        $settings->exam_code_required = $request->has('exam_code_required');
        $settings->refresh_detection = $request->has('refresh_detection');
        $settings->developer_bypass_enabled = $request->has('developer_bypass_enabled');
        $settings->monitoring_banner_enabled = $request->has('monitoring_banner_enabled');
        
        // Set validated non-boolean fields
        $settings->idle_timeout_minutes = $validated['idle_timeout_minutes'];
        $settings->ip_check_strictness = $validated['ip_check_strictness'];
        
        $settings->save();

        return redirect()
            ->route('admission.settings.anticheat')
            ->with('success', 'Anti-cheat settings updated successfully!');
    }
}
