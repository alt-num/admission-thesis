<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2" style="display: none;"></div>

<script>
    // Toast system
    (function() {
        const toastContainer = document.getElementById('toast-container');
        
        window.showToast = function(type, message) {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' 
                ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
                : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            
            toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-[300px] max-w-md transform transition-all duration-300 ease-in-out opacity-0 translate-x-full`;
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex-1 text-sm font-medium';
            messageDiv.textContent = message;
            
            const iconDiv = document.createElement('div');
            iconDiv.className = 'flex-shrink-0';
            iconDiv.innerHTML = icon;
            
            const closeButton = document.createElement('button');
            closeButton.className = 'flex-shrink-0 text-white hover:text-gray-200 focus:outline-none';
            closeButton.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            closeButton.onclick = function() { toast.remove(); if (toastContainer.children.length === 0) toastContainer.style.display = 'none'; };
            
            toast.appendChild(iconDiv);
            toast.appendChild(messageDiv);
            toast.appendChild(closeButton);
            
            toastContainer.appendChild(toast);
            toastContainer.style.display = 'block';
            
            // Trigger animation
            setTimeout(() => {
                toast.classList.remove('opacity-0', 'translate-x-full');
            }, 10);
            
            // Auto-hide after 4 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                    if (toastContainer.children.length === 0) {
                        toastContainer.style.display = 'none';
                    }
                }, 300);
            }, 4000);
        };
        
        // Auto-show session messages
        @if(session('success'))
            showToast('success', '{{ session('success') }}');
        @endif
        
        @if(session('error'))
            showToast('error', '{{ session('error') }}');
        @endif
    })();
</script>

