<!-- Sidebar -->
<div class="w-64 border-r bg-background/95 backdrop-blur-sm h-screen flex flex-col fixed left-0 shadow-sm">
    <!-- Logo and Brand -->
    <div class="p-6 border-b bg-background">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2">
                <div class="rounded-lg bg-primary/10 p-2.5 flex items-center justify-center shadow-sm">
                    <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M15 6v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3V6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3"/>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg tracking-tight">Event Registration</span>
                    <span class="text-xs text-muted-foreground">Participant Portal</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto">
        <div class="space-y-4">
            <!-- Dashboard Section -->
            <a href="../participant/dashboard.php" 
                class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
                </svg>
                Dashboard
            </a>

            <!-- Events Section -->
            <div class="space-y-1">
                <h2 class="mb-2 px-2 text-xs font-semibold tracking-wider uppercase text-muted-foreground/70">Events</h2>
                <a href="../participant/browse-events.php" 
                    class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'browse-events.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Browse Events
                </a>

                <a href="../participant/my-registrations.php" 
                    class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'my-registrations.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    My Registrations
                </a>
            </div>

            <!-- Profile Section -->
            <div class="space-y-1">
                <h2 class="mb-2 px-2 text-xs font-semibold tracking-wider uppercase text-muted-foreground/70">Account</h2>
                <a href="../participant/profile.php" 
                    class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile Settings
                </a>
            </div>
        </div>
    </nav>

    <!-- User Menu -->
    <div class="border-t p-4 relative bg-background/50 backdrop-blur-sm">
        <button data-dropdown-toggle 
            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm text-muted-foreground w-full justify-between">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <?php echo isset($_SESSION['participant']['name']) ? htmlspecialchars($_SESSION['participant']['name']) : 'Participant'; ?>
            </div>
            <svg class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="absolute bottom-full left-0 w-full mb-1 hidden z-50">
            <div class="mx-4 rounded-lg border bg-background/95 backdrop-blur-sm shadow-lg">
                <a href="../../controllers/AuthController.php?action=logout" 
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm text-muted-foreground">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Update the margin div -->
<div class="ml-64 bg-background"></div>

<script>
const profileButton = document.querySelector('[data-dropdown-toggle]');
const dropdownMenu = document.getElementById('dropdownMenu');
const dropdownArrow = profileButton.querySelector('svg:last-child');

profileButton.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdownMenu.classList.toggle('hidden');
    dropdownArrow.style.transform = dropdownMenu.classList.contains('hidden') ? '' : 'rotate(180deg)';
});

document.addEventListener('click', () => {
    dropdownMenu.classList.add('hidden');
    dropdownArrow.style.transform = '';
});

dropdownMenu.addEventListener('click', (e) => {
    e.stopPropagation();
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        dropdownMenu.classList.add('hidden');
        dropdownArrow.style.transform = '';
    }
});
</script>

<style>
    .nav-link:hover svg {
        transform: scale(1.1);
        transition: transform 0.2s ease;
    }
    
    .nav-link.active {
        @apply bg-accent/90 text-accent-foreground shadow-sm;
    }
    
    .nav-link.active svg {
        @apply text-accent-foreground;
    }
</style> 