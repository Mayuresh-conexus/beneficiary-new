<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Access Denied | ImpactNexus</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
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
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-[#181111] dark:text-white antialiased">
<div class="relative flex h-screen w-full flex-col overflow-x-hidden">
    <!-- Top Navigation Bar -->
    <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-primary/10 bg-white/80 backdrop-blur-md px-6 py-3 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center size-10 rounded-lg bg-primary/10 text-primary">
                <span class="material-symbols-outlined text-3xl">shield_person</span>
            </div>
            <div class="flex flex-col">
                <h2 class="text-[#181111] text-base font-bold leading-tight tracking-tight">ImpactNexus</h2>
                <span class="text-xs text-[#181111]/60 font-medium uppercase tracking-wider">Enterprise Management</span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-[#181111]">{{ auth()->user()->name ?? 'Guest User' }}</p>
                    <p class="text-xs text-primary font-medium capitalize">{{ str_replace('_', ' ', auth()->user()->role ?? 'visitor') }}</p>
                </div>
                <div class="size-10 rounded-full border-2 border-primary/20 bg-cover bg-center flex items-center justify-center bg-slate-100">
                    <span class="material-symbols-outlined text-primary">person</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content: Access Denied Screen -->
    <main class="flex-1 flex flex-col items-center justify-center px-6 py-12">
        <div class="max-w-[560px] w-full text-center space-y-8">
            <!-- Security Illustration Container -->
            <div class="relative inline-block">
                <div class="absolute inset-0 bg-primary/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="relative flex items-center justify-center size-48 rounded-full bg-white shadow-xl shadow-primary/5 border border-primary/5">
                    <div class="flex items-center justify-center size-32 rounded-full bg-primary/5">
                        <span class="material-symbols-outlined text-8xl text-primary font-light">lock_person</span>
                    </div>
                    <!-- Status Badge -->
                    <div class="absolute bottom-4 right-4 bg-primary text-white p-2 rounded-lg shadow-lg flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">gpp_maybe</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest leading-none">Status 403</span>
                    </div>
                </div>
            </div>

            <!-- Text Content -->
            <div class="space-y-4">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-[#181111] tracking-tight">Access Denied</h1>
                <div class="h-1 w-20 bg-primary mx-auto rounded-full"></div>
                <p class="text-lg font-semibold text-[#181111] leading-relaxed max-w-md mx-auto">
                    You do not have permission to view this module.
                </p>
                <p class="text-sm text-[#181111]/60 leading-relaxed max-w-sm mx-auto">
                    Your current role (<span class="font-bold text-[#181111]">{{ ucwords(str_replace('_', ' ', auth()->user()->role ?? 'unassigned')) }}</span>) does not have the necessary permissions to access this feature.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 min-w-[200px] h-14 bg-primary text-white rounded-xl font-bold text-base hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 active:scale-95">
                    <span class="material-symbols-outlined text-xl">dashboard</span>
                    <span>Go to Dashboard</span>
                </a>
                <a href="mailto:admin@example.com" class="flex items-center justify-center gap-2 min-w-[200px] h-14 bg-white border-2 border-primary/10 text-[#181111] rounded-xl font-bold text-base hover:bg-primary/5 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-xl">support_agent</span>
                    <span>Contact Admin</span>
                </a>
            </div>

            <!-- Debug/Footer Info -->
            <div class="pt-12">
                <div class="inline-flex items-center gap-4 px-4 py-2 bg-primary/5 rounded-full text-[10px] font-medium text-[#181111]/40 uppercase tracking-[0.2em]">
                    <span>REQ_REF: #NX-SEC-{{ strtoupper(auth()->user()->id ?? 'GUEST') }}</span>
                    <span class="w-1 h-1 bg-primary/20 rounded-full"></span>
                    <span>ROLE_UID: {{ strtoupper(auth()->user()->role ?? 'NULL') }}</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Minimalist Footer -->
    <footer class="p-6 text-center border-t border-primary/5 bg-white/50">
        <p class="text-xs text-[#181111]/40 font-medium">
            © {{ date('Y') }} ImpactNexus Enterprise Management. All rights reserved.
        </p>
    </footer>
</div>
</body>
</html>
