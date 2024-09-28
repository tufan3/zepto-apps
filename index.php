<?php
/// start database connect///
$host = 'localhost';  
$dbname = 'zepto_apps';
$user = 'root';  
$password = '';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
/// end database connect///


// start File upload and delete part///
$response = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['font_file'])) {
        $file = $_FILES['font_file'];

        $allowed_extension = 'ttf';
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($file_extension === $allowed_extension) {
            $upload_dir = 'uploads/';
            $new_file_name = time() . '_' . basename($file['name']);
            $upload_file = $upload_dir . $new_file_name;

            if (move_uploaded_file($file['tmp_name'], $upload_file)) {
                $font_name = $conn->real_escape_string($new_file_name);
                $sql = "INSERT INTO uploaded_fonts (font_name) VALUES ('$font_name')";

                if ($conn->query($sql) === TRUE) {
                    $response = "<span style='background-color: #40E0D0; padding:5px;'>Font uploaded successfully.</span>";
                }
            }
        } else {
            $response = "<span style='background-color: #FFA07A; padding:5px;'>Please upload a TTF file only.</span>";
        }
    }
}

// delete the upload ttf file// 
if (isset($_GET['delete_ttf'])) {
    $delete_ttf_id = (int)$_GET['delete_ttf'];

    $query = "SELECT font_name FROM uploaded_fonts WHERE id = $delete_ttf_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $font_name = $row['font_name'];
        $font_path = 'uploads/' . $font_name;

        if (file_exists($font_path)) {
            unlink($font_path);
        }

        $delete_query = "DELETE FROM uploaded_fonts WHERE id = $delete_ttf_id";
        if ($conn->query($delete_query) === TRUE) {
            $response = "<span style='background-color: #FFA07A; padding:5px;'>Font deleted successfully.</span>";
        }
    } else {
        $response = "<span style='background-color: #FFA07A; padding:5px;'>Font not found.</span>";
    }
}
// end File upload and delete part///



/// start create group and font///
$response1 = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_title'])) {
    $group_title = $conn->real_escape_string($_POST['group_title']);
    $font_names = $_POST['font_name'];
    $font_styles = $_POST['font_style'];
    $specific_sizes = $_POST['specific_size'];
    $price_changes = $_POST['price_change'];

    if (isset($_GET['id'])) {
        $group_id = (int)$_GET['id'];
        $sql_update_group = "UPDATE font_groups SET group_title = '$group_title' WHERE id = $group_id";
        if ($conn->query($sql_update_group) === TRUE) {
            $conn->query("DELETE FROM group_fonts WHERE group_id = $group_id");

            for ($i = 0; $i < count($font_names); $i++) {
                $font_name = $conn->real_escape_string($font_names[$i]);
                $font_style = $conn->real_escape_string($font_styles[$i]);
                $specific_size = (int)$specific_sizes[$i];
                $price_change = (int)$price_changes[$i];

                $sql_insert_fonts = "INSERT INTO group_fonts (group_id, font_name, font_style, specific_size, price_change) 
                                     VALUES ('$group_id', '$font_name', '$font_style', '$specific_size', '$price_change')";
                $conn->query($sql_insert_fonts);
            }
            $response1 = "<span style='background-color: #40E0D0; padding:5px;'>Font group updated successfully.</span>";
        } else {
            $response1 = "<span style='background-color: #FFA07A; padding:5px;'>Error updating font group: </span>" . $conn->error;
        }
    } else {
        $sql_create_group = "INSERT INTO font_groups (group_title) VALUES ('$group_title')";
        if ($conn->query($sql_create_group) === TRUE) {
            $group_id = $conn->insert_id;

            for ($i = 0; $i < count($font_names); $i++) {
                $font_name = $conn->real_escape_string($font_names[$i]);
                $font_style = $conn->real_escape_string($font_styles[$i]);
                $specific_size = (int)$specific_sizes[$i];
                $price_change = (int)$price_changes[$i];

                $sql_insert_fonts = "INSERT INTO group_fonts (group_id, font_name, font_style, specific_size, price_change) 
                                     VALUES ('$group_id', '$font_name', '$font_style', '$specific_size', '$price_change')";
                $conn->query($sql_insert_fonts);
            }
            $response1 = "<span style='background-color: #40E0D0; padding:5px;'>Font group created successfully.</span>";
        } else {
            $response1 = "<span style='background-color: #FFA07A; padding:5px;'>Error creating font group: </span>" . $conn->error;
        }
    }
}
/// end create group and font///


