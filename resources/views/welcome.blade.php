<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'CRM Port') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 text-slate-900 dark:text-slate-50 min-h-screen">

        <!-- Hero Section -->
        <div class="flex items-center justify-center min-h-[calc(100vh-80px)] px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl w-full text-center space-y-8">
                <!-- Header -->
                <div class="space-y-4">
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight">
                        SoloStream CRM
                    </h1>
                    <p class="text-xl sm:text-2xl text-slate-600 dark:text-slate-300 font-light">
                        Professional CRM for Modern Teams
                    </p>
                </div>

                <!-- Description -->
                <div class="space-y-6">
                    <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed mx-auto">
                        Manage clients, projects, documents, and team activities in one unified dashboard. 
                        Built with Laravel and Filament for maximum efficiency.
                    </p>

                    <!-- Features Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                        <div class="flex flex-col items-center gap-2 p-4 rounded-lg bg-white/40 dark:bg-slate-800/40 backdrop-blur border border-slate-200 dark:border-slate-700">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 4H9m6 16H9m6-4a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="font-medium text-sm">Client Management</span>
                        </div>

                        <div class="flex flex-col items-center gap-2 p-4 rounded-lg bg-white/40 dark:bg-slate-800/40 backdrop-blur border border-slate-200 dark:border-slate-700">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span class="font-medium text-sm">Project Tracking</span>
                        </div>

                        <div class="flex flex-col items-center gap-2 p-4 rounded-lg bg-white/40 dark:bg-slate-800/40 backdrop-blur border border-slate-200 dark:border-slate-700">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="font-medium text-sm">Activity Logs</span>
                        </div>
                    </div>
                </div>

                <!-- CTA Button -->
                <div class="pt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ url('/admin') }}" class="inline-block px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                        Enter CRM Dashboard →
                    </a>
                </div>

                <!-- Footer -->
                <div class="pt-12 border-t border-slate-300 dark:border-slate-700">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Demo Admin Account: <code class="bg-slate-200 dark:bg-slate-800 px-2 py-1 rounded text-xs">admin@solostream.lv</code> / <code class="bg-slate-200 dark:bg-slate-800 px-2 py-1 rounded text-xs">password123</code>
                    </p>
                    <br>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Demo User Account: <code class="bg-slate-200 dark:bg-slate-800 px-2 py-1 rounded text-xs">maris@solostream.lv</code> / <code class="bg-slate-200 dark:bg-slate-800 px-2 py-1 rounded text-xs">password123</code>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
