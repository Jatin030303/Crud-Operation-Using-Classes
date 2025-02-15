<?php

class CategoryManager      // Class name
{
    private $con;                 //Properties
    public $categories = [];
    public $error  = '';
    public $category_name = '';
    public $description = '';
    public $parent_id = null;
    public $isValid = true;
    public $id = null;

    public function __construct($con, $id = null) //construct
    {
        $this->con = $con;
        $this->id = $id;
        if ($id) {
            $this->fetchData();
        }
        $this->fetchCategories();
    }

    // Fetch categories
    private function fetchCategories()
    {
        $sql = "SELECT id, category_name, parent_id, description, created_at, updated_at FROM categories ORDER BY category_name";
        $result = mysqli_query($this->con, $sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $this->categories[$row['parent_id']][] = $row;
            }
        } else {
            die(mysqli_error($this->con));
        }
    }

    // Fetch category from id 
    private function fetchData()
    {
        $sql = "SELECT * FROM categories WHERE id = '$this->id'";
        $result = mysqli_query($this->con, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $category = mysqli_fetch_assoc($result);
            $this->category_name = $category['category_name'];
            $this->description = $category['description'];
            $this->parent_id = $category['parent_id'];
        } else {
            die("Category not found");
        }
    }

    //Validation for category
    public function validateCategoryData($postData)
    {
        $this->category_name = trim($postData['category_name'] ?? '');
        $this->description = trim($postData['description'] ?? '');
        $this->parent_id = $postData['parent_id'] ?: null;

        // Validate category name
        if (empty($this->category_name)) {
            $this->error = 'Category name is required.';
            $this->isValid = false;
        } elseif (!preg_match("/^[A-Za-z ]+$/", $this->category_name)) {
            $this->error = 'Invalid characters in category name.';
            $this->isValid = false;
        }
        if ($this->id && $this->parent_id == $this->id) {
            $this->error = 'A category cannot be its own parent.';
            $this->isValid = false;
        } elseif ($this->parent_id && !$this->isParentCategoryValid($this->parent_id)) {
            $this->error = 'Invalid parent category or circular relationship detected.';
            $this->isValid = false;
        }

        // Check  duplication
        if ($this->isValid) {
            $this->checkSiblingDuplication(); //function call
        }

        // add or update category
        if ($this->isValid) {
            if ($this->id) {
                $this->updateCategory();
            } else {
                $this->addCategory();
            }
        }
    }

    // Display category to show on user side
    public function display_users($parentId = null, $level = 0, &$sno = 1)
    {
        if (isset($this->categories[$parentId])) {
            foreach ($this->categories[$parentId] as $category) {
                echo '<tr>';
                if ($level === 0) {
                    echo '<td>' . $sno++ . '</td>';
                } else {
                    echo '<td></td>';
                }
                echo '<td>' . str_repeat('-', $level) . $category['category_name'] . '</td>';
                echo '<td>' . htmlspecialchars($category['description'] ?: '') . '</td>';
                echo '</tr>';
                $this->display_users($category['category_id'], $level + 1, $sno);
            }
        }
    }

    private function isParentCategoryValid($parentId)
    {
        $currentId = $this->id;
        while ($parentId) {
            if ($parentId == $currentId) {
                return false;
            }
            $sql = "SELECT parent_id FROM categories WHERE id = '$parentId'";
            $result = mysqli_query($this->con, $sql);
            if ($result && $row = mysqli_fetch_assoc($result)) {
                $parentId = $row['parent_id'];
            } else {
                break;
            }
        }
        return true;
    }
    //Check sub category duplication
    private function checkSiblingDuplication()
    {
        $sql = "SELECT id FROM categories WHERE category_name = '$this->category_name' AND parent_id " .
            ($this->parent_id ? "= '$this->parent_id'" : "IS NULL") .
            ($this->id ? " AND id != '$this->id'" : "");
        $duplication_result = mysqli_query($this->con, $sql);

        if ($duplication_result && mysqli_num_rows($duplication_result) > 0) {
            $this->error = 'A category with the same name already exists under this parent.';
            $this->isValid = false;
        }
    }

    // Add new category
    private function addCategory()
    {
        $sql = "INSERT INTO categories (category_name, description, parent_id) VALUES ('$this->category_name', '$this->description', " .
            ($this->parent_id ? "'$this->parent_id'" : "NULL") . ")";
        if (mysqli_query($this->con, $sql)) {
            header("Location: category.php");
            exit();
        } else {
            die(mysqli_error($this->con));
        }
    }
    // Update the category
    private function updateCategory()
    {
        $parent_id = $this->parent_id ? "'$this->parent_id'" : "NULL";
        $sql = "UPDATE categories SET category_name = '$this->category_name', description = '$this->description', parent_id = $parent_id WHERE id = '$this->id'";
        if (mysqli_query($this->con, $sql)) {
            header("Location: category.php");
            exit();
        } else {
            die("Error updating category: " . mysqli_error($this->con));
        }
    }

    //Display category on admin side
    public function displayCategories($parentId = null, $level = 0, &$sno = 1)
    {
        if (isset($this->categories[$parentId])) {
            foreach ($this->categories[$parentId] as $category) {
                echo '<tr>';
                if ($level === 0) {
                    // Display the  number for parent  only
                    echo '<td>' . $sno++ . '</td>';
                } else {

                    echo '<td></td>';
                }
                echo '<td>' . str_repeat('--', $level) . $category['category_name'] . '</td>';
                echo '<td>' . ($category['description'] ?: '-') . '</td>';
                echo '<td>' . date("M, d Y h:i A", strtotime($category['created_at'])) . '</td>';
                echo '<td>' . date("M, d Y h:i A", strtotime($category['updated_at'])) . '</td>';
                echo '<td>
                <a href="edit.php?edit_id=' . $category['id'] . '">Edit</a> |
                <a href="delete.php?delete_id=' . $category['id'] . '" onclick="return confirm(\'Are you sure?\');">Delete</a>
            </td>';
                echo '</tr>';
                // Recursive display child categories
                $this->displayCategories($category['id'], $level + 1, $sno);
            }
        }
    }

    public function displayCategoriesWithSerialNumbers($parentId = null, $level = 0, &$sno = 1)
    {
        if (isset($this->categories[$parentId])) {
            foreach ($this->categories[$parentId] as $category) {
                echo '<tr>';
                if ($level === 0) {
                    echo '<td>' . $sno++ . '</td>'; // Displaying serial number
                } else {
                    echo '<td></td>';
                }
                echo '<td>' . str_repeat('--', $level) . $category['category_name'] . '</td>';
                echo '<td>' . ($category['description'] ?: '-') . '</td>';
                echo '</tr>';
                // Recursive display child categories
                $this->displayCategoriesWithSerialNumbers($category['id'], $level + 1, $sno);
            }
        }
    }


    public function displayCategoriesWithIndentation($parentId = null, $indent = '')
    {
        if (isset($this->categories[$parentId])) {
            foreach ($this->categories[$parentId] as $category) {
                echo '<option value="' . $category['id'] . '"' . ($this->parent_id == $category['id'] ? ' selected' : '') . '>' . $indent . $category['category_name'] . '</option>';
                $this->displayCategoriesWithIndentation($category['id'], $indent . '--');
            }
        }
    }

    // Display public listing categories
    function display_public_Categories($parentId, $categories)
    {
        if (isset($categories[$parentId])) {
            echo '<ol>';
            foreach ($categories[$parentId] as $category) {
                echo '<li>';
                echo '<h1>' . '<a href="blog_public.php?category_id=' . $category['id'] . '">' . $category['category_name'] . '</a>' . '</h1>';
                $this->display_public_Categories($category['id'], $categories);
                echo '</li>';
            }
            echo '</ol>';
        }
    }

    public function hasCategories()
    {
        return !empty($this->categories);
    }
}
$categoryManager = new CategoryManager($db->con); //Object created 