// start delete group and font// 
if (isset($_GET['delete_group_id'])) {
    $group_id = (int)$_GET['delete_group_id'];

    $delete_group_sql = "DELETE FROM font_groups WHERE id = $group_id";
    $delete_group_sql1 = "DELETE FROM group_fonts WHERE group_id = $group_id";

    $delete_group_result = $conn->query($delete_group_sql);
    $delete_group_fonts_result = $conn->query($delete_group_sql1);

    if ($delete_group_result && $delete_group_fonts_result) {
        $response1 = "<span style='background-color: #FFA07A; padding:5px;'>Font Group and Fonts deleted successfully</span>";
    } else {
        $response1 = "<span style='background-color: #FFA07A; padding:5px;'>Error: </span>" . $conn->error;
    }

}
// end delete group and font//


//update group and font//
$group = null;
$fonts = [];

if (isset($_GET['id'])) {
    $group_id = (int)$_GET['id'];

    $sql_group = "SELECT * FROM font_groups WHERE id = $group_id";
    $result_group = $conn->query($sql_group);

    if ($result_group->num_rows > 0) {
        $group = $result_group->fetch_assoc();

        $sql_fonts = "SELECT * FROM group_fonts WHERE group_id = $group_id";
        $result_fonts = $conn->query($sql_fonts);

        while ($row_font = $result_fonts->fetch_assoc()) {
            $fonts[] = $row_font;
        }
    }
}
//end update group and font//
?>


<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<style>
    .upload-area {
        border: 2px dashed #ccc;
        border-radius: 5px;
        text-align: center;
        padding: 20px;
        margin-top: 5px;
    }

    .upload-area:hover {
        background-color: #f9f9f9;
    }
</style>


