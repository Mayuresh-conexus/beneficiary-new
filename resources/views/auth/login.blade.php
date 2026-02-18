<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Enterprise Beneficiary Management SaaS - Login</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#333", // Darker primary
                        "background-light": "#f6f6f8",
                        "background-dark": "#121220",
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
                },
            },
        }
    </script>
    <style>
        /* Shine Effect */
        .shining-text {
            background: linear-gradient(90deg, #94a3b8 0%, #ffffff 40%, #ffffff 50%, #ffffff 60%, #94a3b8 100%);
            background-size: 200% auto;
            color: #fff;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shine 3s linear infinite;
            font-weight: 800;
        }

        @keyframes shine {
            to { background-position: 200% center; }
        }
        
        /* Box Shine */
        .shining-box {
            position: relative;
            overflow: hidden;
        }
        .shining-box::after {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 150%;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.1) 50%, transparent 100%);
            transform: skewX(-25deg);
            animation: boxShine 6s infinite;
            pointer-events: none;
        }
        
        @keyframes boxShine {
            0% { left: -150%; }
            20% { left: 150%; }
            100% { left: 150%; }
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display min-h-screen flex items-center justify-center p-4">
    <div class="max-w-5xl w-full bg-white dark:bg-slate-900 rounded-xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[650px]">
        <!-- Left Side: Branding -->
        <!-- Left Side: Branding -->
        <div class="hidden md:flex md:w-1/2 bg-[#171719] p-12 flex-col justify-center items-center relative overflow-hidden">
            <!-- Background Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#171719] via-[#1e1e24] to-[#0f172a] z-0"></div>
            
            <!-- Animated decorative elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-20 pointer-events-none">
                <div class="absolute -top-[20%] -left-[20%] w-[70%] h-[70%] bg-blue-500/20 rounded-full blur-[100px] animate-pulse"></div>
                <div class="absolute top-[40%] -right-[20%] w-[60%] h-[60%] bg-indigo-500/20 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s"></div>
            </div>

            <!-- Main Content -->
            <div class="relative z-10 flex flex-col items-center">
                <!-- Glass Box -->
                <div class="shining-box rounded-2xl border border-white/10 bg-white/5 backdrop-blur-xl p-4 flex flex-col items-center gap-4 shadow-2xl mb-6">
                    <!-- App Logo Container -->
                    <div class="bg-white p-4 rounded-xl shadow-lg ring-1 ring-black/5 transform transition-transform hover:scale-105 duration-500">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto object-contain">
                    </div>
                    
                    <!-- App Text -->
                    <div class="text-center">
                        <h1 class="text-sm font-bold uppercase tracking-[0.2em] shining-text leading-tight opacity-90 mb-2">Beneficiary App</h1>
                        <p class="text-slate-400 text-xs font-light tracking-wider">Enterprise Management Portal</p>
                    </div>
                </div>

                <!-- Features List -->
                <div class="space-y-3 w-full max-w-xs transition-all duration-700 delay-100">
                    <div class="flex items-center gap-3 text-slate-300 bg-white/5 p-2 rounded-lg border border-white/5 backdrop-blur-sm hover:bg-white/10 transition-colors">
                        <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                            <span class="material-icons text-emerald-400 text-sm">check</span>
                        </div>
                        <span class="text-xs font-medium tracking-wide">Beneficiary verification in seconds</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-300 bg-white/5 p-2 rounded-lg border border-white/5 backdrop-blur-sm hover:bg-white/10 transition-colors">
                        <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center shrink-0">
                            <span class="material-icons text-blue-400 text-sm">analytics</span>
                        </div>
                        <span class="text-xs font-medium tracking-wide">Live impact analytics dashboard</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-300 bg-white/5 p-2 rounded-lg border border-white/5 backdrop-blur-sm hover:bg-white/10 transition-colors">
                        <div class="w-6 h-6 rounded-full bg-purple-500/20 flex items-center justify-center shrink-0">
                            <span class="material-icons text-purple-400 text-sm">hub</span>
                        </div>
                        <span class="text-xs font-medium tracking-wide">Centralized program management</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-300 bg-white/5 p-2 rounded-lg border border-white/5 backdrop-blur-sm hover:bg-white/10 transition-colors">
                        <div class="w-6 h-6 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0">
                            <span class="material-icons text-amber-400 text-sm">inventory_2</span>
                        </div>
                        <span class="text-xs font-medium tracking-wide">Real-time inventory tracking</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-300 bg-white/5 p-2 rounded-lg border border-white/5 backdrop-blur-sm hover:bg-white/10 transition-colors">
                        <div class="w-6 h-6 rounded-full bg-rose-500/20 flex items-center justify-center shrink-0">
                            <span class="material-icons text-rose-400 text-sm">download</span>
                        </div>
                        <span class="text-xs font-medium tracking-wide">Instant report exports</span>
                    </div>
                </div>
            </div>
            
            <!-- Footer Text -->
            <div class="absolute bottom-8 left-0 w-full text-center z-10">
                <p class="text-slate-500 text-xs tracking-widest uppercase opacity-50">Secure Access</p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            <div class="max-w-md mx-auto w-full">
                <!-- Mobile Logo -->
                <div class="flex md:hidden items-center gap-2 mb-8 justify-center">
                    <div class="w-8 h-8 bg-primary rounded flex items-center justify-center">
                        <span class="material-icons text-white text-lg">public</span>
                    </div>
                    <span class="text-slate-900 dark:text-white text-xl font-bold">ImpactNexus</span>
                </div>
                
                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Welcome Back</h2>
                    <p class="text-slate-500 dark:text-slate-400">Please enter your credentials to access your portal.</p>
                </div>

                @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded mb-6 text-sm">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2" for="email">Email Address</label>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">alternate_email</span>
                            <input class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none text-slate-900 dark:text-white" id="email" name="email" placeholder="name@organization.org" required type="email" value="{{ old('email') }}"/>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="password">Password</label>
                        </div>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">lock</span>
                            <input class="w-full pl-10 pr-10 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none text-slate-900 dark:text-white" id="password" name="password" placeholder="••••••••" required type="password"/>
                            <button class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg hover:text-slate-600" type="button" onclick="togglePasswordVisibility(this)">visibility</button>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <input class="w-4 h-4 text-primary bg-slate-100 border-slate-300 rounded focus:ring-primary" id="remember" name="remember" type="checkbox"/>
                        <label class="ml-2 text-sm text-slate-600 dark:text-slate-400" for="remember">Keep me logged in for 30 days</label>
                    </div>
                    <button class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-lg shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2 group" type="submit">
                        Sign In to Portal
                        <span class="material-icons text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </form>

                <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                        Having trouble logging in? <a class="text-primary font-bold hover:underline" href="mailto:mayuresh.mhatre@conexus-ns.com">Contact Support</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePasswordVisibility(button) {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                button.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                button.textContent = 'visibility';
            }
        }
    </script>
</body>
</html>
