/**
 * Anti-Cheat Manager
 * 
 * Centralized manager for anti-cheat monitoring on applicant exam pages.
 * This module provides hooks for various suspicious behaviors and logs them
 * to the backend. It should ONLY be loaded on exam-taking pages.
 */

class AntiCheatManager {
    constructor(config) {
        this.config = {
            enabled: config.enabled !== false,
            attemptId: config.attemptId,
            logEndpoint: config.logEndpoint || '/applicant/exam/anticheat/log',
            csrfToken: config.csrfToken,
            features: config.features || {
                tabSwitchDetection: true,
                focusLossViolations: true,
                copyPasteBlocking: true,
                rightClickBlocking: true,
                devtoolsHotkeyBlocking: true,
            },
        };

        this.isInitialized = false;
        this.eventQueue = [];
        this.isLogging = false;
        this.pageLoadedAt = Date.now(); // Track when page was loaded (to ignore initial load/reload)
        this.initialLoadGracePeriod = 2000; // 2 seconds grace period after page load
        this.isPageUnloading = false; // Track if page is being unloaded (refresh/reload)
    }

    /**
     * Initialize the anti-cheat manager and set up event listeners.
     */
    init() {
        if (!this.config.enabled) {
            console.log('[AntiCheat] Disabled - not initializing');
            return;
        }

        if (this.isInitialized) {
            console.warn('[AntiCheat] Already initialized - cleaning up and re-initializing');
            this.cleanupEventListeners();
        }

        this.isInitialized = true;
        this.pageLoadedAt = Date.now(); // Reset page load time on init
        this.setupEventListeners();
        this.setupPageUnloadDetection();
        console.log('[AntiCheat] Initialized');
    }

    /**
     * Set up detection for page unload (refresh/reload) to ignore violations during reload.
     */
    setupPageUnloadDetection() {
        // Detect page unload (refresh/reload)
        window.addEventListener('beforeunload', () => {
            this.isPageUnloading = true;
        });

        window.addEventListener('unload', () => {
            this.isPageUnloading = true;
        });

        // Also detect pagehide (more reliable for mobile)
        window.addEventListener('pagehide', () => {
            this.isPageUnloading = true;
        });
    }

    /**
     * Set up all event listeners for suspicious behaviors.
     * Uses bound methods to ensure listeners can be properly removed.
     */
    setupEventListeners() {
        // Store bound methods for cleanup
        this.boundHandlers = {
            onWindowBlur: this.onWindowBlur.bind(this),
            onWindowFocus: this.onWindowFocus.bind(this),
            onVisibilityChange: this.onVisibilityChange.bind(this),
            onCopyAttempt: this.onCopyAttempt.bind(this),
            onCutAttempt: this.onCutAttempt.bind(this),
            onPasteAttempt: this.onPasteAttempt.bind(this),
            onRightClick: this.onRightClick.bind(this),
            onKeyDown: this.onKeyDown.bind(this),
        };

        // Window focus/blur events (detects alt+tab, window switching, minimizing)
        if (this.config.features.tabSwitchDetection) {
            window.addEventListener('blur', this.boundHandlers.onWindowBlur);
            window.addEventListener('focus', this.boundHandlers.onWindowFocus);
            console.log('[AntiCheat] Listener attached: window blur/focus');
        }

        // Tab visibility API (detects tab switching, browser minimization)
        if (this.config.features.tabSwitchDetection) {
            document.addEventListener('visibilitychange', this.boundHandlers.onVisibilityChange);
            console.log('[AntiCheat] Listener attached: visibilitychange');
        }

        // Block copy, cut, and paste events
        if (this.config.features.copyPasteBlocking) {
            document.addEventListener('copy', this.boundHandlers.onCopyAttempt, true);
            document.addEventListener('cut', this.boundHandlers.onCutAttempt, true);
            document.addEventListener('paste', this.boundHandlers.onPasteAttempt, true);
            console.log('[AntiCheat] Listener attached: copy/cut/paste');
        }

        // Block right-click context menu
        if (this.config.features.rightClickBlocking) {
            document.addEventListener('contextmenu', this.boundHandlers.onRightClick, true);
            console.log('[AntiCheat] Listener attached: contextmenu');
        }

        // Block developer tool hotkeys and select all
        if (this.config.features.devtoolsHotkeyBlocking) {
            document.addEventListener('keydown', this.boundHandlers.onKeyDown, true);
            console.log('[AntiCheat] Listener attached: keydown');
        }
    }

