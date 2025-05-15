<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('admin');

require_once '../../controllers/admin/CategoryController.php';
$categoryController = new CategoryController();
$categories = $categoryController->getAllCategories();

include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <?php include_once '../shared/adminSidebar.php'; ?>
    <?php include_once '../shared/alerts.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Event Categories</h1>
                <p class="text-muted-foreground mt-2">Manage event categories and types.</p>
            </div>
            <button onclick="openCreateModal()" class="btn-primary px-4 py-2 rounded-md">Add Category</button>
        </div>

        <!-- Categories Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($categories as $category): ?>
                <div class="rounded-lg border bg-card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-primary/10 p-2">
                                <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold"><?php echo htmlspecialchars($category['name']); ?></h3>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($category)); ?>)" 
                                    class="text-sm text-muted-foreground hover:text-primary">Edit</button>
                            <button onclick="confirmDelete(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')" 
                                    class="text-sm text-red-600 hover:text-red-700">Delete</button>
                        </div>
                    </div>
                    <p class="text-sm text-muted-foreground"><?php echo htmlspecialchars($category['description']); ?></p>
                    <div class="mt-4">
                        <span class="text-xs text-muted-foreground"><?php echo $category['event_count']; ?> events</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Create/Edit Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 id="modalTitle" class="text-xl font-semibold mb-4">Add Category</h2>
            <form id="categoryForm" method="POST" action="../../controllers/admin/CategoryController.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="categoryId">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="name">Category Name</label>
                        <input type="text" id="name" name="name" required
                            class="w-full rounded-md border p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="description">Description</label>
                        <textarea id="description" name="description" rows="3" required
                            class="w-full rounded-md border p-2"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 border rounded-md hover:bg-gray-100">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Add Category';
            document.getElementById('formAction').value = 'create';
            document.getElementById('categoryId').value = '';
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('categoryModal').classList.remove('hidden');
            document.getElementById('categoryModal').classList.add('flex');
        }

        function openEditModal(category) {
            document.getElementById('modalTitle').textContent = 'Edit Category';
            document.getElementById('formAction').value = 'update';
            document.getElementById('categoryId').value = category.id;
            document.getElementById('name').value = category.name;
            document.getElementById('description').value = category.description;
            document.getElementById('categoryModal').classList.remove('hidden');
            document.getElementById('categoryModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('categoryModal').classList.add('hidden');
            document.getElementById('categoryModal').classList.remove('flex');
        }

        function confirmDelete(id, name) {
            if (confirm(`Are you sure you want to delete the category "${name}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../../controllers/admin/CategoryController.php';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;

                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 