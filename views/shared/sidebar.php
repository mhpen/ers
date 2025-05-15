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
                    <span class="text-xs text-muted-foreground">System</span>
                </div>
            </div>
            
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto">
        <div class="space-y-4">
            <!-- Dashboard Section -->
            <a href="../admin/dashboard.php" 
                class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            <!-- Event Management Section -->
            <div class="space-y-1">
                <h2 class="mb-2 px-2 text-xs font-semibold tracking-wider uppercase text-muted-foreground/70">Event Management</h2>
                <a href="../admin/events.php" 
                    class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Events
                </a>

                <a href="../admin/participants.php" 
                    class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'participants.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Participants
                </a>
            </div>

            <!-- Attendance Section -->
            <div class="space-y-1">
                <h2 class="mb-2 px-2 text-xs font-semibold tracking-wider uppercase text-muted-foreground/70">Attendance</h2>
                <a href="../admin/attendance.php" 
                    class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Attendance
                </a>

                <a href="../admin/checkin.php" 
                    class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'checkin.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Check-in
                </a>
            </div>

            <!-- System Management Section -->
            <div class="space-y-1">
                <h2 class="mb-2 px-2 text-xs font-semibold tracking-wider uppercase text-muted-foreground/70">System Management</h2>
                <a href="../admin/users.php" 
                    class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    User Management
                </a>
                <a href="../admin/access.php" 
                    class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'access.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Access Control
                </a>
                <a href="../admin/activity.php" 
                    class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm <?php echo basename($_SERVER['PHP_SELF']) == 'activity.php' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground'; ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Activity Log
                </a>
            </div>
        </div>
    </nav>

    <!-- User Menu -->
    <div class="border-t p-4 relative bg-background/50 backdrop-blur-sm">
        <!-- Profile Button -->
        <button data-dropdown-toggle 
            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm text-muted-foreground w-full justify-between">
            <div class="flex items-center gap-3">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Admin
            </div>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="absolute bottom-full left-0 w-full mb-1 hidden z-50">
            <div class="mx-4 rounded-lg border bg-background/95 backdrop-blur-sm shadow-lg">
                <a href="../../controllers/admin/logout.php" 
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all hover:bg-accent/50 hover:text-accent-foreground hover:shadow-sm text-muted-foreground">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
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
// Get the profile button and dropdown menu
const profileButton = document.querySelector('[data-dropdown-toggle]');
const dropdownMenu = document.getElementById('dropdownMenu');

// Toggle dropdown when clicking the profile button
profileButton.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdownMenu.classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', () => {
    dropdownMenu.classList.add('hidden');
});

// Prevent dropdown from closing when clicking inside it
dropdownMenu.addEventListener('click', (e) => {
    e.stopPropagation();
});

// Close dropdown when pressing escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        dropdownMenu.classList.add('hidden');
    }
});
</script>

<!-- Add subtle hover effect to all SVG icons -->
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