    /**
     * Clean up all event listeners to prevent duplicates.
     */
    cleanupEventListeners() {
        if (!this.boundHandlers) {
            return;
        }

        if (this.config.features.tabSwitchDetection) {
            window.removeEventListener('blur', this.boundHandlers.onWindowBlur);
            window.removeEventListener('focus', this.boundHandlers.onWindowFocus);
            document.removeEventListener('visibilitychange', this.boundHandlers.onVisibilityChange);
        }

        if (this.config.features.copyPasteBlocking) {
            document.removeEventListener('copy', this.boundHandlers.onCopyAttempt, true);
            document.removeEventListener('cut', this.boundHandlers.onCutAttempt, true);
            document.removeEventListener('paste', this.boundHandlers.onPasteAttempt, true);
        }

        if (this.config.features.rightClickBlocking) {
            document.removeEventListener('contextmenu', this.boundHandlers.onRightClick, true);
        }

        if (this.config.features.devtoolsHotkeyBlocking) {
            document.removeEventListener('keydown', this.boundHandlers.onKeyDown, true);
        }

        this.boundHandlers = null;
    }

    /**
     * Handle window blur event (alt+tab, switching to other apps, minimizing).
     */
    onWindowBlur() {
        if (!this.config.enabled) {
            return; // Skip if disabled
        }

        // Ignore if page is unloading (refresh/reload) or within grace period
        if (this.isPageUnloading || (Date.now() - this.pageLoadedAt) < this.initialLoadGracePeriod) {
            return;
        }

        this.logEvent('window_blur', {
            timestamp: new Date().toISOString(),
            visibility_state: document.visibilityState || 'unknown',
        });

        // Track focus violation only if it's a real blur (not refresh)
        this.handleFocusViolation('window_blur');
    }

    /**
     * Handle window focus event (returning to exam window).
     */
    onWindowFocus() {
        this.logEvent('window_focus', {
            timestamp: new Date().toISOString(),
            visibility_state: document.visibilityState || 'unknown',
        });
    }

    /**
     * Handle visibility change (tab switching, browser minimization).
     */
    onVisibilityChange() {
        const visibilityState = document.visibilityState || 'unknown';
        const isHidden = document.hidden;

        if (isHidden) {
            if (!this.config.enabled) {
                // Still log but don't track violations in dev mode
                this.logEvent('tab_switch', {
                    timestamp: new Date().toISOString(),
                    visibility_state: visibilityState,
                    hidden: true,
                });
                return;
            }

            // Tab switched away or window minimized
            this.logEvent('tab_switch', {
                timestamp: new Date().toISOString(),
                visibility_state: visibilityState,
                hidden: true,
            });

            // Also log as window_hidden for consistency
            this.logEvent('window_hidden', {
                timestamp: new Date().toISOString(),
                visibility_state: visibilityState,
            });

            // Track focus violation
            this.handleFocusViolation('tab_switch');
        } else {
            // Visibility returned
            this.logEvent('visibility_return', {
                timestamp: new Date().toISOString(),
                visibility_state: visibilityState,
                hidden: false,
            });
        }
    }

    /**
     * Handle copy attempts - block and log.
     */
    onCopyAttempt(event) {
        if (!this.config.enabled) {
            return; // Allow in development mode
        }

        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        this.logEvent('copy_attempt', {
            timestamp: new Date().toISOString(),
            type: 'copy',
            target: event.target?.tagName || 'unknown',
        });
    }

    /**
     * Handle cut attempts - block and log.
     */
    onCutAttempt(event) {
        if (!this.config.enabled) {
            return; // Allow in development mode
        }

        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        this.logEvent('cut_attempt', {
            timestamp: new Date().toISOString(),
            type: 'cut',
            target: event.target?.tagName || 'unknown',
        });
    }

    /**
     * Handle paste attempts - block and log.
     */
    onPasteAttempt(event) {
        if (!this.config.enabled) {
            return; // Allow in development mode
        }

        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        this.logEvent('paste_attempt', {
            timestamp: new Date().toISOString(),
            type: 'paste',
            target: event.target?.tagName || 'unknown',
        });
    }

    /**
     * Handle right-click context menu - block and log.
     */
    onRightClick(event) {
        if (!this.config.enabled) {
            return; // Allow in development mode
        }

        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        this.logEvent('contextmenu_blocked', {
            timestamp: new Date().toISOString(),
            target: event.target?.tagName || 'unknown',
            clientX: event.clientX,
            clientY: event.clientY,
        });
    }

