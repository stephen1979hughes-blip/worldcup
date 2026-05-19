<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WorldCupDB') — Every World Cup, Every Player</title>
    <meta name="description" content="@yield('meta_description', 'Browse squads, groups, scorers and records from all 22 FIFA World Cup tournaments, 1930–2022.')">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { DEFAULT: '#16a34a', light: '#dcfce7', dark: '#14532d' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-green-600 rounded-md flex items-center justify-center">
                        <i class="ti ti-trophy text-white text-sm"></i>
                    </div>
                    <span class="font-semibold text-gray-900">world<span class="text-green-600">cup</span>db</span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                       class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('home') ? 'bg-gray-100 font-medium text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        Home
                    </a>
                    <a href="{{ route('tournaments.index') }}"
                       class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('tournaments.*') ? 'bg-gray-100 font-medium text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        Tournaments
                    </a>
                    <a href="{{ route('players.index') }}"
                       class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('players.*') ? 'bg-gray-100 font-medium text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        Players
                    </a>
                    <a href="{{ route('teams.index') }}"
                       class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('teams.*') ? 'bg-gray-100 font-medium text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        Teams
                    </a>
                    <a href="{{ route('matches.index') }}"
                       class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('matches.index') ? 'bg-gray-100 font-medium text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        Matches
                    </a>
                    <a href="{{ route('records') }}"
                       class="px-3 py-1.5 rounded-md text-sm {{ request()->routeIs('records') ? 'bg-gray-100 font-medium text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                        Records
                    </a>
                </div>

                {{-- Gender switcher --}}
                <div class="flex items-center bg-gray-100 rounded-lg p-0.5 text-sm mr-2">
                    <a href="{{ route('gender.switch', 'men') }}"
                       class="px-3 py-1 rounded-md font-medium transition-colors {{ $activeGender === 'men' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Men
                    </a>
                    <a href="{{ route('gender.switch', 'women') }}"
                       class="px-3 py-1 rounded-md font-medium transition-colors {{ $activeGender === 'women' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Women
                    </a>
                </div>

                <div class="relative">
                    <input type="text" id="global-search" placeholder="Search players..."
                           autocomplete="off"
                           class="w-52 pl-8 pr-3 py-1.5 text-sm border border-gray-200 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <i class="ti ti-search absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <div id="search-results" class="hidden absolute right-0 mt-1 w-72 bg-white border border-gray-200 rounded-lg shadow-lg z-50"></div>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 mt-16 py-8 text-center text-xs text-gray-400">
        <p>Data: <a href="https://github.com/jfjelstul/worldcup" class="underline" target="_blank" rel="noopener">Fjelstul World Cup Database</a>
        © 2023 Joshua C. Fjelstul, Ph.D., licensed under <a href="https://creativecommons.org/licenses/by-sa/4.0/" class="underline" target="_blank" rel="noopener">CC BY-SA 4.0</a>.</p>
    </footer>

    <script>
        const searchInput = document.getElementById('global-search');
        const searchResults = document.getElementById('search-results');
        let searchTimeout;

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            if (q.length < 2) { searchResults.classList.add('hidden'); return; }
            searchTimeout = setTimeout(async () => {
                const res = await fetch(`/search?q=${encodeURIComponent(q)}`);
                const data = await res.json();
                if (!data.length) { searchResults.classList.add('hidden'); return; }
                searchResults.innerHTML = data.map(p => `
                    <a href="${p.url}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                        <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center text-xs font-medium text-green-800 flex-shrink-0">
                            ${p.name.split(' ').map(w => w[0]).slice(0,2).join('')}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">${p.name}</p>
                            <p class="text-xs text-gray-500">${p.team}</p>
                        </div>
                    </a>
                `).join('');
                searchResults.classList.remove('hidden');
            }, 250);
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target)) searchResults.classList.add('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
