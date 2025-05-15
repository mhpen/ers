<?php
require_once '../../helpers/SessionHelper.php';
SessionHelper::requireLogin('admin');

require_once '../../config/config.php';
require_once '../../models/Category.php';
require_once '../../models/ActivityLogger.php';

class CategoryController {
    private $conn;
    private $categoryModel;
    private $activityLogger;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->categoryModel = new Category($conn);
        $this->activityLogger = new ActivityLogger($conn);
    }

    public function getAllCategories() {
        return $this->categoryModel->getAllCategories();
    }

    public function createCategory($name, $description) {
        try {
            if ($this->categoryModel->create($name, $description)) {
                $this->activityLogger->log(
                    $_SESSION['admin']['id'],
                    'create_category',
                    "Created new category: {$name}"
                );
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error creating category: " . $e->getMessage());
            return false;
        }
    }

    public function updateCategory($id, $name, $description) {
        try {
            if ($this->categoryModel->update($id, $name, $description)) {
                $this->activityLogger->log(
                    $_SESSION['admin']['id'],
                    'update_category',
                    "Updated category: {$name}"
                );
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }

    public function deleteCategory($id) {
        try {
            $category = $this->categoryModel->getById($id);
            if (!$category) {
                throw new Exception("Category not found");
            }

            if ($this->categoryModel->delete($id)) {
                $this->activityLogger->log(
                    $_SESSION['admin']['id'],
                    'delete_category',
                    "Deleted category: {$category['name']}"
                );
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CategoryController();
    $action = $_POST['action'] ?? '';
    $success = false;
    $message = '';

    try {
        switch ($action) {
            case 'create':
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $success = $controller->createCategory($name, $description);
                $message = $success ? 'Category created successfully' : 'Failed to create category';
                break;

            case 'update':
                $id = $_POST['id'] ?? '';
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $success = $controller->updateCategory($id, $name, $description);
                $message = $success ? 'Category updated successfully' : 'Failed to update category';
                break;

            case 'delete':
                $id = $_POST['id'] ?? '';
                $success = $controller->deleteCategory($id);
                $message = $success ? 'Category deleted successfully' : 'Failed to delete category';
                break;

            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
    }

    $redirectParam = $success ? 'success' : 'error';
    header("Location: ../../views/admin/categories.php?{$redirectParam}=" . urlencode($message));
    exit();
} 