    /**
     * Handle all keyboard events - block forbidden hotkeys and select all.
     */
    onKeyDown(event) {
        if (!this.config.enabled) {
            return; // Allow in development mode
        }

        const keyCombo = this.getKeyCombo(event);
        const key = event.key;

        // List of forbidden hotkeys
        const forbiddenKeys = ['F12'];
        const forbiddenCombos = [
            'Ctrl+Shift+I',  // Inspect
            'Ctrl+Shift+C', // Element picker
            'Ctrl+Shift+J', // Console
            'Ctrl+U',       // View source
            'Ctrl+S',       // Save page
            'Ctrl+P',       // Print dialog
            'Ctrl+Shift+R', // Hard reload
            'Ctrl+A',       // Select all
        ];

        // Check if this is a forbidden key combination
        const isForbidden = forbiddenKeys.includes(key) || forbiddenCombos.includes(keyCombo);

        if (isForbidden) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            // Log the blocked hotkey
            this.logEvent('forbidden_hotkey', {
                timestamp: new Date().toISOString(),
                key: key,
                keyCombo: keyCombo,
                ctrlKey: event.ctrlKey,
                shiftKey: event.shiftKey,
                altKey: event.altKey,
                metaKey: event.metaKey,
            });

            // Special handling for Ctrl+A (select all)
            if (keyCombo === 'Ctrl+A' || (event.ctrlKey && (key.toLowerCase() === 'a'))) {
                this.logEvent('select_all_attempt', {
                    timestamp: new Date().toISOString(),
                    keyCombo: keyCombo,
                });
            }
        }
    }

    /**
     * Get a human-readable key combination string.
     */
    getKeyCombo(event) {
        const parts = [];
        if (event.ctrlKey) parts.push('Ctrl');
        if (event.shiftKey) parts.push('Shift');
        if (event.altKey) parts.push('Alt');
        if (event.metaKey) parts.push('Meta');
        if (event.key && !['Control', 'Shift', 'Alt', 'Meta'].includes(event.key)) {
            // Normalize letter keys to uppercase for consistency
            const key = event.key.length === 1 && event.key.match(/[a-z]/i) 
                ? event.key.toUpperCase() 
                : event.key;
            parts.push(key);
        }
        return parts.join('+');
    }

    /**
     * Handle focus violation (window blur or tab switch).
     * Logs the event only - no punishment or warnings.
     */
    handleFocusViolation(violationType) {
        if (!this.config.enabled || !this.config.features.focusLossViolations) {
            return; // Skip if disabled or feature is off
        }

        // Ignore violations during page unload or within grace period (refresh/reload)
        if (this.isPageUnloading || (Date.now() - this.pageLoadedAt) < this.initialLoadGracePeriod) {
            return;
        }

        // Log the event (logging only - no punishment)
        this.logEvent('focus_violation', {
            timestamp: new Date().toISOString(),
            violation_type: violationType,
            visibility_state: document.visibilityState || 'unknown',
        });
    }

    /**
     * Generic method to log any suspect event.
     */
    onSuspectEvent(eventType, extraData = {}) {
        this.logEvent(eventType, {
            ...extraData,
            timestamp: new Date().toISOString(),
        });
    }

    /**
     * Send event to backend for logging.
     */
    async logEvent(eventType, eventDetails = {}) {
        if (!this.config.enabled || !this.config.attemptId) {
            return;
        }

        const eventData = {
            exam_attempt_id: this.config.attemptId,
            event_type: eventType,
            event_details: eventDetails,
        };

        // Queue event if currently logging
        if (this.isLogging) {
            this.eventQueue.push(eventData);
            return;
        }

        this.isLogging = true;

        try {
            const response = await fetch(this.config.logEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(eventData),
            });

            if (!response.ok) {
                console.warn('[AntiCheat] Failed to log event:', eventType);
            }
        } catch (error) {
            console.error('[AntiCheat] Error logging event:', error);
        } finally {
            this.isLogging = false;

            // Process queued events
            if (this.eventQueue.length > 0) {
                const nextEvent = this.eventQueue.shift();
                this.logEvent(nextEvent.event_type, nextEvent.event_details);
            }
        }
    }

    /**
     * Cleanup - remove all event listeners.
     */
    destroy() {
        // Note: We don't actually remove listeners here as it's complex
        // and the page will be destroyed anyway. This is a placeholder
        // for future cleanup if needed.
        this.isInitialized = false;
        console.log('[AntiCheat] Destroyed');
    }
}

// Export for use in exam_take.blade.php
window.AntiCheatManager = AntiCheatManager;

