<?php

class BlogManager       // Class created
{
    public $con;                      // Properties
    public $categories = [];
    public $blogs = [];
    public $id;
    public $old_title;
    public $old_section;
    public $old_image;
    public $old_category = [];
    public $titleErr = '';
    public $sectionErr = '';
    public $categoryErr = '';
    public $fileErr = '';
    private $isValid = true;
    public $title = '';
    public $section = '';
    public $file = '';
    public $currentCategoryId;
    public $parentCategories = '';

    public function __construct($con, $id = null)    //Constructor
    {
        $this->con = $con;
        $this->id = $id;
        $this->fetchCategories();
        $this->fetchBlogs();
        if ($this->id) {
            $this->OldBlog();
            $this->OldCategory();
        }
    }
    public function fetchCategories() // Fetching categories from the database
    {
        $sql = "WITH RECURSIVE category_hierarchy AS (
            SELECT id, category_name, parent_id, 0 AS level
            FROM categories
            WHERE parent_id IS NULL
            UNION ALL
            SELECT c.id, c.category_name, c.parent_id, ch.level + 1 AS level
            FROM categories c
            INNER JOIN category_hierarchy ch ON c.parent_id = ch.id
        )
        SELECT id, category_name, parent_id, level FROM category_hierarchy ORDER BY id";

