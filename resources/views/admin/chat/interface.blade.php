<div id="admin-chat-widget" class="fixed bottom-6 right-6 z-50 font-sans">
    <!-- Chat Trigger Button -->
    <button id="chat-trigger" 
        class="group flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-full shadow-2xl hover:shadow-[0_0_40px_rgba(168,85,247,0.4)] transform hover:-translate-y-1 transition-all duration-300 focus:outline-none ring-4 ring-white/30 dark:ring-gray-800/30">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white group-hover:rotate-12 transition-transform duration-300 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
        <span class="absolute top-0 right-0 w-4 h-4 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></span>
    </button>

    <!-- Chat Interface -->
    <div id="chat-window" 
        class="hidden flex flex-col w-[400px] h-[36rem] max-h-[85vh] bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-white/20 dark:border-gray-700/50 overflow-hidden transform origin-bottom-right transition-all duration-500 opacity-0 scale-95 mt-4">
        
        <!-- Header -->
        <div class="relative px-6 py-5 bg-gradient-to-r from-indigo-600 to-purple-600 overflow-hidden">
            <!-- Decorative shapes -->
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 rounded-full bg-white/10 blur-xl"></div>
            <div class="absolute bottom-0 left-0 -ml-4 -mb-4 w-16 h-16 rounded-full bg-black/10 blur-lg"></div>
            
            <div class="relative flex items-center justify-between z-10">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm shadow-inner border border-white/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-green-400 border-2 border-purple-600"></div>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-base tracking-wide">CoNex AI Admin</h3>
                        <p class="text-indigo-100 text-xs font-medium opacity-80 flex items-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 mr-1.5 animate-pulse"></span>
                            Online & Ready
                        </p>
                    </div>
                </div>
                <button id="close-chat" class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-full transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-6 scroll-smooth custom-scrollbar">
            <!-- Welcome Message -->
            <div class="flex justify-start animate-slide-in-left">
                <div class="flex items-end space-x-2 w-full">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center flex-shrink-0 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-bl-sm px-5 py-3.5 shadow-sm border border-gray-200/50 dark:border-gray-700 max-w-[85%]">
                        <p class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed font-medium">
                            Welcome back, Admin! 👋<br><br>
                            I'm here to help you manage beneficiaries, track projects, and monitor volunteer activities. What do you need today?
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white/50 dark:bg-gray-900/50 backdrop-blur-md border-t border-gray-100 dark:border-gray-800">
            <form id="chat-form" class="relative group">
                <input type="text" id="chat-input" 
                    placeholder="Ask me anything..." 
                    class="w-full bg-gray-100/80 dark:bg-gray-800/80 text-gray-800 dark:text-gray-100 border focus:border-purple-500 dark:border-gray-700 rounded-2xl pl-5 pr-14 py-4 focus:ring-4 focus:ring-purple-500/10 transition-all duration-300 text-sm shadow-inner outline-none"
                    autocomplete="off">
                <button type="submit" 
                    class="absolute text-center right-2 top-1/2 transform -translate-y-1/2 p-2.5 bg-gradient-to-r from-purple-500 to-indigo-500 text-white rounded-xl shadow-md hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </form>
           
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #475569;
    }

    /* Animations */
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes typingDots {
        0%, 100% { transform: translateY(0); opacity: 0.5; }
        50% { transform: translateY(-4px); opacity: 1; }
    }

    .animate-slide-in-right { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-slide-in-left { animation: slideInLeft 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    .typing-dot {
        animation: typingDots 1.4s infinite ease-in-out;
    }
    .typing-dot:nth-child(1) { animation-delay: 0s; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    /* Markdown Styling */
    .prose-content {
        font-size: 0.875rem;
        line-height: 1.6;
    }
    .prose-content p { margin-bottom: 0.75em; }
    .prose-content p:last-child { margin-bottom: 0; }
    .prose-content ul { list-style-type: none; padding-left: 0.5rem; margin-bottom: 0.75em; }
    .prose-content li { position: relative; padding-left: 1.25rem; margin-bottom: 0.25em; }
    .prose-content li::before {
        content: "•";
        color: #8b5cf6;
        font-weight: bold;
        position: absolute;
        left: 0;
    }
    .prose-content strong { font-weight: 700; color: inherit; }
    .prose-content h3, .prose-content h4 { font-weight: 700; margin-top: 1em; margin-bottom: 0.5em; }
    .prose-content code { background: rgba(0,0,0,0.05); padding: 0.1em 0.3em; border-radius: 0.25rem; font-family: monospace; }
    .dark .prose-content code { background: rgba(255,255,255,0.1); }
</style>

<!-- Load Marked.js for true Markdown parsing -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const triggerDiv = document.getElementById('chat-trigger');
        const windowDiv = document.getElementById('chat-window');
        const closeBtn = document.getElementById('close-chat');
        const form = document.getElementById('chat-form');
        const input = document.getElementById('chat-input');
        const messages = document.getElementById('chat-messages');
        const submitBtn = form.querySelector('button');

        let isOpen = false;

        marked.setOptions({
            breaks: true,
            gfm: true
        });

        // Toggle Chat
        function toggleChat() {
            isOpen = !isOpen;
            if (isOpen) {
                windowDiv.classList.remove('hidden');
                setTimeout(() => {
                    windowDiv.classList.remove('opacity-0', 'scale-95');
                    triggerDiv.classList.add('opacity-0', 'scale-75'); 
                    setTimeout(() => triggerDiv.classList.add('hidden'), 300);
                    input.focus();
                }, 10);
            } else {
                windowDiv.classList.add('opacity-0', 'scale-95');
                triggerDiv.classList.remove('hidden');
                setTimeout(() => {
                    triggerDiv.classList.remove('opacity-0', 'scale-75');
                }, 50);
                setTimeout(() => {
                    windowDiv.classList.add('hidden');
                }, 500);
            }
        }

        triggerDiv.addEventListener('click', toggleChat);
        closeBtn.addEventListener('click', toggleChat);

        // Append Message function
        function appendMessage(text, isUser = false) {
            const wrapper = document.createElement('div');
            wrapper.className = `flex ${isUser ? 'justify-end animate-slide-in-right' : 'justify-start animate-slide-in-left'}`;
            
            let bubbleHtml = '';

            if (isUser) {
                // User Bubble
                bubbleHtml = `
                    <div class="bg-gradient-to-tr from-purple-500 to-indigo-600 text-white rounded-2xl rounded-br-sm px-5 py-3.5 shadow-md max-w-[85%]">
                        <p class="text-sm font-medium leading-relaxed">${escapeHtml(text)}</p>
                    </div>
                `;
            } else {
                // Bot Bubble + Avatar
                bubbleHtml = `
                    <div class="flex items-end space-x-2 w-full">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center flex-shrink-0 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-bl-sm px-5 py-3.5 shadow-sm border border-gray-200/50 dark:border-gray-700 max-w-[85%]">
                            <div class="text-gray-800 dark:text-gray-200 prose-content">${marked.parse(text)}</div>
                        </div>
                    </div>
                `;
            }

            wrapper.innerHTML = bubbleHtml;
            messages.appendChild(wrapper);
            scrollToBottom();
        }

        // Typing Indicator
        function showTyping() {
            const id = 'typing-' + Date.now();
            const wrapper = document.createElement('div');
            wrapper.id = id;
            wrapper.className = 'flex justify-start animate-slide-in-left';
            wrapper.innerHTML = `
                <div class="flex items-end space-x-2 w-full">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center flex-shrink-0 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl rounded-bl-sm px-5 py-4 shadow-sm border border-gray-200/50 dark:border-gray-700">
                        <div class="flex space-x-1.5 items-center justify-center h-2">
                            <div class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full typing-dot"></div>
                            <div class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full typing-dot"></div>
                            <div class="w-1.5 h-1.5 bg-gray-400 dark:bg-gray-500 rounded-full typing-dot"></div>
                        </div>
                    </div>
                </div>
            `;
            messages.appendChild(wrapper);
            scrollToBottom();
            return id;
        }

        function removeTyping(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function scrollToBottom() {
            messages.scrollTo({
                top: messages.scrollHeight,
                behavior: 'smooth'
            });
        }

        function escapeHtml(unsafe) {
            return unsafe
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }

        // Handle Submit
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;

            appendMessage(message, true);
            input.value = '';
            input.disabled = true;
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            
            const typingId = showTyping();

            try {
                const response = await fetch('{{ route("admin.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                removeTyping(typingId);

                if (response.ok) {
                    appendMessage(data.message || 'No response received.');
                } else {
                    appendMessage('⚠️ Error: ' + (data.error || response.statusText));
                }
            } catch (error) {
                removeTyping(typingId);
                appendMessage('⚠️ Network error. Please check your connection and try again.');
                console.error(error);
            } finally {
                input.disabled = false;
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                input.focus();
            }
        });
    });
</script>