<div class="main-container" style="margin: 50px 50px 0 50px;">
    <div class="row">
        <!-- Left side Upload ttf -->
        <div class="col-md-4">
            <div class="upload-area" id="upload_file">
                <p>Click to upload or drag and drop</p>
                <p><strong>Only TTF File Allowed</strong></p>
                <form id="upload_form" enctype="multipart/form-data" method="POST">
                    <input type="file" id="font_file" name="font_file" accept=".ttf" style="display:none;">
                </form>
            </div>
            <div class="table-container">
                <h2>Our Fonts</h2>
                <p id="response_message"><?php echo $response; ?></p>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Font Name</th>
                            <th>Preview</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="fontList">
                        <?php
                        $result = $conn->query("SELECT font_name,id FROM uploaded_fonts ORDER BY id DESC");

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $font_name = $row['font_name'];
                                $delete_id = $row['id'];
                                $fontUrl = 'uploads/' . $font_name;

                                echo "<tr>";
                                echo "<td>$font_name</td>";
                                echo "<td class='preview' style='font-family: \"Uploaded_font_$font_name\";'>Example Style</td>";
                                // echo "<td><a class='delete' href='#' data-id='$delete_id'>Delete</a></td>";
                                echo "<td><a class='' href='?delete_ttf=" . $delete_id . "'>Delete</a></td>";
                                echo "</tr>";

                                echo "<style>
                                    @font-face {
                                        font-family: 'Uploaded_font_$font_name';
                                        src: url('$fontUrl') format('truetype');
                                    }
                                </style>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No fonts uploaded yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
         <!-- Left side Upload ttf -->

        <!-- Right side Font font group -->
        <div class="col-md-8">
            <h2><?php echo isset($group) ? 'Edit Font Group' : 'Create Font Group'; ?></h2>
            <form id="font_group_form" action="" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" id="group_title" name="group_title" placeholder="Group Title"
                        value="<?php echo isset($group) ? $group['group_title'] : ''; ?>" required>
                </div>
                <div id="font_group_container">
                    <?php if (!empty($fonts)){ ?>
                        <?php foreach ($fonts as $font){ ?>
                            <div class="form-row mb-2 font-row" style="box-shadow: black 2px;">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="font_name[]" placeholder="Font Name"
                                        value="<?php echo $font['font_name']; ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" name="font_style[]" required>
                                        <option value="">Select a Font</option>
                                        <option value="Arial" <?php echo ($font['font_style'] == 'Arial') ? 'selected' : ''; ?>>Arial</option>
                                        <option value="Times New Roman" <?php echo ($font['font_style'] == 'Times New Roman') ? 'selected' : ''; ?>>Times New Roman</option>
                                        <option value="Helvetica" <?php echo ($font['font_style'] == 'Helvetica') ? 'selected' : ''; ?>>Helvetica</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" name="specific_size[]" placeholder="Specific Size"
                                        value="<?php echo $font['specific_size']; ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control" name="price_change[]" placeholder="Price Change"
                                        value="<?php echo $font['price_change']; ?>" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger remove-row">X</button>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else{ ?>
                        <div class="form-row mb-2 font-row" style="box-shadow: black 2px;">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="font_name[]" placeholder="Font Name" required>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" name="font_style[]" required>
                                    <option value="">Select a Font</option>
                                    <option value="Arial">Arial</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Helvetica">Helvetica</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="specific_size[]" placeholder="Specific Size" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="price_change[]" placeholder="Price Change" required>
                            </div>

                        </div>
                    <?php } ?>
                </div>
                <button type="button" class="btn btn-secondary" id="add_row">+ Add Row</button>
                <button style="float: right; margin-right: 10px;" type="submit" class="btn btn-primary"><?php echo isset($group) ? 'Save Changes' : 'Create'; ?></button>
            </form>
            <?php
            $sql_group = "SELECT fg.id as id, fg.group_title, GROUP_CONCAT(fgf.font_style SEPARATOR ', ') AS fonts, COUNT(fgf.font_style) as count FROM font_groups fg JOIN group_fonts fgf ON fg.id = fgf.group_id GROUP BY fg.id";
            $result = $conn->query($sql_group);
            ?>
            <div class="container table-container">
                <h2>Our Fonts</h2>
                <span id="response_message1"><?php echo $response1; ?></span>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Group Name</th>
                            <th>Fonts</th>
                            <th>Count</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="group_list">
                        <?php if ($result->num_rows > 0) { ?>
                            <?php while ($row_group = $result->fetch_assoc()) {
                                $delete_group_id = $row_group['id'];
                            ?>
                                <tr>
                                    <td><?php echo $row_group['group_title']; ?></td>
                                    <td><?php echo $row_group['fonts']; ?></td>
                                    <td><?php echo $row_group['count']; ?></td>
                                    <td>
                                        <a href="?id=<?php echo $row_group['id']; ?>" class="btn btn-sm btn-primary">Edit</a>

                                        <!-- <a href="#" class="btn btn-sm btn-primary edit_group_list" data-id='<?php echo $row_group['id']; ?>'>Edit</a> -->

                                        <!-- <a class='btn btn-sm btn-danger delete_group_list' href='#' data-id='<?php echo $delete_group_id; ?>'>Delete</a> -->
                                        <a class='btn btn-sm btn-danger' href='?delete_group_id=<?php echo $row_group['id']; ?>'>Delete</a>

                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4">No font groups found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Right side Font font group -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
    $(document).ready(function() {
        const upload_container = document.getElementById('upload_file');
        const file_input = document.getElementById('font_file');
        const form = document.getElementById('upload_form');
        const response_message = document.getElementById('response_message');
        var response_message1 = document.getElementById('response_message1');

        ///ttf file upload///
        upload_container.addEventListener('click', () => file_input.click());

        file_input.addEventListener('change', (e) => {
            form.submit();
        });
         ///ttf file upload///


         ///add new row in font group///
        $('#add_row').click(function() {
            let newRow = `
            <div class="form-row mb-2 font-row" style="box-shadow: black 2px;">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="font_name[]" placeholder="Font Name" required>
                </div>
                <div class="col-md-3">
                    <select class="form-control" name="font_style[]" required>
                        <option value="">Select a Font</option>
                        <option value="Arial">Arial</option>
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Helvetica">Helvetica</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="specific_size[]" placeholder="Specific Size" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="price_change[]" placeholder="Price Change" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-row">X</button>
                </div>
            </div>`;
            $('#font_group_container').append(newRow);
        });
        ///add new row in font group///
        
        ///remove row in font group///
        $(document).on('click', '.remove-row', function() {
            $(this).closest('.font-row').remove();
        });
        ///remove row in font group///


        // validation at least two font rows//
        $('#font_group_form').on('submit', function(e) {
            let font_rows = $('#font_group_container .font-row').length;

            if (font_rows < 2) {
                e.preventDefault();
                alert('You must select at least two fonts to create a group.');
            }
        });
        // validation at least two font rows//


    });
</script>

<script>
    $(document).ready(function() {
        $('#font_group_form').on('submit', function(e) {
            e.preventDefault(); 
            
            let form_data = $(this).serialize(); 
            let form_action = $(this).attr('action');
            
            $.ajax({
                type: 'POST',
                url: form_action, 
                data: form_data,
                success: function(response) {

                    $('#font_group_form')[0].reset();
                    
                    $('#group_title').val('');  

                    $('#font_group_container').html(`
                        <div class="form-row mb-2 font-row" style="box-shadow: black 2px;">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="font_name[]" placeholder="Font Name" required>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" name="font_style[]" required>
                                    <option value="">Select a Font</option>
                                    <option value="Arial">Arial</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Helvetica">Helvetica</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="specific_size[]" placeholder="Specific Size" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="price_change[]" placeholder="Price Change" required>
                            </div>
                        </div>
                    `);
                    
                    $('#response_message1').html("<span style='background-color: #40E0D0; padding:5px;'>Font group update successfully.</span>");

                   // $('#font_group_form').trigger("reset");
                    $('#group_list').html($(response).find('#group_list').html());

                },
                error: function() {
                    $('#response_message1').html("<span style='background-color: #FFA07A; padding:5px;'>Error occurred while saving font group.</span>");
                }
            });
        });
    });
</script>
