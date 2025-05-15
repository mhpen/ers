<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: adminLogin.php');
    exit();
}

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/adminSidebar.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight">Settings</h1>
            <p class="text-muted-foreground mt-2">Manage system settings and configurations.</p>
        </div>

        <!-- Settings Sections -->
        <div class="grid gap-6">
            <!-- General Settings -->
            <div class="rounded-lg border bg-card p-6">
                <h2 class="text-xl font-semibold mb-6">General Settings</h2>
                <form class="space-y-4">
                    <div class="grid gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Site Name</label>
                            <input type="text" value="Event Registration System" 
                                   class="w-full rounded-md border px-3 py-2">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Admin Email</label>
                            <input type="email" value="admin@example.com" 
                                   class="w-full rounded-md border px-3 py-2">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Timezone</label>
                            <select class="w-full rounded-md border px-3 py-2">
                                <option>UTC</option>
                                <option>America/New_York</option>
                                <option>Europe/London</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Email Settings -->
            <div class="rounded-lg border bg-card p-6">
                <h2 class="text-xl font-semibold mb-6">Email Settings</h2>
                <form class="space-y-4">
                    <div class="grid gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">SMTP Host</label>
                            <input type="text" placeholder="smtp.example.com" 
                                   class="w-full rounded-md border px-3 py-2">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">SMTP Port</label>
                            <input type="number" placeholder="587" 
                                   class="w-full rounded-md border px-3 py-2">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">SMTP Username</label>
                            <input type="text" placeholder="username" 
                                   class="w-full rounded-md border px-3 py-2">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">SMTP Password</label>
                            <input type="password" placeholder="••••••••" 
                                   class="w-full rounded-md border px-3 py-2">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Save Changes -->
            <div class="flex justify-end">
                <button class="btn-primary px-4 py-2 rounded-md">Save Changes</button>
            </div>
        </div>
    </main>
</body>
</html> 