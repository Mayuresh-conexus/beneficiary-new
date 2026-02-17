<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
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
                        "primary": "#1e1e8a",
                        "background-light": "#f6f6f8",
                        "background-dark": "#121220",
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
                    borderRadius: {"DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px"},
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display min-h-screen flex items-center justify-center p-4">
    <div class="max-w-5xl w-full bg-white dark:bg-slate-900 rounded-xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[650px]">
        <!-- Left Side: Branding -->
        <div class="hidden md:flex md:w-1/2 bg-primary p-12 flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-8">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <span class="material-icons text-primary text-2xl">public</span>
                    </div>
                    <span class="text-white text-2xl font-bold tracking-tight">ImpactNexus</span>
                </div>
                <!-- Content ... -->
            </div>
            <!-- More content similar to storage but simplified for speed -->
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
                            <a class="text-xs font-bold text-primary hover:underline" href="#">Forgot Password?</a>
                        </div>
                        <div class="relative">
                            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">lock</span>
                            <input class="w-full pl-10 pr-10 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none text-slate-900 dark:text-white" id="password" name="password" placeholder="••••••••" required type="password"/>
                            <button class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg hover:text-slate-600" type="button">visibility</button>
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
                        Having trouble logging in? <a class="text-primary font-bold hover:underline" href="#">Contact Support</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