        $categoryResult = mysqli_query($this->con, $sql);
        if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
            while ($row = mysqli_fetch_assoc($categoryResult)) {

                if (isset($row['id']) && isset($row['category_name'])) {
                    $this->categories[$row['id']] = [
                        'name' => $row['category_name'],
                        'parent_id' => $row['parent_id'] ?? null,
                        'level' => $row['level']
                    ];
                }
            }
        }
    }
    // Edit the categories
    public function fetch_edit_Categories($parent_id = null)
    {
        $categories = [];
        $query = $parent_id === null
            ? "SELECT * FROM `categories` WHERE parent_id IS NULL"
            : "SELECT * FROM `categories` WHERE parent_id = '{$parent_id}'";

        $result = mysqli_query($this->con, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = $row;
            }
        }
        return $categories;
    }


    public function OldBlog()              //Fetching old Blog data
    {
        $sql = "SELECT * FROM `blog` WHERE id = '{$this->id}'";
        $result = mysqli_query($this->con, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $this->old_title = $row['title'];
            $this->old_section = $row['description'];
            $this->old_image = $row['image'];
        }
    }


    public function OldCategory()          //Fetching old category
    {
        $category_sql = "SELECT category_id FROM blog_category_map WHERE blog_id = '{$this->id}'";
        $category_result = mysqli_query($this->con, $category_sql);
        while ($cat_row = mysqli_fetch_assoc($category_result)) {
            $this->old_category[] = $cat_row['category_id'];
        }
    }

    public function old_fetchCategories($parent_id = null)
    {
        $categories = [];
        $query = $parent_id === null
            ? "SELECT * FROM `categories` WHERE parent_id IS NULL"
            : "SELECT * FROM `categories` WHERE parent_id = '{$parent_id}'";

        $result = mysqli_query($this->con, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = $row;
            }
        }
        return $categories;
    }


    public function fetchBlogs()       //Fetching blogs
    {
        $sql = "SELECT b.id, b.title, b.description, b.image, 
                GROUP_CONCAT(bcm.category_id) AS category_ids
                FROM blog b
                LEFT JOIN blog_category_map bcm ON b.id = bcm.blog_id
                GROUP BY b.id";
        $blogResult = mysqli_query($this->con, $sql);
        if ($blogResult && mysqli_num_rows($blogResult) > 0) {
            while ($blog = mysqli_fetch_assoc($blogResult)) {
                $blog['category_ids'] = explode(',', $blog['category_ids'] ?? '');
                $this->blogs[] = $blog;
            }
        }
    }

    public function displayCategories($categoryIds)      //Display categories
    {
        $categoryStrings = [];
        foreach ($categoryIds as $categoryId) {
            if (isset($this->categories[$categoryId])) {
                $prefix = str_repeat('-', $this->categories[$categoryId]['level']);
                $categoryStrings[] = $prefix . ' ' . $this->categories[$categoryId]['name'];
            }
        }
        return !empty($categoryStrings) ? implode(', ', $categoryStrings) : 'Un categorized';
    }

    //Display Category in checkbox
    public function displayCategoryCheckboxes($categories, $parentId = null, $indentationLevel = 0, $selectedCategories = [])
    {
        foreach ($categories as $categoryId => $category) {
            // Display categories only if their parent matches 
            if ($category['parent_id'] == $parentId) {
                $isChecked = in_array($categoryId, $selectedCategories) ? 'checked' : '';
                echo '<div style="margin-left: ' . ($indentationLevel * 20) . 'px;">';
                echo '<input type="checkbox" name="categories[]" value="' . $categoryId . '" ' . $isChecked . '> ';
                echo '<label>' . $category['name'] . '</label>';

                // Recursively display children categories
                $this->displayCategoryCheckboxes($categories, $categoryId, $indentationLevel + 1, $selectedCategories);

                echo '</div>';
            }
        }
    }

    // Validation in blogs
    public function validateAndSave($postData, $fileData)
    {
        $this->validateTitle($postData['title']);
        $this->validateSection($postData['section']);
        $selectedCategories = $postData['categories'] ?? [];
        $this->validateCategories($selectedCategories);
        $this->validateFile($fileData['image']);
        if ($this->isValid) {
            $this->saveBlog($postData['title'], $postData['section'], $selectedCategories);
        }
    }

    private function validateTitle($title)
    {
        if (empty($title)) {
            $this->titleErr = 'Title cannot be empty';
            $this->isValid = false;
        } else {
            $this->title = $title;
            if (!preg_match("/^[A-Za-z0-9 ]+$/", $title)) {
                $this->titleErr = 'Title name not valid';
                $this->isValid = false;
            } else {
                $sql = "SELECT * FROM `blog` WHERE title = '$title'";
                $result = mysqli_query($this->con, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $this->titleErr = 'Cannot have the same title name';
                    $this->isValid = false;
                }
            }
        }
    }

    private function validateSection($section)
    {
        if (empty($section)) {
            $this->sectionErr = 'Section cannot be empty';
            $this->isValid = false;
        } else {
            $this->section = $section;
        }
    }

    private function validateCategories($selectedCategories)
    {
        if (empty($selectedCategories)) {
            $this->categoryErr = 'Please select at least one category';
            $this->isValid = false;
        }
    }

    private function validateFile($file)
    {
        if (empty($file['size'])) {
            $this->fileErr = 'Image cannot be empty';
            $this->isValid = false;
        } else if (isset($file) && $file['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
            $file_type = mime_content_type($file['tmp_name']);
            $file_tmp = $file['tmp_name'];
            $file_name = time() . "_" . $file['name'];
            $file_destination = "../images/" . $file_name;

            if ($file['size'] > $maxFileSize) {
                $this->fileErr = 'File is too large. Maximum file size is 5MB.';
                $this->isValid = false;
            } elseif (!in_array($file_type, $allowed_types)) {
                $this->fileErr = 'Only upload JPEG/PNG files';
                $this->isValid = false;
            } else {
                if (move_uploaded_file($file_tmp, $file_destination)) {
                    $this->file = $file_name;
                } else {
                    $this->fileErr = 'Failed to upload image.';
                    $this->isValid = false;
                }
            }
        }
    }

    private function saveBlog($title, $section, $selectedCategories)
    {
        $query = "INSERT INTO blog (title, description, image) VALUES ('$title', '$section', '{$this->file}')";
        if (mysqli_query($this->con, $query)) {
            $blogId = mysqli_insert_id($this->con);

            foreach ($selectedCategories as $categoryId) {
                $mapQuery = "INSERT INTO blog_category_map (blog_id, category_id) VALUES ($blogId, $categoryId)";
                mysqli_query($this->con, $mapQuery);
            }

            header('Location: blog_admin.php');
            exit();
        }
    }
    //Update the blog
    public function UpdateBlog($postData, $fileData)
    {
        $title = $postData['title'];
        $section = $postData['section'];
        $selected_categories = isset($postData['categories']) ? $postData['categories'] : [];
        $category_string = implode(',', $selected_categories);

        if (!empty($fileData['image']['name'])) {
            $file_name = $fileData['image']['name'];
            $file_temp = $fileData['image']['tmp_name'];
            $file_destination = "../images/" . $file_name;
            move_uploaded_file($file_temp, $file_destination);
        } else {
            $file_name = $this->old_image;
        }

        $update_sql = "UPDATE `blog` 
                       SET title = '$title', description = '$section', image = '$file_name' 
                       WHERE id = '{$this->id}'";
        $update_result = mysqli_query($this->con, $update_sql);
        if ($update_result) {
            $delete_sql = "DELETE FROM blog_category_map WHERE blog_id = '{$this->id}'";
            mysqli_query($this->con, $delete_sql);

            foreach ($selected_categories as $categoryId) {
                $query = "INSERT INTO blog_category_map (blog_id, category_id) VALUES ('{$this->id}', '$categoryId')";
                if (!mysqli_query($this->con, $query)) {
                    echo "Error: " . mysqli_error($this->con);
                }
            }

            header('Location: blog_admin.php');
            exit();
        } else {
            die("Failed to update blog: " . mysqli_error($this->con));
        }
    }

    //Fetch data for edit
    public function fetchBlogDataForEdit()
    {
        $sql = "SELECT * FROM `blog` WHERE id = '{$this->id}'";
        $result = mysqli_query($this->con, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $blogData = [
                'title' => $row['title'],
                'description' => $row['description'],
                'image' => $row['image'],
            ];

            $category_sql = "SELECT category_id FROM blog_category_map WHERE blog_id = '{$this->id}'";
            $category_result = mysqli_query($this->con, $category_sql);

            $categories = [];
            while ($cat_row = mysqli_fetch_assoc($category_result)) {
                $categories[] = $cat_row['category_id'];
            }

            $blogData['categories'] = $categories ?: [];  // Ensure it's always an array

            return $blogData;
        } else {
            return null;
        }
    }

    //Display Category in user side
    public function display_categories_user($parent_id = Null, $indent = "")
    {
        $query = "SELECT * FROM categories WHERE parent_id " . ($parent_id ? "= $parent_id" : "IS NULL") . " ORDER BY category_name"; //Ternary operator
        $result = mysqli_query($this->con, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($category = mysqli_fetch_assoc($result)) {
                echo $indent . $category['category_name'] . "<br>";

                // Recursive call to display subcategories with an additional indent
                $this->display_categories_user($category['id'], $this->con, $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
            }
        }
    }
    // Showing data with indentation
    public function getCategoryIndent($parent_id)
    {
        $indent = "";
        $level = 0;

        while ($parent_id) {
            $query = "SELECT parent_id FROM categories WHERE id = $parent_id";
            $result = mysqli_query($this->con, $query);
            $category = mysqli_fetch_assoc($result);

            if ($category) {
                $parent_id = $category['parent_id'];
                $level++;
            } else {
                break;
            }
        }

        //Loop for 
        for ($i = 0; $i < $level; $i++) {
            $indent .= "-";
        }

        return $indent;
    }
    //Display blog on user side
    public function displayBlog()
    {
        $sql = "SELECT * FROM `blog`";
        $result = mysqli_query($this->con, $sql);

        if ($result) {
            while ($rows = mysqli_fetch_assoc($result)) {
                $id = $rows['id'];
                $title = $rows['title'];
                $section = $rows['description'];
                $image = $rows['image'];
                $category = $rows['category'];

                echo "<div class='blog-entry'>";
                echo "<h2>Blog ID: $id</h2>";
                echo "<h4>Title:</h4> $title <br>";
                echo "<h4>Section:</h4> $section <br>";
                echo "<h4>Image:</h4> <img src='../images/$image' alt='Image'><br>";

                // Fetch the categories  with this blog
                echo "<h4>Categories:</h4>";
                $category_sql = "SELECT c.category_name, c.parent_id FROM blog_category_map bcm 
                                 JOIN categories c ON bcm.category_id = c.id 
                                 WHERE bcm.blog_id = $id";
                $category_result = mysqli_query($this->con, $category_sql);

                if (mysqli_num_rows($category_result) > 0) {
                    while ($category_row = mysqli_fetch_assoc($category_result)) {
                        $category_name = $category_row['category_name'];
                        $parent_id = $category_row['parent_id'];

                        // Indentation based on the parent_id
                        $indent = $this->getCategoryIndent($parent_id, $this->con);
                        echo $indent . $category_name . "<br>";
                    }
                } else {
                    echo "No categories found for this blog.<br>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No blogs available.</p>";
        }
    }
    // Display categories in a hierarchical structure
    public function display_Public_Categories($categories, $currentCategoryId, $parentCategories)
    {
        echo '<h3>';
        foreach ($parentCategories as $parentId) {
            $parentCategory = $this->findCategoryById($categories, $parentId);
            if ($parentCategory) {
                echo '<a href="blog_public.php?category_id=' . $parentCategory['id'] . '">' . $parentCategory['category_name'] . '</a> -> ';
            }
        }
        $currentCategory = $this->findCategoryById($categories, $currentCategoryId);
        if ($currentCategory) {
            echo $currentCategory['category_name'];
        }
        echo '</h3>';

        if (isset($categories[$currentCategoryId])) {
            echo '<ol>';
            foreach ($categories[$currentCategoryId] as $subcategory) {
                echo '<li>';
                echo '<a href="blog_public.php?category_id=' . $subcategory['id'] . '">' . $subcategory['category_name'] . '</a>';
                echo '</li>';
            }
            echo '</ol>';
        }
    }


    // Helper to find category with ID

    // Get category by ID
    public function findCategoryById($categories, $categoryId)
    {
        foreach ($categories as $parentId => $categoryList) {
            foreach ($categoryList as $category) {
                if ($category['id'] == $categoryId) {
                    return $category;
                }
            }
        }
        return null;
    }
    // Fetch blogs by category ID
    public function getBlogsByCategory($categoryId)
    {
        $sql = "SELECT b.id, b.title, b.description, b.image 
                FROM blog b 
                INNER JOIN blog_category_map bcm 
                ON b.id = bcm.blog_id 
                WHERE bcm.category_id = $categoryId";
        return $this->con->query($sql);
    }

    // Find Parent categories in Blog Public 
    function findParentCategories($categories, $currentCategoryId)
    {
        $parents = [];
        while ($currentCategoryId) {
            $parentId = $this->findParentCategory($categories, $currentCategoryId);
            if ($parentId) {
                array_unshift($parents, $parentId);
                $currentCategoryId = $parentId;
            } else {
                break;
            }
        }
        return $parents;
    }

    function findParentCategory($categories, $currentCategoryId)   //Find single parent category
    {
        foreach ($categories as $parentId => $categoryList) {
            foreach ($categoryList as $category) {
                if ($category['id'] == $currentCategoryId) {
                    return $parentId;
                }
            }
        }
        return null;
    }

    public function getAllCategories()
    {
        $sql = "SELECT * FROM `categories` ORDER BY category_name";
        return $this->con->query($sql);
    }
}


$blogManager = new BlogManager($db->con);
