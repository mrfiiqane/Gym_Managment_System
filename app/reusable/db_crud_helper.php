
<?php
// Generic Functions waana clean code

// qeybta Normal CRUD

//Insert
function create($conn, $table, $data) {
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $types = str_repeat("s", count($data)); 
    $values = array_values($data);

    $stmt = $conn->prepare("INSERT INTO `$table` ($columns) VALUES ($placeholders)");
    $stmt->bind_param($types, ...$values);
    return $stmt->execute();
}

//Read All
function db_read_all($conn, $table, $extra_sql = "") {
    $sql = "SELECT * FROM `$table` $extra_sql";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
function db_read($conn, $table, $extra_sql = "") {
    $sql = "SELECT * FROM `$table` $extra_sql";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

//Update
function update($conn, $table, $data, $id, $id_col = 'id') {
    $fields = "";
    foreach ($data as $key => $value) { $fields .= "$key = ?, "; }
    $fields = rtrim($fields, ", ");

    $types = str_repeat("s", count($data)) . "s";
    $values = array_values($data);
    $values[] = $id;

    $stmt = $conn->prepare("UPDATE `$table` SET $fields WHERE $id_col = ?");
    $stmt->bind_param($types, ...$values);
    return $stmt->execute();
}

//Delete
function delete($conn, $table, $id, $id_col = 'id') {
    $stmt = $conn->prepare("DELETE FROM `$table` WHERE $id_col = ?");
    $stmt->bind_param("s", $id);
    return $stmt->execute();
}


// qeybta crud SP 

// Insert SP
function create_sp($conn, $sp_name, $data) {
    if (empty($data)) return false;
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $types = str_repeat("s", count($data)); 
    $values = array_values($data);

    $stmt = $conn->prepare("CALL `$sp_name`($placeholders)");
    if ($stmt) {
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $stmt->close();
        
        // Clear remaining results
        while ($conn->more_results() && $conn->next_result()) {
            if ($res = $conn->store_result()) {
                $res->free();
            }
        }
        return $result;
    }
    return false;
}

// Read All SP
function db_read_all_sp($conn, $sp_name, $data = []) {
    if (empty($data)) {
        $result = $conn->query("CALL `$sp_name`()");
        $data_result = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        if ($result && !is_bool($result)) {
             $result->free();
        }
        
        // Clear remaining results
        while ($conn->more_results() && $conn->next_result()) {
            if ($res = $conn->store_result()) {
                $res->free();
            }
        }
        return $data_result;
    } else {
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $types = str_repeat("s", count($data)); 
        $values = array_values($data);

        $stmt = $conn->prepare("CALL `$sp_name`($placeholders)");
        if ($stmt) {
            $stmt->bind_param($types, ...$values);
            $stmt->execute();
            $result = $stmt->get_result();
            $data_result = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            if ($result && !is_bool($result)) {
                 $result->free();
            }
            $stmt->close();
            
            // Clear remaining results
            while ($conn->more_results() && $conn->next_result()) {
                if ($res = $conn->store_result()) {
                    $res->free();
                }
            }
            return $data_result;
        }
        return [];
    }
}

// Update SP
function update_sp($conn, $sp_name, $data) {
    if (empty($data)) return false;
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $types = str_repeat("s", count($data)); 
    $values = array_values($data);

    $stmt = $conn->prepare("CALL `$sp_name`($placeholders)");
    if ($stmt) {
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $stmt->close();
        
        // Clear remaining results
        while ($conn->more_results() && $conn->next_result()) {
            if ($res = $conn->store_result()) {
                $res->free();
            }
        }
        return $result;
    }
    return false;
}

// Delete SP
function delete_sp($conn, $sp_name, $id) {
    $stmt = $conn->prepare("CALL `$sp_name`(?)");
    if ($stmt) {
        $stmt->bind_param("s", $id);
        $result = $stmt->execute();
        $stmt->close();
        
        // Clear remaining results
        while ($conn->more_results() && $conn->next_result()) {
            if ($res = $conn->store_result()) {
                $res->free();
            }
        }
        return $result;
    }
    return false;
}

