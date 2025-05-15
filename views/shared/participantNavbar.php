<?php
if (!isset($_SESSION['participant'])) {
    header('Location: ../participant/participantLogin.php');
    exit();
}
?>

<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side -->
            <div class="flex">
                <!-- Logo/Brand -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="participantPage.php" class="flex items-center">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="ml-2 text-xl font-semibold text-gray-900">EventHub</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="participantPage.php" 
                       class="<?php echo basename($_SERVER['PHP_SELF']) === 'participantPage.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Browse Events
                    </a>
                    <a href="my-registrations.php" 
                       class="<?php echo basename($_SERVER['PHP_SELF']) === 'my-registrations.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        My Registrations
                    </a>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center">
                <!-- Notifications -->
                <button type="button" 
                        class="ml-3 p-2 rounded-full text-gray-500 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="sr-only">View notifications</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>

                <!-- Profile Dropdown -->
                <div class="ml-3 relative">
                    <div>
                        <button type="button" 
                                class="flex items-center max-w-xs rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                id="user-menu-button" 
                                aria-expanded="false" 
                                aria-haspopup="true">
                            <span class="sr-only">Open user menu</span>
                            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                <?php echo strtoupper(substr($_SESSION['participant']['name'], 0, 1)); ?>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700">
                                <?php echo htmlspecialchars($_SESSION['participant']['name']); ?>
                            </span>
                            <svg class="ml-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Dropdown menu -->
                    <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" 
                         role="menu" 
                         aria-orientation="vertical" 
                         aria-labelledby="user-menu-button" 
                         tabindex="-1"
                         id="user-menu-dropdown">
                        <a href="profile.php" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                           role="menuitem">
                            Your Profile
                        </a>
                        <a href="settings.php" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                           role="menuitem">
                            Settings
                        </a>
                        <a href="../../controllers/participant/authController.php?action=logout" 
                           class="block px-4 py-2 text-sm text-red-700 hover:bg-gray-100" 
                           role="menuitem">
                            Sign out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state -->
    <div class="sm:hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="participantPage.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) === 'participantPage.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Browse Events
            </a>
            <a href="my-registrations.php" 
               class="<?php echo basename($_SERVER['PHP_SELF']) === 'my-registrations.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                My Registrations
            </a>
        </div>
    </div>
</nav>

<script>
// Toggle user menu
const userMenuButton = document.getElementById('user-menu-button');
const userMenuDropdown = document.getElementById('user-menu-dropdown');

userMenuButton.addEventListener('click', () => {
    userMenuDropdown.classList.toggle('hidden');
});

// Close dropdown when clicking outside
document.addEventListener('click', (event) => {
    if (!userMenuButton.contains(event.target) && !userMenuDropdown.contains(event.target)) {
        userMenuDropdown.classList.add('hidden');
    }
});
</script> 